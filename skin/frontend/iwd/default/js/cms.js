var $j = $j.noConflict();
var cms_banner_block = '.cms-banner, .cms-text-banner';
$j(document).ready(function () {
	$j(cms_banner_block).each(function(){
		var background_image = $j(this).find(' > img ');
		
		if(typeof background_image !=='undefined')
		{	
			var src = background_image.attr('src');
		    $j(this).css("background", "transparent url('"+src+"') no-repeat center center");
			background_image.css('visibility', 'hidden');
			if($j(this).hasClass('cms-text-banner'))
			{
				background_image.hide();
			}
		}
	});
});
/*added for speed order pagination and ajax add to cart*/
function setAjaxData(data,id,iframe){
    if(data.status == 'ERROR'){
        alert(data.message);
        $j('button#show_'+id).html("Add to Cart");
        $j('#show_'+id).removeAttr('disabled');
    }else{
        var json =  eval(data);
        if ($j('.header-minicart')) {
            $j('.header-minicart').html( json.minicart );
            $j('#header-account').html(json.minicart );
            $j('button#show_'+id).html("Add More...");
            $j('button#show_'+id).removeAttr('disabled');
        }
        $j('#header-cart').on('click', '.skip-link-close', function(e) {
            var parent = $j(this).parents('.skip-content');
            var link = parent.siblings('.skip-link');
            parent.removeClass('skip-active');
            link.removeClass('skip-active');
            e.preventDefault();
        });
    }
}

function updateTable(page_url) {
    if($j('.spof-search_box').val() != ''){
        if(page_url.match("search_query") == null){
            if(page_url.indexOf("?") != -1) {
                page_url = page_url + '&search_query=' + encodeURI($j('.spof-search_box').val());
            }else{
                page_url = page_url + '?search_query='+ encodeURI($j('.spof-search_box').val());
            }
        }
    }
    $j.ajax({
        type: "get",
        url: page_url,
        success: function (data) {
            var finaldata  = $j(data).find('#amshopby-page-container').html();
            $j("#amshopby-page-container").html(finaldata);
            $j(".amshopby-overlay").hide();
            $j(document).ready(function() {
                $j(".speedorder-paging .pages a").on('click', function (event) {
                    $j(".amshopby-overlay").show();
                    event.preventDefault();
                    var page_url = $j(this).attr('href');
                    updateTable(page_url);
                });
                $j(".products-list").off("click.plus").on("click.plus", ".plus, .minus", function() {
                    qtyIncDec(this);
                    return false;
                });
                $j('.spof-search_box').keypress(function (e) {
                    var key = e.which;
                    if(key == 13)  // the enter key code
                    {
                        $j('.search_btn').trigger('click');
                        return false;
                    }
                });
            });
        }
    });
}
$j(document).ready(function() {
    $j('.toolbar-bottom .spof-search_box').remove();
    $j(".speedorder-paging .pages a").on('click', function (event) {
        $j(".amshopby-overlay").show();
        event.preventDefault();
        var page_url = $j(this).attr('href');
        updateTable(page_url);
    });
    String.prototype.getDecimals || (String.prototype.getDecimals = function() {
        var t = ("" + this).match(/(?:\.(\d+))?(?:[eE]([+-]?\d+))?$/);
        return t ? Math.max(0, (t[1] ? t[1].length : 0) - (t[2] ? +t[2] : 0)) : 0
    });
    $j(".products-list").off("click.plus").on("click.plus", ".plus, .minus", function() {
        qtyIncDec(this);
        return false;
    });
    $j("body").on('blur', '.qty', function () {

        var maxval = parseInt($j(this).attr('max'));
        var minval = parseInt($j(this).attr('min'));
        var stepval = parseInt($j(this).attr('step'));

        if (parseInt($j(this).val()) < minval) {
            $j(this).val(minval);
            alert("Minimum quantity should be " + minval + "!");
            return !1
        }
        if (parseInt($j(this).val()) > maxval) {
            $j(this).val(maxval);
            alert("Maximum quantity should be " + maxval + "!");
            return !1
        }
        if (parseInt($j(this).val()) < stepval) {
            $j(this).val(stepval);
            alert("Quantity must be in multiple of " + stepval + " !");
            return !1
        }
        if ($j(this).val() == '') {
            $j(this).val(stepval);
            alert("Quantity must be in multiple of " + stepval + " !");
            return !1
        }
    });
    var delay = (function(){
        var timer = 0;
        return function(callback, ms){
            clearTimeout (timer);
            timer = setTimeout(callback, ms);
        };
    })();
    /*$j('#srch_pro_term').on('keyup change', function (evt){

        if (evt.keyCode == 13) {
            evt.preventDefault();
            return !1;
        }
        var search_string = $j('#srch_pro_term').val();
        if(search_string == ''){
            delay(function(){
                $j(".amshopby-overlay").show();
                updateTable($j('.spof-search_box').data('action'));
            }, 1000 );

        }else {
            if ($j.trim(search_string).length < 3) return;
            delay(function(){
                $j(".amshopby-overlay").show();
                updateTable($j('.spof-search_box').data('action'));
            }, 2000 );
        }
    });*/
    $j('.spof-search_box').keypress(function (e) {
        var key = e.which;
        if(key == 13)  // the enter key code
        {
            $j('.search_btn').trigger('click');
            return false;
        }
    });
});
function setLocationSearch(searchurl){
    var search_string = $j('#srch_pro_term').val();
        $j(".amshopby-overlay").show();
        updateTable(searchurl);

}
function qtyIncDec(t) {
    var e = $j(t).closest(".quantity").find(".qty"),
        r = parseFloat(e.val()),
        a = parseFloat(e.attr("max")),
        n = parseFloat(e.attr("min")),
        s = e.attr("step");
    "6" == s && ($(t).hasClass("plus") ? r % 6 != 0 && (e.val(6 * Math.ceil(r / 6)), r = e.val()) : r % 6 != 0 && (e.val(6 * Math.floor(r / 6) + parseInt(s)), r = e.val())), r && "" !== r && "NaN" !== r || (r = 0), "" !== a && "NaN" !== a || (a = ""), "" !== n && "NaN" !== n || (n = 0), "any" !== s && "" !== s && void 0 !== s && "NaN" !== parseFloat(s) || (s = 1), $j(t).is(".plus") ? a && r >= a ? e.val(a) : e.val((r + parseFloat(s)).toFixed(s.getDecimals())) : n && r <= n ? e.val(n) : r > 0 && e.val((r - parseFloat(s)).toFixed(s.getDecimals())), e.trigger("change")
}

