$(document).ready(function(){

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
                if ($(this).is('span'))
                    $(this).hover(function (e) {
                        if (e.type === "mouseenter") $(this).css({'text-decoration': 'underline', 'color': '#e4003a'});
                        if (e.type === "mouseleave") $(this).css({'text-decoration': 'none', 'color': '#333'});
                    });
            });
        }
    });

    $('body').on('hidden.bs.popover', function (e) {
        $(e.target).data("bs.popover").inState = { click: false, hover: false, focus: false }
    });
});