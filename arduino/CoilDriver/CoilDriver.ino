/*
* Coil Driver Arduino Server
*
* Version 0.9.1, August 2016
*
* Sean Leavey
* s.leavey.1@research.gla.ac.uk
*
* https://github.com/SeanDS/arduino-coil-driver/
*/

// comment out for production mode (for greatly enhanced speed)
//#define DEBUG

#include <SPI.h>
#include <Ethernet.h>
#include <SD.h>
#include <EthernetUdp.h>
#include <ArduinoJson.h>

// PWM pins to use
unsigned int outputPins[8] = {2, 3, 5, 6, 7, 8, 9, 11};

// digital pins to use for inputs to report (e.g. for coil contact)
const int DIGITAL_INPUT_PIN_1 = 40;
const int DIGITAL_INPUT_PIN_2 = 41;

// default output level upon startup for pins without a saved value
const int DEFAULT_OUTPUT_VALUE = 128;

// the MAC address of the ethernet shield currently connected
byte mac[] = {0x90, 0xA2, 0xDA, 0x0F, 0xD2, 0x67};

// IP address of the controller, static
IPAddress ipAddressController(192, 168, 1, 1);

// fully qualified path to software on server
String path = "/path/to/server/";

//
// STOP EDITING
//

// software version
const char* SOFTWARE_VERSION = "0.9.1";

// slave select pins
const int SD_SS_PIN = 4;          // SD card slave select
const int ETHERNET_SS_PIN = 10;   // Ethernet link slave select

// string to hold mac address
String macString;

// IP address of this Arduino, determined by DHCP from server
IPAddress ipAddress;
String ipString;

// Ethernet server on port 80
EthernetServer server(80);

// number of defined pins in list
const int NUMBER_OF_OUTPUTS = (sizeof(outputPins) / sizeof(int));
// list of values for each pin
unsigned int pinValues[NUMBER_OF_OUTPUTS];

// the reply string
String reply;

// flag for presence of SD card (default false)
boolean sdcardPresent = false;

int PIN_MODE_SINGLE = 0;
int PIN_MODE_DUAL   = 1;

int TOGGLE_MODE_SNAP = 0;
int TOGGLE_MODE_RAMP = 1;

int MID_OUTPUT_LEVEL = 128;

// for counting main program loops, for periodic status reports
int loopCounter = 0;

// min/max output levels
int MIN_OUTPUT_LEVEL = 0;
int MAX_OUTPUT_LEVEL = 255;

// request error constants
const int REQUEST_ERROR_NONE                  = 0;
const int REQUEST_ERROR_INVALID_REQUEST       = 1;
const int REQUEST_ERROR_INVALID_JSON          = 2;
const int REQUEST_ERROR_NO_PIN_MODE           = 3;
const int REQUEST_ERROR_INVALID_PIN_MODE      = 4;
const int REQUEST_ERROR_NO_TOGGLE_MODE        = 5;
const int REQUEST_ERROR_INVALID_TOGGLE_MODE   = 6;
const int REQUEST_ERROR_NO_DELAY              = 7;
const int REQUEST_ERROR_INVALID_DELAY         = 8;
const int REQUEST_ERROR_NO_MAPPING            = 9;
const int REQUEST_ERROR_INVALID_MAPPING       = 10;
const int REQUEST_ERROR_NO_OVERLAP            = 11;
const int REQUEST_ERROR_INVALID_OVERLAP       = 12;
const int REQUEST_ERROR_NO_PIN                = 13;
const int REQUEST_ERROR_INVALID_PIN           = 14;
const int REQUEST_ERROR_NO_PIN_VALUE          = 15;
const int REQUEST_ERROR_INVALID_PIN_VALUE     = 16;

// error messages
String requestErrorMessages[17];

