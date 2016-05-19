<script type="text/javascript">
    // initialise mode switches
    $(document).ready(
        function() {
            // enable fancy switch buttons on driver outputs
            $(".switchramp").each(
                function() {
                    $(this).bootstrapSwitch()
                }
            );
            
            // set callbacks on driver output adjustment buttons
            $(".driver-output-adjust-button").each(
                function() {
                    $(this).click(
                        function() {
                            // get driver output
                            var driverOutputId = $(this).data("driverOutputId");
                        
                            // get adjustment offset
                            var offset = $(this).data("offset");
                            
                            // get current value
                            var currentValue = getDriverOutputValue(driverOutputId);
                            
                            // toggle output
                            toggleOutput(driverOutputId, currentValue + offset);
                        }
                    );
                }
            );
            
            // set callbacks on driver output adjustment textareas
            $(".driver-output-adjust-set-button").each(
                function() {
                    $(this).click(
                        function() {
                            // get driver output
                            var driverOutputId = $(this).data("driverOutputId");
                            
                            // get adjustment value
                            var value = getSetToValue(driverOutputId);
                            
                            // toggle output
                            toggleOutput(driverOutputId, value);
                        }
                    );
                }
            );
        }
    );
    
    // toggles the output
    function toggleOutput(driverOutputId, value) {        
        // disable buttons etc.
        setWidgetStates(driverOutputId, false);
        
        // mute the current output value
        setDriverOutputValueMute(driverOutputId, true);
        
        toggleRequest(
            driverOutputId,
            value,
            function(data) {
                // successful communication
                
                // was the toggle ok?
                if (data.status === "ok") {
                    // update the value
                    setDriverOutputValueFromResponse(driverOutputId, data);
                } else if (data.status === "error") {
                    // error - the message should give some detail
                    alertMessage(data.message);
                } else {
                    // unknown error
                    alertMessage("Unrecognised response code");
                }
            },
            function(jqXHR, textStatus, errorThrown) {
                // communication error
                console.log(jqXHR);
                
                alertMessage("Communication error (" + textStatus + "): " + errorThrown);
            },
            function() {
                // finally, re-enable the controls
                
                // unmute the current output value
                setDriverOutputValueMute(driverOutputId, false);
            
                // enable buttons etc.
                setWidgetStates(driverOutputId, true);
            }
        );
    }
    
    function setDriverOutputValueFromResponse(driverOutputId, response) {
        try {
            var keyVal = JSON.parse(response.message);
        } catch (e) {
            alertMessage(e);
            
            return;
        }
        
        setDriverOutputValue(driverOutputId, keyVal[driverOutputId]);
    }
    
    function setDriverOutputValue(driverOutputId, value) {
        if (! isInt(value)) {
            alertMessage("Received output value is not an integer");
            
            return;
        }
        
        $(".driver-output-adjust-current-value").each(
            function() {
                if ($(this).data("driverOutputId") === driverOutputId) {
                    $(this).text(value);
                    
                    return;
                }
            }
        );
    }
    
    function alertMessage(message) {
        alert(message);
    }
    
    function isInt(n) {
        // from http://stackoverflow.com/questions/3885817/how-to-check-that-a-number-is-float-or-integer
        return n === +n && n === (n|0);
    }
    
    function toggleRequest(driverOutputId, value, toggleSuccessCallback, toggleFailureCallback, toggleAlwaysCallback) {
        // get toggle mode
        var toggleMode = getSwitchToggleMode(driverOutputId);        
        var toggleModeString = (toggleMode) ? "ramp" : "snap";
        
        // create AJAX command
        var command = "comms.php?do=dual&oid=" + driverOutputId + "&value=" + value + "&togglemode=" + toggleModeString;
        
        // send AJAX command
        $.getJSON(command).done(toggleSuccessCallback).fail(toggleFailureCallback).always(toggleAlwaysCallback);
    }
    
    function setDriverOutputValueMute(driverOutputId, mute) {
        $(".driver-output-adjust-current-value").each(
            function() {
                if ($(this).data("driverOutputId") === driverOutputId) {
                    $(this).toggleClass("text-muted", mute);
                    
                    return;
                }
            }
        );
    }
    
    function setWidgetStates(driverOutputId, state) {
        // set "set to" button
        $(".driver-output-adjust-set-button").each(
            function() {
                setButtonState($(this), state);
            }
        );
        
        // set offset buttons
        $(".driver-output-adjust-button").each(
            function() {
                setButtonState($(this), state);
            }
        );
        
        // set ramp/snap switch
        $(".switchramp").each(
            function() {
                setSwitchState($(this), state);
            }
        );
    }
    
    function setButtonState(button, state) {    
        if (state) {
            button.removeAttr('disabled');
        } else {
            button.attr('disabled', 'disabled');
        }
    }
    
    function setSwitchState(selectSwitch, state) {
        selectSwitch.bootstrapSwitch("disabled", !state);
    }
    
    // finds the toggle mode of the specified output ID
    function getSwitchToggleMode(driverOutputId) {
        var toggleMode = false;
    
        $(".switchramp").each(
            function() {
                if ($(this).data("driverOutputId") === driverOutputId) {
                    toggleMode = $(this).bootstrapSwitch("state");
                    
                    return;
                }
            }
        );
        
        return toggleMode;
    }
    
    // finds the value of the driver output
    function getDriverOutputValue(driverOutputId) {
        var value = null;
        
        $(".driver-output-adjust-current-value").each(
            function() {
                if ($(this).data("driverOutputId") === driverOutputId) {
                    value = parseInt($(this).text(), 10);
                    
                    return;
                }
            }
        );
        
        return value;
    }
    
    // finds the value in the manual adjustment text box
    function getSetToValue(driverOutputId) {
        var value = null;
        
        $(".driver-output-adjust-set-text").each(
            function() {
                if ($(this).data("driverOutputId") === driverOutputId) {
                    value = parseInt($(this).val(), 10);
                    
                    return;
                }
            }
        );
        
        return value;
    }
</script>