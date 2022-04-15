
$(document).on('ready', function() {
    $(".mobile-icon  button").on("click", function(e){
      $("#example-one").toggleClass("open");
      $(this).toggleClass('open');
         e.stopPropagation()
      });
      $("#example-one").on("click", function(e){
         e.stopPropagation()
      });
    $(".notification-btn").on("click", function(e){
      $(".noti-popup").toggleClass("open");
         e.stopPropagation()
      });
      $(".noti-popup").on("click", function(e){
         e.stopPropagation()
    });
    $(".user-menu-btn").on("click", function(e){
      $(".header-user-menu").toggleClass("open");
         e.stopPropagation()
      });
      $(".header-user-menu").on("click", function(e){
         e.stopPropagation()
    });
});

$(document).on("click", function(e) {
    if ($(e.target).is("#example-one") === false) {
      $("#example-one").removeClass("open");
    }
    if ($(e.target).is(".mobile-icon  button") === false) {
      $(".mobile-icon  button").removeClass("open");
    }
    if ($(e.target).is(".noti-popup") === false) {
      $(".noti-popup").removeClass("open");
    }
    if ($(e.target).is(".header-user-menu") === false) {
      $(".header-user-menu").removeClass("open");
    }
});


/* $(document).on('ready', function() {
    var $el, leftPos, newWidth,
        $mainNav = $("#_example-one");
      $mainNav.append("<li id='magic-line'></li>");
      var $magicLine = $("#magic-line");

      if ($(".current_page_item").length > 0) {
        $magicLine
          .width($(".current_page_item").width())
          .css("left", $(".current_page_item a").position().left)
      }
    
      $magicLine
          //.width($(".current_page_item").width())
          //.css("left", $(".current_page_item a").position().left)
          .data("origLeft", $magicLine.position().left)
          .data("origWidth", $magicLine.width());
        
    $("#example-one li a").hover(function() {
        $el = $(this);
        leftPos = $el.position().left;
        newWidth = $el.parent().width();
          $magicLine.stop().animate({
              left: leftPos,
              width: newWidth
          });
    }, function() {
        $magicLine.stop().animate({
            left: $magicLine.data("origLeft"),
            width: $magicLine.data("origWidth")
        });   
    });
});*/


new WOW().init();

/* ========================================== 
scrollTop() >= 100
Should be equal the the height of the header
========================================== */

$(window).scroll(function(){
    if ($(window).scrollTop() >= 180) {
        $('.header-area').addClass('fixed-header');
        $('header').parent('body').addClass('fixed');
    }
    else {
        $('.header-area').removeClass('fixed-header');
        $('header').parent('body').removeClass('fixed');
    }
});




$(document).ready(function() {
    $('.minus').on("click", function () {
      var $input = $(this).parent().find('input');
      var count = parseInt($input.val()) - 1;
      count = count < 1 ? 1 : count;
      $input.val(count);
      $input.change();
      $('#peepid').html('<strong>'+count+' People</strong>');
      return false;
    });
    $('.plus').on("click", function () {
      var $input = $(this).parent().find('input');
      var count = parseInt($input.val()) + 1;
      $input.val(count);
      $input.change();
      $('#peepid').html('<strong>'+count+' People</strong>');
      return false;
    });
});