void setup()
{
  // create error messages
  requestErrorMessages[REQUEST_ERROR_NONE]                = "";
  requestErrorMessages[REQUEST_ERROR_INVALID_REQUEST]     = "Invalid request";
  requestErrorMessages[REQUEST_ERROR_INVALID_JSON]        = "Invalid JSON message";
  requestErrorMessages[REQUEST_ERROR_NO_PIN_MODE]         = "No pin mode specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_PIN_MODE]    = "Invalid pin mode";
  requestErrorMessages[REQUEST_ERROR_NO_TOGGLE_MODE]      = "No toggle mode specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_TOGGLE_MODE] = "Invalid toggle mode";
  requestErrorMessages[REQUEST_ERROR_NO_DELAY]            = "No delay specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_DELAY]       = "Invalid delay";
  requestErrorMessages[REQUEST_ERROR_NO_MAPPING]          = "No mapping specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_MAPPING]     = "Invalid mapping";
  requestErrorMessages[REQUEST_ERROR_NO_OVERLAP]          = "No overlap specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_OVERLAP]     = "Invalid overlap";
  requestErrorMessages[REQUEST_ERROR_NO_PIN]              = "No pin specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_PIN]         = "Invalid pin";
  requestErrorMessages[REQUEST_ERROR_NO_PIN_VALUE]        = "No pin value specified";
  requestErrorMessages[REQUEST_ERROR_INVALID_PIN_VALUE]   = "Invalid pin value";

#ifdef DEBUG
  // open serial communications and wait for port to open:
  Serial.begin(9600);
#endif

  // set analogue write resolution to 8-bits, which is the fundamental limit of the Due's PWM outputs
  analogWriteResolution(8);

  // set slave select pins to output (ethernet and SD card reader)
  //pinMode(SD_SS_PIN, OUTPUT); // weirdly, with this enabled, the program crashes, but it works without it being used
  pinMode(ETHERNET_SS_PIN, OUTPUT);

  // toggle SD card on, ethernet off
  switchSlaveSelect(true);

  if (!SD.begin(SD_SS_PIN)) {
    debugPrint("SD card initialisation failed.");

    // set outputs to default values (better than undefined values, which just output a floating voltage)
    for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
      snapToValue(outputPins[i], DEFAULT_OUTPUT_VALUE);
    }
  } else {
    debugPrint("SD card initialised!");

    // set card present
    sdcardPresent = true;

    // search for saved pin values on SD card
    restoreSavedOutputLevels();
  }

  // toggle ethernet shield on, SD card off
  switchSlaveSelect(false);

  // start server
  startServer();

  // convert IP address and mac into strings (for sending to the controller as part of status messages)
  ipString = ipToString(Ethernet.localIP());
  macString = macToString(mac);

  // initialise input pins
  pinMode(DIGITAL_INPUT_PIN_1, INPUT);
  pinMode(DIGITAL_INPUT_PIN_2, INPUT);
}

void loop()
{
  loopCounter++;

  if (loopCounter >= 666000) { // approximately 30s
    // send a status report to controller
    sendCheckIn();

    // reset loop counter
    loopCounter = 0;
  }

  // listen for incoming client
  EthernetClient client = server.available();

  if (client)
  {
    debugPrint("New client connection");

    // get request
    String request = getClientRequest(client);

    // handle the request
    handleRequest(request, client);

    // close the connection
    client.stop();

    debugPrint("Client disconnected");
  }
}

void debugPrint(String message) {
#ifdef DEBUG
  Serial.println(message);
#endif
}

String getClientRequest(EthernetClient client) {
  String request;

  // an http request ends with a blank line, so keep track of this.
  boolean currentLineIsBlank = true;

  while (client.connected())
  {
    if (client.available())
    {
      // read next character from client's request
      char c = client.read();

      // add this character to the request string
      request += c;

      // an end of line character on an otherwise blank line tells us the client's request has ended, so send a reply
      if (c == '\n' && currentLineIsBlank)
      {
        break;
      }

      // if we get this far, we've not reached the end of a request,
      // so check if we're at the end of a line instead
      if (c == '\n')
      {
        // we're starting a new line
        currentLineIsBlank = true;
      }
      else if (c != '\r')
      {
        // we've gotten a character on the current line
        currentLineIsBlank = false;
      }
    }
  }

  // give the web browser time to receive the data
  delay(1);

  return request;
}