function setLocationAjax(url,id,type){
    var qty = $j('#quantity_'+id).val();
    if(qty != null) {
        url += 'isAjax/1/qty/' + qty;
    }else{
        url += 'isAjax/1';
    }
    var btnText = 'Show Options';
    var btnHideText = 'Hide Options';
    if(type == 'simple' || type == 'virtual' || type == 'downloadable') {
        url = url.replace("checkout/cart", "speedorder/ajax");
        $j('button#show_'+id).html("Adding...");
        $j('button#show_'+id).attr('disabled', 'disabled');
        /*$j('#ajax_loader'+id).show();*/
        $j.ajax({
            url: url,
            dataType: 'json',
            success: function (data) {
                    setAjaxData(data,id, false);

            }
        });
    }else{
        url += '/pid/'+id;
        url = url.replace("checkout/cart/add", "speedorder/ajax/showoption");
        $j('#show_'+id).attr('disabled', 'disabled');
        if ($j('#show_'+id).closest('tr').next().hasClass('appendvaiant')) {
            if ($j('#show_'+id).closest('tr').next().css('display') != "none") {
                $j('#show_'+id).closest('tr').next().attr('style', 'display:none!important;');
                $j('button#show_'+id).html(btnText);
                $j('#show_'+id).removeAttr('disabled')
            } else {
                $j('#show_'+id).closest('tr').next().show();
                $j('button#show_'+id).html(btnHideText);
                $j('#show_'+id).removeAttr('disabled')
            }
        } else {
            $j.ajax({
                url: url,
                dataType: 'json',
                success: function (data) {
                    if (data.status == 'SUCCESS') {
                        $j('#show_' + id).closest('tr').after('<tr class="variant items appendvaiant"><td colspan="7"><table width="100%">' + data.options + '</table></td></tr>');
                        $j('button#show_'+id).html(btnHideText);
                        $j('#show_' + id).removeAttr('disabled');

                    }
                }
            });
        }
    }

}



