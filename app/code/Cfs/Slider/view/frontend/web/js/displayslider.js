require(['jquery', 'owlcarousel'], function ($) {
    $(document).ready(function () {

        $('.home-slider').owlCarousel({
            center: false,
            startPosition: 0,
            margin: 0,
            responsive: {
                0: {
                    items: 1
                },
                768: {
                    items: 1
                }
            },
            nav: true,
            loop: false,
            rewind: true,
            dots: true,
            dotsSpeed: 1000,
            slideBy: 1,
            autoplay: true,
            autoplayTimeout: 5000,
            autoplayHoverPause: true,
            autoplaySpeed: 800,
            navSpeed: 1000,
            mouseDrag: true,
            responsiveRefreshRate: 100
        });
    });
});