void startServer() {
  debugPrint("Starting Ethernet server...");

  // start the ethernet connection and the server
  Ethernet.begin(mac);
  server.begin();

  debugPrint("Server started");

  // print server IP address
  debugPrint("IP address: " + ipToString(Ethernet.localIP()));
}

String ipToString(IPAddress ipAddress) {
  String ipString = String(ipAddress[0]);

  for (int i = 1; i < 4; i++) {
    ipString += "." + String(ipAddress[i]);
  }

  return ipString;
}

String macToString(byte mac[]) {
  macString = String(mac[0]);

  for (int i = 1; i < 6; i++) {
    macString += ":" + String(mac[i]);
  }

  return macString;
}

void switchSlaveSelect(boolean sdcard) {
  boolean sdPin;
  boolean ethernetPin;

  if (sdcard) {
    sdPin = LOW;
    ethernetPin = HIGH;
  } else {
    sdPin = HIGH;
    ethernetPin = LOW;
  }

  digitalWrite(SD_SS_PIN, sdPin);
  digitalWrite(ETHERNET_SS_PIN, ethernetPin);
}

void handleRequest(String request, EthernetClient client) {
  debugPrint("Request: " + request);

  /*
   * Types of request:
   *
   * GET /status      Returns a status message for this Arduino, with IP, MAC, etc.
   * GET /outputs     Returns a list of each of this Arduino's outputs and corresponding values
   * GET /toggle [PIN] [VALUE]  Toggles pin [PIN] output to value [VALUE]. [PIN] must be two characters
   *        long, so pin 2 should be specified as 02.
   */

  // parse the requested information
  if (request.indexOf("GET /status") > -1) {
    // send 200 OK header
    sendOkHeaders(client);
    
    // send status as JSON message
    sendStatus(client);
  } else if (request.indexOf("GET /outputs") > -1) {
    // send 200 OK header
    sendOkHeaders(client);
    
    // send outputs as JSON message
    sendOutputs(client);
  } else if (request.indexOf("GET /toggle") > -1) {
    int requestResult = handleToggleRequest(request);

    if (requestResult == REQUEST_ERROR_NONE) {
      // everything is ok
      sendOkHeaders(client);
        
      // send list of outputs and corresponding values
      sendOutputs(client);
    } else {
      // request error - send error header
      sendBadRequestHeaders(client);
      
      // send appropriate error message
      sendErrorMessage(client, requestResult);
    }
  } else {
    // the message wasn't recognised
    // send 404 headers
    sendNotFoundHeaders(client);
    
    // send message
    sendNotFoundMessage(client);
  }
}

void sendStatus(EthernetClient client) {
  JsonObject& root = getStatusJSON();

  root.printTo(client);
}

JsonObject& getStatusJSON() {
  StaticJsonBuffer<1024> jsonBuffer;
  JsonObject& root = jsonBuffer.createObject();

  // set type
  root.set("type", "status");

  if (sdcardPresent) {
    root["sdcard"] = "present";
  } else {
    root["sdcard"] = "vacant";
  }

  root["mac"] = macString;
  root["ip"] = ipString;
  root["version"] = SOFTWARE_VERSION;
  root["digital_input_1"] = digitalRead(DIGITAL_INPUT_PIN_1);
  root["digital_input_2"] = digitalRead(DIGITAL_INPUT_PIN_2);
  
  return root;
}

void sendOutputs(EthernetClient client) {
  JsonObject& root = getOutputsJSON();

  root.printTo(client);
}

