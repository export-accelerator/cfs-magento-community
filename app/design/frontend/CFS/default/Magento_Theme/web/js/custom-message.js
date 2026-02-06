require(['jquery'], function($){
    $(document).on('click', '.customClose', function() {
        $(this).parent().parent().remove();
    })
    $(".showcart").on("click", function() {
        $("body").addClass("fixed");
    });
    $(".algolia-search-input").on("click", function() {
        $(".algolia-search-input").toggleClass("open");
    });
    $(window).scroll(function() {
        if ($(this).scrollTop() > 65){  
            $('.page-header').addClass("sticky");
          }
          else{
            $('.page-header').removeClass("sticky");
        }
        if ($(this).scrollTop() > 55) {
            $('.page-header').addClass("mob");
          }
          else{
            $('.page-header').removeClass("mob");
        }
    });
});
