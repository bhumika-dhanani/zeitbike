;(function ( $, window, document, undefined ) {

    // Create the defaults once
    var pluginName = "pdfoptFancybox",
        defaults = {
            type        : 'inline',
            title       : 'Catalog Print Option',
	    printUrl    : '',
            printConfig : null,
            printBtnLabel: 'Print Catalog',
            customerGroup: 0,
            //assemblyUrl : ''
        };

    // The actual plugin constructor
    function Plugin( element, options ) {
        this.element = element;

        // jQuery has an extend method which merges the contents of two or
        // more objects, storing the result in the first object. The first object
        // is generally empty as we don't want to alter the default options for
        // future instances of the plugin
        this.options = $.extend( {}, defaults, options );

        this._defaults = defaults;
        this._name = pluginName;

        this.jsonObj;
        this.init();
    }

    Plugin.prototype = {

        init: function() {
            var title       = this.options.title;
	    var printUrl    = this.options.printUrl;
            var printConfig = this.options.printConfig;
            var printBtnLabel = this.options.printBtnLabel;
            var customer_group_id = parseInt(this.options.customerGroup);
            //var assemblyUrl = this.options.assemblyUrl;
            
            $(this.element).fancybox({
                type: this.options.type,
                beforeShow: function () {
					var form = $('<form></form>').attr("method","post");
					form.attr("id","option-form");
					form.attr("action",printUrl);
					form.attr("target","_blank");
					var submit = $('<button></button>').attr("type","submit");
					submit.attr("class","button");
					submit.bind("click",function(){
						$.fancybox.close();
                        if(navigator.appName == "Microsoft Internet Explorer")
                        {
                            var appVer = navigator.appVersion;
                        
                            if(appVer.indexOf("MSIE 7.0") != -1)
                            {
                                $('#option-form').submit();
                            }
                        }
                        
					});
					submit.html("<span><span>"+printBtnLabel+"</span></span>");
                    $(".fancybox-inner").html('');
                    var ul = $('<ul class="inline-opt"></ul>');
					ul.append("<li><p class='amount'><strong>"+printConfig.layout_options[0]+"</strong></p></li>");
					ul.append('<li class="layout-opts"><input class="radio" checked="checked" type="radio" name="layout" value="grid" /><span>'+printConfig.layout_options[1]+'</span></li>');
					ul.append('<li class="layout-opts"><input class="radio last" type="radio" name="layout" value="list" /><span>'+printConfig.layout_options[2]+'</span></li>');
					form.append(ul);
					var ul2 = $('<ul class="pdfOpt"></ul>');
					var li1 = $('<li></li>').append("<p class='amount'><strong>"+printConfig.pdf_options[0]+"</strong></p>");
						var ul11 = $("<ul></ul>").append(
							'<li><input class="radio" type="checkbox" checked="checked" name="heading" value="1" />'+printConfig.pdf_options[1]+'</li>');
						ul11.append('<li><input checked="checked" class="radio" type="checkbox" name="cover" value="2" />'+printConfig.pdf_options[2]+'</li>');
						ul11.append('<li><input checked="checked" class="radio" type="checkbox" name="contents" value="2" />'+printConfig.pdf_options[3]+' </li>');
						ul11.append('<li><input checked="checked" class="radio" type="checkbox" name="mfg_asc" value="3" />'+printConfig.pdf_options[4]+' </li>');
						ul11.append('<li><input class="radio" type="checkbox" name="part_asc" value="4" />'+printConfig.pdf_options[5]+'</li>');
					li1.append(ul11);
					ul2.append(li1);
					form.append(ul2);
					var div1 = $('<div></div>').attr("class","buttons-set");
					if(navigator.appName == "Microsoft Internet Explorer")
					{
						var appVer = navigator.appVersion;
					
						if(appVer.indexOf("MSIE 7.0") != -1)
						{
							div1.attr('style','margin-right:25px;width:514px;');

						}
					}
                                        div1.append('<input type="hidden" name="customer_group_id" value="'+customer_group_id+'" />');
					div1.append(submit);
					
					form.append(div1);
					//var li3 = $('<li></li>').append("Product Attribute Option");
                    //ul.append(li3);
					$(".fancybox-header-middle").html('<h2>' + title + '</h2>');
                    //set content
                    $(".fancybox-inner").append(form);
                    //set width of header
                    //$(".fancybox-header-middle").width($(".fancybox-header").width()-$(".fancybox-header-left").width()-$(".fancybox-header-right").width());
					$(".fancybox-header-middle").css({'width':'528px'});
					//$(".fancybox-wrap").width("542px");
				}
            });
        }
    };

    // A really lightweight plugin wrapper around the constructor,
    // preventing against multiple instantiations
    $.fn[pluginName] = function ( options ) {
        return this.each(function () {
            if (!$.data(this, "plugin_" + pluginName)) {
                $.data(this, "plugin_" + pluginName, new Plugin( this, options ));
            }
        });
    };

})( $md, window, document );