JsonObject& getOutputsJSON() {
  StaticJsonBuffer<1024> jsonBuffer;
  
  JsonObject& root = jsonBuffer.createObject();

  // set type
  root.set("type", "outputs");

  // create list of key names
  String keyNames[NUMBER_OF_OUTPUTS];

  for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
    keyNames[i] = String("pin_") + String(outputPins[i]);
  }

  // now add these key names to the JSON array with their values
  // this has to be done this way because of https://github.com/bblanchon/ArduinoJson/issues/84
  for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
    root.set(keyNames[i], pinValues[i]);
  }
  
  return root;
}

void sendNotFoundMessage(EthernetClient client) {
  sendErrorMessage(client, REQUEST_ERROR_INVALID_REQUEST);
}

void sendErrorMessage(EthernetClient client, int errorLevel) {
  // get error message
  String message = getErrorMessage(errorLevel);

  debugPrint("Error: " + message);

  JsonObject& root = getErrorMessageJSON(message);

  root.printTo(client);
}

JsonObject& getErrorMessageJSON(String message) {
  StaticJsonBuffer<1024> jsonBuffer;
  JsonObject& root = jsonBuffer.createObject();
  root.set("type", "error");
  root.set("message", message);
  
  return root;
}

void sendOkHeaders(EthernetClient client) {
  client.println("HTTP/1.1 200 OK");
  client.println("Content-Type: application/json");
  client.println("Connection: close");
  client.println();
}

void sendNotFoundHeaders(EthernetClient client) {
  client.println("HTTP/1.1 404 Not Found");
  client.println("Content-Type: text/html");
  client.println("Connection: close");
  client.println();
}

void sendBadRequestHeaders(EthernetClient client) {
  client.println("HTTP/1.1 400 Bad Request");
  client.println("Content-Type: application/json");
  client.println("Connection: close");
  client.println();
}

void sendCheckIn() {
  debugPrint("Reporting in with server");

  // initialise a client
  EthernetClient statusClient;

  // connect and send message
  if (statusClient.connect(ipAddressController, 80)) {
    // get the JSON status
    JsonObject& root = getStatusJSON();
    
    // calculate message length
    int messageLength = root.measureLength();
    // +8 for "message="
    messageLength += 8;
    
    // print POST header
    statusClient.println("POST " + path + "registry.php?do=report HTTP/1.0");
    statusClient.println("Content-Type: text/html; charset=utf-8");
    statusClient.print("Content-Length: ");
    statusClient.println(messageLength);
    
    // required blank line
    statusClient.println();
 
    // print POST parameter
    statusClient.print("message=");
    // print value (the JSON status string)
    root.printTo(statusClient);
    
    statusClient.stop();
  } else {
    debugPrint("Status report failed");
  }
}

