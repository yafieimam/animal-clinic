(function () {
    "use strict";
    var closed = true;
    $(".top-bar, .top-bar-boxed")
        .find(".search")
        .find("input")
        .each(function (e) {
            $(this).on("focus", function () {
                $(".top-bar, .top-bar-boxed")
                    .find(".search-result")
                    .addClass("show");
                closed = false;
            });

            $(this).on("focusout", function (e) {
                closed = true;
            });

        });

    $(window).on('click', function (e) {

        if (closed) {
            console.log(!$(e.target).hasClass('search-result__content'))
            if (!$(e.target).parent('.search-result__content').hasClass('search-result__content') && !$(e.target).hasClass('search-result__content')) {
                $(".top-bar, .top-bar-boxed")
                    .find(".search-result")
                    .removeClass("show");
            }
        }
    })
})();
