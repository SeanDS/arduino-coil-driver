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
        // get toggle mode
        var toggleMode = getSwitchToggleMode(driverOutputId);
        
        console.log("Driver output ID: " + driverOutputId);
        console.log("Value: " + value);
        console.log("Toggle mode: " + toggleMode);
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