int handleToggleRequest(String request) {
  int setIndex = request.indexOf("set=");
  int httpIndex = request.indexOf("HTTP/1.1");

  if (setIndex < 0 || httpIndex < 0) {
    // didn't find required information in request
    return REQUEST_ERROR_INVALID_REQUEST;
  }

  // get characters from "input=" to end of request
  String jsonString = request.substring(setIndex + 4, httpIndex);

  debugPrint("Parsing string: " + jsonString);

  // JSON buffer for receiving JSON messages
  StaticJsonBuffer<1024> jsonBuffer;

  // parse JSON
  JsonObject& root = jsonBuffer.parseObject(jsonString);

  if (! root.success()) {
    debugPrint("Failed to parse JSON object");

    return REQUEST_ERROR_INVALID_JSON;
  }

  debugPrint("Successfully parsed JSON object");

  //
  // check that there is at least one pin toggle defined
  //

  if (! root.containsKey("pinmode")) {
    return REQUEST_ERROR_NO_PIN_MODE;
  }

  // check for "single pin" mode
  if (root["pinmode"].as<int>() == PIN_MODE_SINGLE) {
    // single pin mode
    debugPrint("[Single pin mode]");

    /*
     * For single pin mode, we require the following information to be specified as keys of the JSON object:
     *   pin: the output pin to set
     *   value: the output value to set
     *   togglemode: the toggle mode, snap or ramp
     *   delay: wait time in ms between output levels (only required if togglemode == ramp)
     */

    int outputPin;
    int outputValue;

    if (! root.containsKey("pin")) {
      // required key not specified
      return REQUEST_ERROR_NO_PIN;
    }

    outputPin = int(root["pin"]);

    if (! outputPinExists(outputPin)) {
      return REQUEST_ERROR_INVALID_PIN;
    }

    if (! root.containsKey("value")) {
      // output value not specified
      return REQUEST_ERROR_NO_PIN_VALUE;
    }

    outputValue = int(root["value"]);

    if (! outputValueIsValid(outputValue)) {
      return REQUEST_ERROR_INVALID_PIN_VALUE;
    }

    if (! root.containsKey("togglemode")) {
      // no toggle mode specified
      return REQUEST_ERROR_NO_TOGGLE_MODE;
    }

    if (root["togglemode"].as<int>() == TOGGLE_MODE_SNAP) {
      debugPrint("[Snap toggle mode]");

      snapToValue(outputPin, outputValue);
    } else if (root["togglemode"].as<int>() == TOGGLE_MODE_RAMP) {
      debugPrint("[Ramp toggle mode]");

      int rampDelay;

      if (! root.containsKey("delay")) {
        // ramp delay not specified
        return REQUEST_ERROR_NO_DELAY;
      }

      rampDelay = int(root["delay"]);

      if (rampDelay < 0) {
        // invalid ramp delay
        return REQUEST_ERROR_INVALID_DELAY;
      }

      rampToValue(outputPin, outputValue, rampDelay);
    } else {
      // invalid toggle mode
      return REQUEST_ERROR_INVALID_TOGGLE_MODE;
    }

    // save pin value to SD
    savePinValue(outputPin);
  } else if (root["pinmode"].as<int>() == PIN_MODE_DUAL) {
    // dual pin mode
    debugPrint("[Dual pin mode]");

    /*
     * For dual pin mode, we require the following information to be specified as keys of the JSON object:
     *   pin1: the 'coarse' output pin to set
     *   pin2: the 'fine' output pin to set
     *   value1: the output value to set for 'coarse' pin
     *   value2: the output value to set for 'fine' pin
     *   overlap: the pin setting where 'coarse' pin overlaps with 'fine' pin
     *   map: a mapping between levels of the 'fine' pin and levels of the 'coarse' pin
     *   togglemode: the toggle mode, snap or ramp
     *   delay: wait time in ms between 'fine' output levels (only required if togglemode == ramp)
     *
     * Dual pin mode uses the 'fine' pin to bridge the gap between the 'coarse' output levels. It is assumed
     * that the 'coarse' and 'fine' pins are connected to the same device, but with different gains. The mapping
     * between signal magnitudes is specified by the 'map' key.
     */

    int coarsePin;
    int finePin;
    int coarseValue;
    int fineValue;
    int overlapValue;
    int mapping;

    if (! root.containsKey("pin1")) {
      // required key not specified
      return REQUEST_ERROR_NO_PIN;
    }

    coarsePin = int(root["pin1"]);

    if (! outputPinExists(coarsePin)) {
      return REQUEST_ERROR_INVALID_PIN;
    }

    if (! root.containsKey("pin2")) {
      // required key not specified
      return REQUEST_ERROR_NO_PIN;
    }

    finePin = int(root["pin2"]);

    if (! outputPinExists(finePin)) {
      return REQUEST_ERROR_INVALID_PIN;
    }

    if (! root.containsKey("value1")) {
      // output value not specified
      return REQUEST_ERROR_NO_PIN_VALUE;
    }

    coarseValue = int(root["value1"]);

    if (! outputValueIsValid(coarseValue)) {
      return REQUEST_ERROR_INVALID_PIN_VALUE;
    }

    if (! root.containsKey("value2")) {
      // output value not specified
      return REQUEST_ERROR_NO_PIN_VALUE;
    }

    fineValue = int(root["value2"]);

    if (! outputValueIsValid(fineValue)) {
      return REQUEST_ERROR_INVALID_PIN_VALUE;
    }

    if (! root.containsKey("overlap")) {
      // overlap value not specified
      return REQUEST_ERROR_NO_OVERLAP;
    }

    overlapValue = int(root["overlap"]);

    if (! outputValueIsValid(overlapValue)) {
      return REQUEST_ERROR_INVALID_OVERLAP;
    }

    if (! root.containsKey("mapping")) {
      // mapping not specified
      return REQUEST_ERROR_NO_MAPPING;
    }

    mapping = int(root["mapping"]);

    if (mapping < 0) {
      // invalid mapping
      return REQUEST_ERROR_INVALID_MAPPING;
    }

    if (! root.containsKey("togglemode")) {
      // no toggle mode specified
      return REQUEST_ERROR_NO_TOGGLE_MODE;
    }

    if (root["togglemode"].as<int>() == TOGGLE_MODE_SNAP) {
      debugPrint("[Snap toggle mode]");

      snapToValue(coarsePin, coarseValue);
      snapToValue(finePin, fineValue);
    } else if (root["togglemode"].as<int>() == TOGGLE_MODE_RAMP) {
      debugPrint("[Ramp toggle mode]");

      int rampDelay;

      if (! root.containsKey("delay")) {
        // ramp delay not specified
        return REQUEST_ERROR_NO_DELAY;
      }

      rampDelay = int(root["delay"]);

      if (rampDelay < 0) {
        // invalid ramp delay
        return REQUEST_ERROR_INVALID_DELAY;
      }

      /*
       * Now that we've collected valid parameters, we ramp to the correct output.
       */

      int currentCoarseValue = pinValues[getPinPosition(coarsePin)];
      int currentFineValue = pinValues[getPinPosition(finePin)];

      // number of coarse steps to make
      int coarseSteps = coarseValue - currentCoarseValue;

      debugPrint("There are " + String(coarseSteps) + " coarse steps to make");

      // true means pin output is to increase, false means decrease
      int coarseDirection;

      if (coarseSteps > 0) {
        coarseDirection = 1;
      } else {
        coarseDirection = -1;
      }

      debugPrint("Direction: " + coarseDirection);

      if (coarseSteps != 0) {
        // we need to use the fine pins to ramp to the next coarse level, and so on, until we reach the correct coarse level

        // move fine output to middle of range
        //rampToValue(finePin, MID_OUTPUT_LEVEL, rampDelay);

        // loop over coarse steps
        for (int i = 0; i < abs(coarseSteps); i++) {
          // move fine pin to next 'coarse' level
          rampToValue(finePin, MID_OUTPUT_LEVEL + coarseDirection * mapping, rampDelay);

          // snap coarse pin to next value and fine pins back to mid level
          snapToValue(coarsePin, currentCoarseValue + coarseDirection);
          snapToValue(finePin, MID_OUTPUT_LEVEL);

          // update current pin values
          currentCoarseValue = pinValues[getPinPosition(coarsePin)];
          currentFineValue = pinValues[getPinPosition(finePin)];
        }
      }

      // adjust fine output
      rampToValue(finePin, fineValue, rampDelay);
    } else {
      // invalid toggle mode
      return REQUEST_ERROR_INVALID_TOGGLE_MODE;
    }

    // save pin values to SD
    savePinValue(coarsePin);
    savePinValue(finePin);
  } else {
    // invalid pin mode specified
    return REQUEST_ERROR_INVALID_PIN_MODE;
  }

  return REQUEST_ERROR_NONE;
}

