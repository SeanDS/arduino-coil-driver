<svg 
   xmlns:dc="http://purl.org/dc/elements/1.1/"
   xmlns:cc="http://creativecommons.org/ns#"
   xmlns:rdf="http://www.w3.org/1999/02/22-rdf-syntax-ns#"
   xmlns:svg="http://www.w3.org/2000/svg"
   xmlns:xlink="http://www.w3.org/1999/xlink"
   xmlns="http://www.w3.org/2000/svg"
   version="1.1"
   viewBox="0 0 344 422">
    
    <style type="text/css" >
      <![CDATA[

        g.metal {
           stroke: #7f7f7f;
           stroke-width: 2;
           fill: #ffffff;
        }

      ]]>
    </style>
    
    <defs>
        <g id="viewport" class="metal">
            <path d="m -16 0 v8 h32 v-8 z"/>
            <path d="m -18 8 v6 h36 v-6 z"/>
        </g>
        <g id="tank-body" class="metal">            
            <g>
                <circle r="36"/>
                <circle r="33"/>
            </g>
        </g>
        <g id="tank">
            <!-- south viewport -->
            <use xlink:href="#viewport" transform="translate(0, 33)"/>
            
            <!-- north viewport -->
            <use xlink:href="#viewport" transform="translate(0, -33) rotate(180)"/>
            
            <!-- west viewport -->
            <use xlink:href="#viewport" transform="translate(-33, 0) rotate(90)"/>
            
            <!-- east viewport -->
            <use xlink:href="#viewport" transform="translate(33, 0) rotate(-90)"/>
            
            <!-- tank body -->
            <use xlink:href="#tank-body"/>
        </g>
        <g id="connector-single" class="metal">
            <path d="m -16 0 v-20 h32 v20"/>
            <path d="m -18 -20 h36 v-6 h-36 v6"/>
        </g>
        <g id="connector-double" class="metal">
            <path d="m -7 16 h14 v-32 h-14 z"/>
            <path d="m -7 18 h-6 v-36 h6 z"/>
            <path d="m 7 18 h6 v-36 h-6 z"/>
        </g>
    </defs>
    
    <!-- left ETM -->
    <a xlink:href="<?=$tankUrls['left_etm']?>">
        <use xlink:href="#tank" id="left_etm" transform="translate(50, 50)"/>
    </a>
    <use xlink:href="#connector-single" transform="translate(50, 124)"/>
    
    <!-- left ITM -->
    <a xlink:href="<?=$tankUrls['left_itm']?>">
        <use xlink:href="#tank" id="left_itm" transform="translate(50, 250)"/>
    </a>
    <use xlink:href="#connector-single" transform="translate(50, 176) rotate(180)"/>
    
    <!-- connector to bottom steering left from left ITM -->
    <use xlink:href="#connector-double" transform="translate(50, 311) rotate(90)"/>
    
    <!-- bottom steering left -->
    <a xlink:href="<?=$tankUrls['bottom_steering_left']?>">
        <use xlink:href="#tank" id="bottom_steering_left" transform="translate(50, 372)"/>
    </a>
    
    <!-- connector to bottom steering centre from bottom steering left -->
    <use xlink:href="#connector-double" transform="translate(111, 372)"/>
    
    <!-- bottom steering centre -->
    <a xlink:href="<?=$tankUrls['bottom_steering_centre']?>">
        <use xlink:href="#tank" id="bottom_steering_centre" transform="translate(172, 372)"/>
    </a>
    
    <!-- connector to middle steering centre from bottom steering centre -->
    <use xlink:href="#connector-double" transform="translate(172, 311) rotate(90)"/>
    
    <!-- middle steering centre -->
    <a xlink:href="<?=$tankUrls['middle_steering']?>">
        <use xlink:href="#tank" id="middle_steering" transform="translate(172, 250)"/>
    </a>
    
    <!-- connector to top steering centre from middle steering centre -->
    <use xlink:href="#connector-double" transform="translate(172, 189) rotate(90)"/>
    
    <!-- top steering centre -->
    <a xlink:href="<?=$tankUrls['top_steering']?>">
        <use xlink:href="#tank" id="top_steering" transform="translate(172, 128)"/>
    </a>
    <use xlink:href="#connector-single" transform="translate(172, 54) rotate(180)"/>
    
    <!-- connector to bottom steering right from bottom steering centre -->
    <use xlink:href="#connector-double" transform="translate(233, 372)"/>
    
    <!-- bottom steering right -->
    <a xlink:href="<?=$tankUrls['bottom_steering_right']?>">
        <use xlink:href="#tank" id="bottom_steering_right" transform="translate(294, 372)"/>
    </a>
    
    <!-- connector to bottom steering right from right ITM -->
    <use xlink:href="#connector-double" transform="translate(294, 311) rotate(90)"/>
    
    <!-- right ITM -->
    <a xlink:href="<?=$tankUrls['right_itm']?>">
        <use xlink:href="#tank" id="right_itm" transform="translate(294, 250)"/>
    </a>
    <use xlink:href="#connector-single" transform="translate(294, 176) rotate(180)"/>
    
    <!-- right ETM -->
    <a xlink:href="<?=$tankUrls['right_etm']?>">
        <use xlink:href="#tank" id="right_etm" transform="translate(294, 50)"/>
    </a>
    <use xlink:href="#connector-single" transform="translate(294, 124)"/>
</svg>

<script type="text/javascript">
    
    function setDim(element) {
        $(document).ready(function() {
            $(element).mouseenter(function() {
                $(this).attr("filter", "opacity(50%) sepia(50%) blur(0.5px)");
            });
            $(element).mouseout(function() {
                $(this).attr("filter", "opacity(100%) sepia(0) blur(0)");
            });
        });
    }
    
    setDim("#left_etm");
    setDim("#left_itm");
    setDim("#bottom_steering_left");
    setDim("#bottom_steering_centre");
    setDim("#middle_steering");
    setDim("#top_steering");
    setDim("#bottom_steering_right");
    setDim("#right_itm");
    setDim("#right_etm");
</script>