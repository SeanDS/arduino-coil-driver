# Arduino Coil Drivers
Server and software for networked, distributed Arduino coil driver controllers.

Arduino hardware required:
 - Arduino Due
 - Arduino ethernet shield (optionally with SD card reader)

Non-standard libraries required:
 - [ArduinoJson](https://github.com/bblanchon/ArduinoJson) 5.2.0

## Very quick guide ##
Ensure you have the libraries above.

Open up `/arduino/CoilDriver/CoilDriver.ino` in a text editor.

Set the list of output pins to use, the two digital input pins (to use for detection of touching coils and magnets), the default output value, the MAC address of the Ethernet shield, the IP address of the server running the software in `/server`, and the path to that software with respect to the IP address.

For debugging, remove the `#` on the line with `#DEBUG true` to enable messages on the serial output over USB. This is not recommended for production as it significantly slows down the operation during changes to the output pin values.

Save the file and upload to your Arduino(s).

Make sure your Arduino(s) is/are connected to the network and that the network will assign a static IP address to each Arduino (I achieved this by running a separate DHCP server on my host for a particular ethernet port).

Sean Leavey  
https://github.com/SeanDS