/*
 * Ramps specified pins to specified values at specified rate
 */
void rampToValue(int pin, int newValue, int stepPause)
{
  if (pinExists(pin)) {
    int currentValue = pinValues[getPinPosition(pin)];
    int difference = newValue - currentValue;

    while (difference != 0) {
      if (difference > 0) {
        // set point is higher than current value
        currentValue++;
      } else {
        // set point is lower than current value
        currentValue--;
      }

      snapToValue(pin, currentValue);

      delay(stepPause);

      difference = newValue - currentValue;
    }
  }
}

/*
 * Instantly snaps specified pin to value without delay.
 */
void snapToValue(int pin, int value) {
  debugPrint("Setting " + String(pin) + String(" from ") + String(pinValues[getPinPosition(pin)]) + String(" to ") + String(value) + String("..."));

  // change the output level
  analogWrite(pin, value);

  debugPrint(" done");

  // update pin value array with new value
  pinValues[getPinPosition(pin)] = value;
}

/*
 * For a given pin, returns that pin's index in the pin output value array
 */
int getPinPosition(int pin)
{
  for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
    if (pin == outputPins[i]) {
      return i;
    }
  }

  return -1;
}

/*
 * Checks if the specified pin exists
 */
boolean pinExists(int pin)
{
  // check input is in list
  for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
    if (pin == outputPins[i]) {
      return true;
    }
  }

  return false;
}

