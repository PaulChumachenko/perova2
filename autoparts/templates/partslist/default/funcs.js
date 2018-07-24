function ShowMPrices(pkey){$('.ip'+pkey).show('fast'); $('.sb'+pkey).hide(); $('.hb'+pkey).show();}
function HideMPrices(pkey){$('.ip'+pkey).hide('fast'); $('.sb'+pkey).show(); $('.hb'+pkey).hide();}

function ShowMoreListPrices(pkey){
	$('.pr'+pkey).show('fast'); $('.sb'+pkey).hide(); $('.op'+pkey).show('fast');
}

var ShowLettersFilter=0;
$(document).ready(function () {
    if(ShowLettersFilter==1){
		var ABrandsDiv=$('.bfname');
		ABrandsDiv.hide();
		var LetsDiv = $('.letfilter > a');
		LetsDiv.click(
			function (){
				FstLet=$(this).text();
				LetsDiv.removeClass("active");
				$(this).addClass("active");
				ABrandsDiv.hide();
				ABrandsDiv.each(function(i){
					var AText = $(this).eq(0).text().toUpperCase();
					if(RegExp('^' + FstLet).test(AText)) {
						$(this).fadeIn(400);
					}
				});
		});
	}
});

var FIRST_PAGE_LINK='';
function AddBrandFilter(BKEY){
	$("<form action='"+FIRST_PAGE_LINK+"' id='bfilterform' method='post'><input type='hidden' name='BRAND_FILTER' value='"+BKEY+"'/></form>").appendTo('body');
	$("#bfilterform").submit();
}

function RemoveBrandFilter(BKEY){
	$("<form action='"+FIRST_PAGE_LINK+"' id='bfilterform' method='post'><input type='hidden' name='BRAND_REMOVE' value='"+BKEY+"'/></form>").appendTo('body');
	$("#bfilterform").submit();
}


function ViewSwitch(VIEW){
	$("<form action='"+FIRST_PAGE_LINK+"' id='viewform' method='post'><input type='hidden' name='VIEW' value='"+VIEW+"'/></form>").appendTo('body');
	$("#viewform").submit();
}

function ShowMoreProps(But, TDItem){
    TDItem = TDItem.replace(/\s/g, '\\ ');
    TDItem = TDItem.replace(/\./g, '\\.');
    var curHeight = $('#'+TDItem).height(),
        autoHeight = $('#'+TDItem).css('height','auto').height();

    $('#'+TDItem).height(curHeight);
    $('#'+TDItem).stop().animate({'height':autoHeight}, 500);
    $(But).hide('normal');
}

function pcAddBrandPopups() {
    var manufacturers = $('[data-pc-manufacturer]').map(function(){
        return $.trim($(this).text());
    }).get();
    manufacturers = $.unique(manufacturers);

    $.get('/index.php?route=module/pc_autopart_brands&' + $.param({brands: manufacturers})).then(function (response) {
        if (!response) return false;

        for (var brand in response) {
            if (!response.hasOwnProperty(brand)) continue;

            $('[data-pc-manufacturer="' + brand + '"]').each(function(){
                $(this).popover({
                    placement: 'right',
                    container: 'body',
                    html: true,
                    title: $.trim($(this).text()),
                    content: response[brand],
                    template: '<div class="popover" role="tooltip"><div class="arrow"></div><div class="popover-header"><button type="button" class="close">&times;</button><h3 class="popover-title"></h3></div><div class="popover-content"></div></div>'
                }).on('shown.bs.popover', function (eventShown) {
                    var $popup = $('#' + $(eventShown.target).attr('aria-describedby'));
                    $popup.find('button.close').click(function (e) {
                        $popup.popover('hide');
                    });
                });

                $(this).css({cursor: 'help'});
            });
        }
    });
}

$(document).ready(function(){
    pcAddBrandPopups();

    $('body').on('hidden.bs.popover', function (e) {
        $(e.target).data("bs.popover").inState = { click: false, hover: false, focus: false }
    });
});