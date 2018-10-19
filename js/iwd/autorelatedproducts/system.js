jQuery(document).ready(function () {

    var currentColor = "ffffff";
    jQuery("#iwd_autorelatedproducts_design_addbutton_background, " +
        "#iwd_autorelatedproducts_design_addbutton_background_hover, " +
        "#iwd_autorelatedproducts_design_addbutton_textcolor, " +
        "#iwd_autorelatedproducts_design_addbutton_textcolor_hover").on('click',function () {
            currentColor = jQuery(this).css('background-color');
            currentColor = rgb2hex(currentColor);
        }).on('change', function(){
            iwd_autorelatedproducts_design_init(jQuery(this));
        }).colpick({
            onBeforeShow: function () {
                jQuery(this).colpickSetColor(currentColor);
            },
            colorScheme: 'light',
            layout: 'rgbhex',
            onSubmit: function (hsb, hex, rgb, el) {
                jQuery(el).colpickHide();
                jQuery(el).val('#' + hex).css('background-color', '#' + hex);
            }
        });

    iwd_autorelatedproducts_design_init("#iwd_autorelatedproducts_design_addbutton_background");
    iwd_autorelatedproducts_design_init("#iwd_autorelatedproducts_design_addbutton_background_hover");
    iwd_autorelatedproducts_design_init("#iwd_autorelatedproducts_design_addbutton_textcolor");
    iwd_autorelatedproducts_design_init("#iwd_autorelatedproducts_design_addbutton_textcolor_hover");
});

function iwd_autorelatedproducts_design_init(input){
    var color = jQuery(input).val();
    jQuery(input).css('background-color', color);
}

function rgb2hex(rgb) {
    rgb = rgb.match(/^rgb\w*\((\d+),\s*(\d+),\s*(\d+)/);
    return "#" +
        ("0" + parseInt(rgb[1], 10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[2], 10).toString(16)).slice(-2) +
        ("0" + parseInt(rgb[3], 10).toString(16)).slice(-2);
}