void restoreSavedOutputLevels() {
  for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
    String filename = "PIN"; // capital letters in filename required by FAT16
    filename += String(outputPins[i]);
    filename += ".TXT"; // the three character extension is required by FAT16

    // convert String object to char array, because the SD library doesn't support Strings
    char filenameChar[filename.length() + 1];
    filename.toCharArray(filenameChar, sizeof(filenameChar));

    if (SD.exists(filenameChar)) {
      // saved value file found, so read it
      File filePointer = SD.open(filenameChar, FILE_READ);

      if (filePointer) {
        debugPrint("Found value file for pin " + String(outputPins[i]));

        char message[3];

        int j = 0;

        // read first 3 characters
        while (filePointer.available() && j < 3) {
          message[j] = filePointer.read();

          j++;
        }

        // convert char array to int
        int value = atoi(message);

        // sanity check the value - make sure it's within range
        if (value < MIN_OUTPUT_LEVEL) {
          value = MIN_OUTPUT_LEVEL;
        } else if (value > MAX_OUTPUT_LEVEL) {
          value = MAX_OUTPUT_LEVEL;
        }

        // set the appropriate pin's value
        snapToValue(outputPins[i], value);

        filePointer.close();
      } else {
        debugPrint("Couldn't open file even though it exists. Using default value.");
        snapToValue(outputPins[i], DEFAULT_OUTPUT_VALUE);
      }
    } else {
      debugPrint("Couldn't find value file for pin " + String(outputPins[i]) + ". Using default value.");

      snapToValue(outputPins[i], DEFAULT_OUTPUT_VALUE);
    }
  }
}

boolean savePinValue(int pin)
{
  String filename = "PIN";
  filename += String(pin);
  filename += ".TXT";

  char filenameChar[filename.length() + 1];
  filename.toCharArray(filenameChar, sizeof(filenameChar));

  // erase existing file (because we can't overwrite its contents)
  SD.remove(filenameChar);
  // make new file with value
  File filePointer = SD.open(filenameChar, FILE_WRITE);

  if (filePointer) {
    debugPrint("Writing pin value to file...");

    // get value
    int value = pinValues[getPinPosition(pin)];

    filePointer.println(String(value));
    filePointer.close();

    debugPrint("Done.");
  } else {
    debugPrint("Failed to write to file.");

    return false;
  }

  return true;
}

boolean outputPinExists(int pin) {
  for (int i = 0; i < NUMBER_OF_OUTPUTS; i++) {
    if (outputPins[i] == pin) {
      return true;
    }
  }

  return false;
}

boolean outputValueIsValid(int value) {
  if (value < MIN_OUTPUT_LEVEL || value > MAX_OUTPUT_LEVEL) {
    return false;
  }

  return true;
}

String getErrorMessage(int errorLevel) {
  return requestErrorMessages[errorLevel];
}
