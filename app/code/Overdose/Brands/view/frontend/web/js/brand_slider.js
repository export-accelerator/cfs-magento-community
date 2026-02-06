define([
        "jquery",
        "slick"
    ], function ($) {
        "use strict";
        return function (config, element) {
            let defaultConfig = {
                infinite: true,
                speed: 300,
                slidesToShow: 4,
                slidesToScroll: 4,
                responsive: [
                    {
                        breakpoint: 1024,
                        settings: {
                            slidesToShow: 3,
                            slidesToScroll: 3,
                            infinite: true,
                        }
                    },
                    {
                        breakpoint: 768,
                        settings: {
                            slidesToShow: 2,
                            slidesToScroll: 2
                        }
                    },
                ]
            };
            $(element).slick($.extend({}, defaultConfig, config));
        }
    }
);
