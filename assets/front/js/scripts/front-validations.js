
"use strict";
//setLanguage
function setLanguage(language_slug){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'/backoffice/lang_loader/setLanguage',
        data : {'language_slug':language_slug},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            location.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
//logout
function logout(){
  jQuery.ajax({
        type : "POST",
        url : BASEURL+'home/logout',
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            location.reload();
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
// click on notification icon
$(".notification-btn").on("click", function(e){
  jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+'home/unreadNotifications',
    success: function(response) {
      //$('.notification_count').html(0);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
    }
  });
});
// submit forgot password form
$("#form_front_forgotpass").on("submit", function(event) { 
    event.preventDefault();
    jQuery.ajax({
        type : "POST",
        dataType :"json",
        url : BASEURL+'home/forgot_password',
        data : {'email_forgot':$('#email_forgot').val(), 'forgot_submit_page':$('#forgot_submit_page').val() },
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) { 
            $('#forgot_error').hide();
            $('#forgot_success').hide();
             $('#quotes-main-loader').hide();
            if (response) {
	            if (response.forgot_error != '') { 
	                $('#forgot_error').html(response.forgot_error);
	                $('#forgot_success').hide();
	                $('#forgot_error').show();
	            }
	            if (response.forgot_success != '') { 
	                $('#forgot_success').html(response.forgot_success);
	                $('#forgot_error').hide();
	                $('#forgot_success').show();
	            }
            }
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {    
            console.log(XMLHttpRequest);       
            alert(errorThrown);
        }
    });
});
// submit forgot password form hidden
$('#forgot-pass-modal').on('hidden.bs.modal', function (e) {
  $(this).find("input[type=number]").val('').end();
  $('#form_front_forgotpass').validate().resetForm();
  $('#forgot_success').text('');
  $('#forgot_error').text('');
  $('#forgot_success').hide();
  $('#forgot_error').hide();
});

// get footer notifications
jQuery(document).ready(function() {
    $('.quick-searches-slider').owlCarousel({
      loop: true,
      margin: 20,
      nav: true,
      autoplay: true,
      autoplayTimeout:3500,
      navSpeed:1300,
      touchDrag:true,
      slideBy:2,
      autoplaySpeed:1300,
      autoplayHoverPause: true,
      navContainer: '#customNav',
      responsive: {
        0: {
          items: 2,
          margin: 15
        },
        600: {
          items: 3,
          margin: 20
        },
        1000: {
          items: 5,
          margin: 20
        },
         1300: {
          items: 6,
          margin: 20
        },
        1550: {
          items: 7
        }
      }
    });
    $('.best-offers-slider').owlCarousel({
      loop: true,
      margin: 20,
      nav: true,
      autoplay: true,
      autoplayTimeout:3500,
      navSpeed:1300,
      autoplaySpeed:1300,
      autoplayHoverPause: true,
      navContainer: '#customNav2',
      responsive: {
        0: {
          items: 2,
          margin: 10
        },
        600: {
          items: 3,
          margin: 20
        },
       
        1550:{
           items: 4
        }
      }
    });
    // set interval to get notification
    var i = setInterval(function(){
    jQuery.ajax({
      type : "POST",
      dataType : "html",
      async: false,
      url : BASEURL+'home/getNotifications',
      success: function(response) {
        $('#notifications_list').html(response);
      },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
      }
    });
    },10000);
});
// menu filter function
function menuFilter(content_id){  
  var food = '';
  var price = '';
  var searchDish = $('#search_dish').val();
  if ($('input[name="filter_food"]:checked').val() == "filter_veg") {
    food = "veg";
  }
  if ($('input[name="filter_food"]:checked').val() == "filter_non_veg") {
    food = "non_veg";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_high_price") {
    price = "high";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_low_price") {
    price = "low";
  }
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/ajax_restaurant_details',
    data : {"content_id":content_id,"food":food,"price":price,"searchDish":searchDish},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#quotes-main-loader').hide();
      $('#res_detail_content').html(response);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// decrease the menu quantity
function minusQuantity(restaurant_id,menu_id,cart_key){
  customItemCount(menu_id,restaurant_id,'minus',cart_key);
}
// increase the menu quantity
function plusQuantity(restaurant_id,menu_id,cart_key){
  customItemCount(menu_id,restaurant_id,'plus',cart_key);
}
// custom item count
function customItemCount(entity_id,restaurant_id,action,cart_key){
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'cart/customItemCount',
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"action":action,"cart_key":cart_key,'is_main_cart':'no'},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#your_cart').html(response.cart);
      if (response.added == 0) {
        $('.addtocart-'+entity_id).html(ADD);
        $('.addtocart-'+entity_id).removeClass('added');
        $('.addtocart-'+entity_id).addClass('add');
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      alert(errorThrown);
    }
    });
}
// check cart restaurant before adding menu item
function checkCartRestaurant(entity_id,restaurant_id,is_addon,item_id) {
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkCartRestaurant',
    data : {"restaurant_id":restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == 0) {
        // another restaurant
        $('#rest_entity_id').val(entity_id);
        $('#rest_restaurant_id').val(restaurant_id);
        $('#rest_is_addon').val(is_addon);
        $('#item_id').val(item_id);
        $('#anotherRestModal').modal('show');
      }
      if (response == 1) {
        // same restaurant
        if (is_addon == '') { // When no addon
          AddToCart(entity_id,restaurant_id,item_id);
        }
        else // When there is addon
        {
          checkMenuItem(entity_id,restaurant_id,item_id);
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// confirm to add menu item
function ConfirmCartRestaurant(){
  var entity_id = $('#rest_entity_id').val();
  var restaurant_id = $('#rest_restaurant_id').val();
  var is_addon = $('#rest_is_addon').val();
  var item_id = $('#item_id').val();
  var restaurant = $('input[name="addNewRestaurant"]:checked').val();
  $('#anotherRestModal').modal('hide');
  if (restaurant == "discardOld") {
    jQuery.ajax({
      type : "POST",
      url : BASEURL+'cart/emptyCart',
      data : {"entity_id":entity_id,'restaurant_id':restaurant_id},
      success: function(response) { 
        if (is_addon == '') {
          AddToCart(entity_id,restaurant_id,item_id);
        }
        else
        {
          checkMenuItem(entity_id,restaurant_id,item_id);
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {
        alert(errorThrown);
      }
      });
  }
  return false;
}

function PreOrder() {
  var pre_order_date = $('#delivery_val').text();
  var order_mode = "";
  if((pre_order_date == "24H Delivery" || pre_order_date == "Livraison en 24H") &&  $('#deliver_24').prop('checked')) {
    order_mode = "delivery_24";
    pre_order_date = null;
  }

  jQuery.ajax({
    type: "POST",
    url: BASEURL+'cart/storePreOrder',
    data: {"pre_order_date": pre_order_date, "order_mode": order_mode},
    success: function(response) {
      console.log(response);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
    }
  });
}
// add to cart
function AddToCart(entity_id,restaurant_id,item_id){  
  var action;
  if ($("#addpackage-"+entity_id).hasClass('inpackage')) {
    action = "remove";
  }
  else
  {
    action = "add";
  }

  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/addToCart',
    data : {"menu_item_id":entity_id,'restaurant_id':restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#your_cart').html(response);
      $('.'+item_id).html(ADDED);
      $('.'+item_id).removeClass('add');
      $('.'+item_id).addClass('added');
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
  return false;
}
// check menu item availability
function checkMenuItem(entity_id,restaurant_id,item_id){
  // check the item in cart if it's already added
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'cart/checkMenuItem' ,
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == 1) {
        $('#con_entity_id').val(entity_id);
        $('#con_restaurant_id').val(restaurant_id);
        $('#con_item_id').val(item_id);
        $('#myconfirmModal').modal('show');
      }
      else
      {
        customMenu(entity_id,restaurant_id,item_id);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(errorThrown);
      alert(errorThrown);
    }
    });
}
// confirm to add to cart
function ConfirmCartAdd(){
  var entity_id = $('#con_entity_id').val();
  var restaurant_id = $('#con_restaurant_id').val();
  var item_id = $('#con_item_id').val();
  var cart = $('input[name="addedToCart"]:checked').val();
  $('#myconfirmModal').modal('hide');
  if (cart == "increaseitem") {
    customItemCount(entity_id,restaurant_id,'plus','');
  }
  else
  {
    customMenu(entity_id,restaurant_id,item_id);
  }
  return false;
}
// custom menu page
function customMenu(entity_id,restaurant_id,item_id){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/getCustomAddOns',
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#myModal').html(response);
      $('#myModal').modal('show');
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}
// search the users dishes
function searchMenuDishes(restaurant_id) {
  var searchDish = $('#search_dish').val();
  var food = '';
  var price = '';
  if ($('input[name="filter_food"]:checked').val() == "filter_veg") {
    food = "veg";
  }
  if ($('input[name="filter_food"]:checked').val() == "filter_non_veg") {
    food = "non_veg";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_high_price") {
    price = "high";
  }
  if ($('input[name="filter_price"]:checked').val() == "filter_low_price") {
    price = "low";
  }
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'restaurant/getResturantsDish',
    data : {'restaurant_id':restaurant_id,'searchDish':searchDish,"food":food,"price":price},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#details_content').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}

var geocoder = new google.maps.Geocoder();
// CUSTOM GEOCODE
function getGeocode(search){
  return jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'v1/gmap_api/geocode',
    data : {'search':search}
    });
}
function postGeocode(latitude, longitude, address){
  return jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'v1/gmap_api/storegeocode',
    data : {'latitude':latitude,'longitude':longitude,'address':address},
    });
}

// get address from lat long
function getAddress(latitude,longitude,page){
  jQuery.ajax({
    type : "POST",
    dataType :"json",
    url : BASEURL+'home/getUserAddress',
    data : {'latitude':latitude,'longitude':longitude,'page':page},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      if (page == 'restaurant_details') {
        $('#delivery_address').val(response);
      }
      else
      {
        $('#address').val(response);
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);
      alert(errorThrown);
    }
    });
}
// search restaurant menu
function menuSearch(category_id){
  if ($('#checkbox-option-'+category_id+'').is(':checked')) {
    $('.check-menu').prop("checked", false);
    $('#checkbox-option-'+category_id+'').prop("checked", true);
    console.log($('#category-'+category_id+'').offset().top);
    $('html, body').animate({
          scrollTop: $('#category-'+category_id+'').offset().top - 200
      }, 2000);
  }
}
// autocomplete function
var autocomplete;
function initAutocomplete(id) {
      autocomplete = new google.maps.places.Autocomplete(
      document.getElementById(id), {
          types: ['geocode'] //'geocode','address','establishment','regions','cities'
      });
      autocomplete.setComponentRestrictions(
        {'country': ['mg']});
      autocomplete.setFields(['address_component', 'formatted_address']);
      
      autocomplete.addListener('place_changed', function() {
        var address = document.getElementById("add_address_area").value;
        var test = /([a-zA-Z0-9]+)\,[\s]+Antananarivo, Madagascar/i.test(address);
        if(!test) {
          // $('#error-quarter').css('display', 'inherit');
        }else {
          $('#error-quarter').css('display', 'none'); 
         }
      });
}

//get restaurant location function 
function geolocate(page, cart) {
  return;
  if (navigator.geolocation) {
    navigator.geolocation.getCurrentPosition(function(position) {
    var geolocation = {
      lat: position.coords.latitude,
      lng: position.coords.longitude
    };
    var circle = new google.maps.Circle({
      center: geolocation, radius: position.coords.accuracy
    });
    autocomplete.setBounds(circle.getBounds());
    if (page == "order_food") {
      $('#latitude').val(position.coords.latitude); 
      $('#longitude').val(position.coords.longitude); 
    }

    if(page == "checkout") {
      $('#add_latitude').val(position.coords.latitude); 
      $('#add_longitude').val(position.coords.longitude); 
      if(typeof handleActionMap == "function") {
        handleActionMap(position.coords.latitude, position.coords.longitude, cart);
      }
    }
    });
  }
}
// get location for every page from id
function getLocation(page) {
  if (navigator.geolocation) {
    if (page == 'restaurant_details') {
      navigator.geolocation.getCurrentPosition(showPosition,locationFail);
    }
    else if (page == 'home_page') {
      navigator.geolocation.getCurrentPosition(showPositionHome,locationFailHome);
    }
    else if (page == 'order_food') {
      navigator.geolocation.getCurrentPosition(showPositionFood,locationFailFood);
    }
    else if (page == 'my_profile') {
      navigator.geolocation.getCurrentPosition(showPositionProfile,locationFailProfile);
    }
    
  }
}
function getSearchedLocation(searched_lat,searched_long,searched_address,page){
  if (page == "home_page") { 
    $('#address').val(searched_address);
    getAddress(searched_lat,searched_long,'');
    for(var id in STORE_TYPES) {
      // getPopularResturants(searched_lat,searched_long,'', STORE_TYPES[id]);
    }

  }
  else if (page == "order_food") {
    $('#latitude').val(searched_lat); 
    $('#longitude').val(searched_long);  
    $('#address').val(searched_address);
    getAddress(searched_lat,searched_long,'');
    getFavouriteResturants('');
  }
}
// restaurant details functions
function showPosition(position) {
  getAddress(position.coords.latitude,position.coords.longitude,'restaurant_details');
}
function locationFail() {
  // getAddress(23.0751887,72.52568870000005,'restaurant_details');
  getAddress(-18.8876653,47.4423024,'restaurant_details');
}
// home page functions
function showPositionHome(position) { 
  getAddress(position.coords.latitude,position.coords.longitude,'');
  for(var id in STORE_TYPES) {
    // getPopularResturants(position.coords.latitude,position.coords.longitude,'', STORE_TYPES[id]);
  }
}
function locationFailHome() {
  // getAddress(23.0751887,72.52568870000005,'');
  getAddress(-18.8876653,47.4423024,'restaurant_details');
  // getPopularResturants(23.0751887,72.52568870000005,'');
  for(var id in STORE_TYPES) {
    // getPopularResturants(-18.8876653,47.4423024,'', STORE_TYPES[id]);
  }
}
// js location function for order Food page
function showPositionFood(position) {
  $('#latitude').val(position.coords.latitude); 
  $('#longitude').val(position.coords.longitude); 
  getAddress(position.coords.latitude,position.coords.longitude,'');
    getFavouriteResturants('');
}
function locationFailFood() {
  // $('#latitude').val(23.0751887); 
  $('#latitude').val(-18.8876653); 
  // $('#longitude').val(72.52568870000005); 
  $('#longitude').val(47.4423024); 
  getAddress(-18.8876653,47.4423024,'');
  getFavouriteResturants('');
}
// my profile 
function showPositionProfile(position) {
    setMarker(position.coords.latitude,position.coords.longitude);
}
function locationFailProfile() {
    setMarker(-18.8876653,47.4423024);
}

function fillInAddress(page) {
  if (page == "home_page") {
      $('html, body').animate({
            scrollTop: $(`#choose-service`).offset().top - 130
        }, 2000);
  }
  else if (page == "order_food") {
    getFavouriteResturants('scroll');
  }
}
// home page js functions
function _fillInAddress(page) {  
  // Get the place details from the autocomplete object.
    // var place = autocomplete.getPlace();
    // var geocoder = new google.maps.Geocoder();
    var address = document.getElementById("address").value;
    getGeocode(address).done(function(res) {
      if(res.results){
        if (page == "home_page") {
          //if (scroll == "scroll") {
            $('html, body').animate({
                  scrollTop: $(`#choose-service`).offset().top - 130
              }, 2000);
          // }
          for(var id in STORE_TYPES) {
            // getPopularResturants(results[0].geometry.location.lat(),results[0].geometry.location.lng(),'scroll', STORE_TYPES[id]);
          }
        }
        else if (page == "order_food") {
          $('#latitude').val(res.results.latitude); 
          $('#longitude').val(res.results.longitude); 
          getFavouriteResturants('scroll');
        }
        addLatLong(res.results.latitude,res.results.longitude,address);
      }
    });

    /* geocoder.geocode( { 'address': address}, function(results, status) {
    if (status == google.maps.GeocoderStatus.OK) {
      if (page == "home_page") {
        //if (scroll == "scroll") {
          $('html, body').animate({
                scrollTop: $(`#choose-service`).offset().top - 130
            }, 2000);
        // }
        for(var id in STORE_TYPES) {
          // getPopularResturants(results[0].geometry.location.lat(),results[0].geometry.location.lng(),'scroll', STORE_TYPES[id]);
        }
      }
      else if (page == "order_food") {
        $('#latitude').val(results[0].geometry.location.lat()); 
        $('#longitude').val(results[0].geometry.location.lng()); 
        getFavouriteResturants('scroll');
      }
      addLatLong(results[0].geometry.location.lat(),results[0].geometry.location.lng(),address);
    } 
    }); */
}
// store the lat long in session
function addLatLong(lat,long,address){
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'home/addLatLong',
    data : {'lat':lat,'long':long,'address':address},
    success: function(response) {
      console.log('addlatlongres',response);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
} 
// quick search menu items
function quickSearch(value){
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'home/quickCategorySearch',
    data : {'category_id':value},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#popular-restaurants').html(response);
      $('html, body').animate({
            scrollTop: $("#popular-restaurants").offset().top
        }, 2000);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get the popular restaurants
function getPopularResturants(latitude,longitude,scroll, typeId=1){
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'home/getPopularResturants',
    data : {'latitude':latitude,'longitude':longitude,'store_type_id':typeId},
    beforeSend: function(){
        // $('#quotes-main-loader').show();
      $(`#popular-restaurants-${typeId} .rest-box-row.main-rest-box-row`).html(`<div class="nearby-loader"><img src="${BASEURL}assets/admin/img/ajax-loader.gif"/></div>`);
    },
    success: function(response) {
      $(`#popular-restaurants-${typeId}`).html(response);
      /*if (scroll == "scroll") {
        $('html, body').animate({
              scrollTop: $(`#popular-restaurants-${typeId}`).offset().top - 730
          }, 2000);
      }*/
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      alert(errorThrown);
    }
    });
}

var FILTER = [];

// get the favourite restaurants
function getFavouriteResturantsFilter(elem){  
  var food_veg = ($('#food_veg').is(":checked"))?1:0;
  var food_non_veg = ($('#food_non_veg').is(":checked"))?1:0;
  var filter = ($(elem).is(":checked")) ? $(elem).val() : false;
  if(!filter) {
    var i = 0;
    FILTER.forEach(function(v, ind){
      if(v == $(elem).val()) {
        i = ind;
      }
    });
    FILTER.splice(i, 1);
  }
  if(filter) {
    FILTER.push(filter);
  }
  var store_filter = FILTER;
  var resdishes = $('#resdishes').val();
  var latitude = $('#latitude').val();
  var longitude = $('#longitude').val();
  var minimum_range = $('#minimum_range').val();
  var maximum_range = $('#maximum_range').val();
  var page = page ? page : 0;
  var store_type = $('#store_type').val();
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url: BASEURL+'restaurant/ajax_restaurants/'+page,
    data : {'latitude':latitude,'longitude':longitude,'resdishes':resdishes,'page':page,'minimum_range':minimum_range,'maximum_range':maximum_range,'food_veg':food_veg,'food_non_veg':food_non_veg,'store_type' : store_type, 'store_filter' : store_filter},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#order_from_restaurants').html(response);
      if (scroll == "scroll") {
        $('html, body').animate({
              scrollTop: $("#order_from_restaurants").offset().top - 250
          }, 2000);
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}

// get the favourite restaurants
function getFavouriteResturants(scroll){  
  var food_veg = ($('#food_veg').is(":checked"))?1:0;
  var food_non_veg = ($('#food_non_veg').is(":checked"))?1:0;
  var resdishes = $('#resdishes').val();
  var latitude = $('#latitude').val();
  var longitude = $('#longitude').val();
  var minimum_range = $('#minimum_range').val();
  var maximum_range = $('#maximum_range').val();
  var page = page ? page : 0;
  var store_type = $('#store_type').val();
  var store_filter = FILTER;
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url: BASEURL+'restaurant/ajax_restaurants/'+page,
    data : {'latitude':latitude,'longitude':longitude,'resdishes':resdishes,'page':page,'minimum_range':minimum_range,'maximum_range':maximum_range,'food_veg':food_veg,'food_non_veg':food_non_veg, 'store_type' : store_type,  'store_filter' : store_filter},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) { 
      $('#order_from_restaurants').html(response);
      if (scroll == "scroll") {
        $('html, body').animate({
              scrollTop: $("#order_from_restaurants").offset().top - 250
          }, 2000);
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// recipe page
function searchRecipes(){
  var recipe = $('#recipe').val();
  jQuery.ajax({
    type : "POST",
    dataType :"html",
    url : BASEURL+'recipe/ajax_recipies',
    data : {'recipe':recipe,'page':''},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#sort_recipies').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
$('#recipe').keypress(function(event){
    var keycode = (event.keyCode ? event.keyCode : event.which);
    if(keycode == '13'){
        event.preventDefault();
    }
});
// my profile page js functions
function geocodePosition(pos) {
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      marker.formatted_address = responses[0].formatted_address;
    } else {
      marker.formatted_address = 'Cannot determine address at this location.';
    }
    infowindow.setContent(marker.formatted_address + "<br>coordinates: " + marker.getPosition().toUrlValue(6));
    infowindow.open(map, marker);
    $('#address_field').val(marker.formatted_address);
  });
}
// get the marker for the map
function getMarker(address_value){
    // var geocoder = new google.maps.Geocoder();
    if (address_value != '') {
        var address = address_value;
    }
    else
    {
        var address = document.getElementById("add_address_area").value;
    }
    getGeocode(address).done(function (re) {
      if(res.result)
      {
        var myLatlng = new google.maps.LatLng(res.results.latitude,res.results.longitude);
            marker.setPosition(myLatlng);
            if (address_value != '') {
                $('#latitude').val(res.results.latitude);
                $('#longitude').val(res-results.longitude);
          }
      }
    });
    /* geocoder.geocode( { 'address': address}, function(results, status) {
        if (status == google.maps.GeocoderStatus.OK) {
            //set the map's marker
            var myLatlng = new google.maps.LatLng(results[0].geometry.location.lat(),results[0].geometry.location.lng());
            marker.setPosition(myLatlng);
            if (address_value != '') {
                $('#latitude').val(results[0].geometry.location.lat());
                $('#longitude').val(results[0].geometry.location.lng());
            }
        }
    }); */
    return false;
}
// set marker on the map
function setMarker(latitude,longitude){
    var myLatlng = new google.maps.LatLng(latitude,longitude);
    marker.setPosition(myLatlng);
    $('#latitude').val(latitude);
    $('#longitude').val(longitude);
}
// add active class
function addActiveClass(value){
    $('.tabs').removeClass('active');
    $('#'+value).addClass('active');
}
// check email validation
function isEmail(email) {
    var regex = /^([a-zA-Z0-9_.+-])+\@(([a-zA-Z0-9-])+\.)+([a-zA-Z0-9]{2,4})+$/;
    return regex.test(email);
}
// check password validation
function customPassword(password) {
    return true;
    var regex = /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/;
    return regex.test(password);
}
// check digits validation
function digitCheck(string) {
    // /^([0-9]{10})|(\([0-9]{3}\)\s+[0-9]{3}\-[0-9]{4})$/
    var regex = /^\d{6,15}$/;
    return regex.test(string);
}
// form my profile validation on submit
$( "#form_my_profile" ).on("submit", function( event ) {   
    if ($('#first_name').val() != '' && $('#email').val() != '' && isEmail($('#email').val()) && $('#phone_number').val() != '' && digitCheck($('#phone_number').val()) && (($('#password').val() != '' && customPassword($('#password').val()) && $('#confirm_password').val() != '' && $('#password').val() == $('#confirm_password').val()) || ($('#password').val() == '' && $('#confirm_password').val() == ''))) 
    {  
        var formData = new FormData($("#form_my_profile")[0]);
        formData.append('submit_profile', 'Save');
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'myprofile',
            data : formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                if (response == "success") {
                    location.reload();
                }
                else
                {
                    $('#quotes-main-loader').hide();
                    $('#error-msg').html(response);
                    $('#error-msg').show();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#quotes-main-loader').hide();
                alert(errorThrown);
            }
        });
    }
    event.preventDefault(); 
});
// form my address validation on submit
$( "#form_add_address" ).on("submit", function( event ) { 
    event.preventDefault();
    if ($('#address_field').val() != '') 
    {
        var formData = new FormData($("#form_add_address")[0]);
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'myprofile/addAddress',
            data : formData,
            cache: false,
            processData: false,
            contentType: false,
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                if (response == "success") {
                    window.location.href = BASEURL+"myprofile/view-my-addresses";
                }
                else
                {
                    $('#quotes-main-loader').hide();
                    $('#add-error-msg').html(response);
                    $('#add-error-msg').show();
                }
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                $('#quotes-main-loader').hide();
                alert(errorThrown);
            }
        });
    }
});
// form my addresses on hidden
$('#add-address').on('hidden.bs.modal', function (e) {
    $('#add_entity_id').val('');
    $('#address_field').val('');
    $('#landmark').val('');
    $('#form_add_address').validate().resetForm();
    $('#add-error-msg').text('');
    $('#submit_address').val(ADD);
    $('#address-form-title').html(ADD);
    getLocation('my_profile');
});
// form my profile on hidden
$('#edit-profile').on('hidden.bs.modal', function (e) {
    $('#form_my_profile').validate().resetForm();
    $('#error-msg').text('');
    $('#error-msg').hide();
    $("#form_my_profile")[0].reset();
});
// get more orders
function moreOrders(order_flag){
    if (order_flag == "process") {
        $('#all_current_orders').show();
        $('#more_in_process_orders').hide();
    }
    if (order_flag == "past") {
        $('#all_past_orders').show();
        $('#more_past_orders').hide();
    }
}
// get more events
function moreEvents(order_flag){
    if (order_flag == "upcoming") {
        $('#all_upcoming_events').show();
        $('#more_upcoming_events').hide();
    }
    if (order_flag == "past") {
        $('#all_past_events').show();
        $('#more_past_events').hide();
    }
}
// get orders details
function order_details(order_id){
    if (order_id) {
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+'myprofile/getOrderDetails',
            data : {"order_id":order_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#order-details').html(response);
                $('#order-details').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// track orders
function track_order(order_id){
    if (order_id) {
        jQuery.ajax({
            type : "POST",
            url : BASEURL+'order',
            data : {"order_id":order_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// get booking details
function booking_details(event_id){
    if (event_id) {
        jQuery.ajax({
            type : "POST",
            dataType : "html",
            url : BASEURL+ 'myprofile/getBookingDetails',
            data : {"event_id":event_id},
            beforeSend: function(){
                $('#quotes-main-loader').show();
            },
            success: function(response) {
                $('#quotes-main-loader').hide();
                $('#booking-details').html(response);
                $('#booking-details').modal('show');
            },
            error: function(XMLHttpRequest, textStatus, errorThrown) {
                alert(errorThrown);
            }
        });
    }
}
// edit address
function editAddress(address_id){
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'myprofile/getEditAddress',
        data : {"address_id":address_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            var address = JSON.parse(response);
            console.log(address);
            $('#user_entity_id').val(address.user_entity_id);
            $('#add_entity_id').val(address.address_id);
            $('#address_field').val(address.address);
            $('#add_address_area').val(address.search_area);
            $('#latitude').val(address.latitude);
            $('#longitude').val(address.longitude);
            $('#landmark').val(address.landmark);
            $('#submit_address').val('Edit');
            $('#address-form-title').html('Edit');
            setMarker(address.latitude,address.longitude);
            $('#quotes-main-loader').hide();
            $('#add-address').modal('show');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            alert(errorThrown);
        }
    });
}
// show delete address popup
function showDeleteAddress(address_id){
    $('#delete_address_id').val(address_id);
    $('#delete-address').modal('show');
}
// delete address
function deleteAddress(){
    var address_id = $('#delete_address_id').val();
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+ 'myprofile/ajaxDeleteAddress' ,
        data : {'address_id':address_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            window.location.href = BASEURL+"myprofile/view-my-addresses";
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#quotes-main-loader').hide();
            alert(errorThrown);
        }
    });
}
// show set main address popup
function showMainAddress(address_id){
    $('#main_address_id').val(address_id);
    $('#main-address').modal('show');
}
// set main address 
function setMainAddress(){
    var address_id = $('#main_address_id').val();
    jQuery.ajax({
        type : "POST",
        dataType : "html",
        url : BASEURL+'myprofile/ajaxSetAddress',
        data : {'address_id':address_id},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
            window.location.href = BASEURL+"myprofile/view-my-addresses";
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {
            $('#quotes-main-loader').hide();
            alert(errorThrown);
        }
    });
}
/*event booking details*/
// get people value
function getPeople(value){
  $('#peepid').html('<strong>'+value+' People</strong>');
}
// show all the reviews
function showAllReviews(){
  $('#all_reviews').show();
  $('#review_button').hide();
}
// form check availability submit
$("#check_event_availability").on("submit", function(event) {
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/checkEventAvailability',
    data : $('#check_event_availability').serialize(),
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      if (response == "success") {
        $('#booking-available').modal('show');
      }
      else if (response == "fail")  {
        $('#booking-not-available').modal('show');
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    }); 
    return false;
});
// add package for event booking
function AddPackage(entity_id){  
  var action;
  if ($("#addpackage-"+entity_id).hasClass('inpackage')) {
    action = "remove";
  }
  else
  {
    action = "add";
  }
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/add_package',
    data : {"entity_id":entity_id,"action":action},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == "success") {
        if ($("#addpackage-"+entity_id).hasClass('inpackage')) {
          $("#addpackage-"+entity_id).removeClass("inpackage");
          $(".addpackage").html("Add");
          $("#addpackage-"+entity_id).html("Add");
        }
        else
        {
          $(".addpackage").removeClass("inpackage");
          $("#addpackage-"+entity_id).addClass("inpackage");
          $(".addpackage").html("Add");
          $("#addpackage-"+entity_id).html("Added");
        }
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    }); 
  return false;
}
// confirm event booking
function confirmBooking(){
  jQuery.ajax({
    type : "POST",
    url : BASEURL+'restaurant/bookEvent',
    data : $('#check_event_availability').serialize(),
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#quotes-main-loader').hide();
      if (response == "success") {
        $('#booking-confirmation').modal('show');
      }
      else {
        $('#booking-not-available').modal('show');
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    }); 
    return false;
}
/*event booking details js end*/

/*event booking page js*/
function searchEvents(){
    var searchEvent = $('#searchEvent').val();
    jQuery.ajax({
      type : "POST",
      dataType :"html",
      url : BASEURL+"restaurant/ajax_events",
      data : {'searchEvent':searchEvent,'page':''},
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#sort_events').html(response);
        $('#quotes-main-loader').hide();
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
      });
  }
  $('#searchEvent').keypress(function(event){
      var keycode = (event.keyCode ? event.keyCode : event.which);
      if(keycode == '13'){
          event.preventDefault();
      }
  });
/*event booking page js ends*/

/*checkout page*/
function _getLatLongCb(latitude, longitude, address, cart_total, store = false){
  // Get Delivery Charge
  getDeliveryCharges(latitude,longitude,"get",cart_total);
  $('#add_latitude').val(latitude);
  $('#add_longitude').val(longitude);
  if(typeof handleActionMap == "function") {
    handleActionMap(latitude, longitude, cart_total);
  }
  if(store){
    postGeocode(latitude, longitude, address);
  }
}
//get lat long
function getLatLong(cart_total){  
    setTimeout(function() {
      var address = document.getElementById("add_address_area").value;
      getGeocode(address).done(function(res) {
        if(res.results) {
         _getLatLongCb(res.results.latitude, res.results.longitude, address, cart_total);
        }else{
          console.log("getLatLong");
          geocoder.geocode( { 'address': address}, function(results, status) {
            if (status == google.maps.GeocoderStatus.OK) {
              _getLatLongCb(results[0].geometry.location.lat(),results[0].geometry.location.lng(),address,cart_total, true);
            } 
            });
        }
      });
    }, 900);
}

function geocodePositionAdr(pos, resolve) {
  // var geocoder = new google.maps.Geocoder();
  console.log("pos");
  geocoder.geocode({
    latLng: pos
  }, function(responses) {
    if (responses && responses.length > 0) {
      resolve(responses[1].formatted_address);
    } else {
      resolve(false);
      // return 'Cannot determine address at this location.';
    }
  });
}

// get delivery charges from the address
function getAddLatLong(address_id,cart_total){
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'checkout/getAddressLatLng',
    data : {"entity_id":address_id},
    success: function(response) {
      getDeliveryCharges(response.latitude,response.longitude,"get",cart_total);
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}

$('#add_address').focus(function(element)
{
  var address = document.getElementById("add_address_area").value;
  if(address) {
    var test = /([a-zA-Z0-9]+)\,[\s]+Antananarivo, Madagascar/i.test(address);
    if(!test) {
      //$('#error-quarter').css('display', 'inherit');
    }else {
      $('#error-quarter').css('display', 'none'); 
     }
  }
});

$.validator.addMethod("add_address_area",function(value,element)
{
  //return this.optional(element) || /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i.test(value);
  return true;
  return this.optional(element) || /([a-zA-Z0-9]+)\,[\s]+Antananarivo, Madagascar/i.test(value);
},"Ex: Analakely, Antananarive, Madagascar");

// get delivery charges
function getDeliveryCharges(latitude,longitude,action,cart_total){
  var mode_24 = $('#delivery_24').prop('checked');
  jQuery.ajax({
    type : "POST",
    dataType : "json",
    url : BASEURL+'checkout/getDeliveryCharges',
    data : {"latitude":latitude,"longitude":longitude,"action":action, 'mode_24' : mode_24},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response.ajax_order_summary);
      if (action == "get") {
        if (response.check == '' || response.check == null) {
            $('#delivery-not-avaliable').modal('show');
            $("#submit_order").attr("disabled", true);
        }
        else
        {
          $("#submit_order").attr("disabled", false);
        }
      }
      $('#quotes-main-loader').hide();
      getCoupons(cart_total,'delivery');
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);           
      alert(errorThrown);
    }
    });
}

function handleDisplay(id, val) {
  const elem = document.getElementById(id);
  if(elem) {
    elem.style.display = val;
  }
}

// show delivery options
function showDelivery(cart_total_price){ 
  handleDisplay('delivery-form', 'block'); 
  initAutocomplete('add_address_area');
  handleActionMap(-18.8876653, 47.4423024, cart_total_price);
	jQuery( ".add_new_address" ).prop('required',true);
	//getCoupons(cart_total_price,'delivery');
  $('#checkout_form').validate().resetForm();
  $('#checkout_form')[0].reset();
  $("#submit_order").attr("disabled", false);
  $("#delivery").prop("checked", true);
  $('#add_address_content').hide();
  $('#your_address_content').hide();
}
// shwo delivery 24 options
function showDelivery24(cart_total_price){  
	handleDisplay('delivery-form', 'block');   
  initAutocomplete('add_address_area');
  handleActionMap(-18.8876653, 47.4423024, cart_total_price);
	jQuery( ".add_new_address" ).prop('required',true);
	// getCoupons(cart_total_price,'delivery');
  $('#checkout_form').validate().resetForm();
  $('#checkout_form')[0].reset();
  $("#submit_order").attr("disabled", false);
  $("#delivery_24").prop("checked", true);
  $('#add_address_content').hide();
  $('#your_address_content').hide();
}
// show pickup options
function showPickup(cart_total_price){  
	handleDisplay('delivery-form', 'none');
	getCoupons(cart_total_price,'pickup');
  $('#checkout_form').validate().resetForm();
  $('#checkout_form')[0].reset();
  $("#submit_order").attr("disabled", false);
  $("#pickup").prop("checked", true);
}
// remove delivery options
function removeDeliveryOptions(){
  jQuery.ajax({
    type : "POST",
    dataType : "html",
    url : BASEURL+'checkout/removeDeliveryOptions',
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get Coupons
function getCoupons(subtotal,order_mode){
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/getCoupons',
    data : {"subtotal":subtotal,"order_mode":order_mode},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response.ajax_order_summary);
      $('#your_coupons').html(response.html);
      $('#quotes-main-loader').hide();
      if (order_mode == "pickup") {
         removeDeliveryOptions();
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
    });
}
// get Coupon details
function getCouponDetails(coupon_id,subtotal,order_mode){ 
  jQuery.ajax({
    type : "POST",
    dataType : 'html',
    url : BASEURL+'checkout/addCoupon',
    data : {"coupon_id":coupon_id,"subtotal":subtotal,"order_mode":order_mode},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {
      $('#ajax_order_summary').html(response);
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {           
      alert(errorThrown);
    }
  });
}
// show address
function showAddAdress(){  
    handleDisplay('add_address_content', 'block');
    jQuery( "#add_address_area" ).prop('required',true);    
    jQuery( "#add_address" ).prop('required',true);    
    jQuery( "#landmark" ).prop('required',true);    
    jQuery( "#zipcode" ).prop('required',true);    
    jQuery( "#city" ).prop('required',true);  
    handleDisplay('your_address_content', 'none');
}
// show your already added address
function showYourAdress(){  
  handleDisplay('add_address_content', 'none');
  handleDisplay('your_address_content', 'block');
  jQuery( "#your_address" ).prop('required',true);
}
// show registration form
function showregister(){
  $('#form_front_login_checkout').validate().resetForm();
  $('#form_front_registration_checkout').validate().resetForm();
  $('.login-validations').html('');
  $('#login_form').hide();
  $('#signup_form').show();
}
//show login form
function showlogin(){
  $('#form_front_login_checkout').validate().resetForm();
  $('#form_front_registration_checkout').validate().resetForm();
  $('.register-validations').html('');
  $('#signup_form').hide();
  $('#login_form').show();
}
// submit checkout form
$( "#checkout_form" ).on("submit", function( event ) { 
  event.preventDefault();
  var choose_order = $("input[name='choose_order']:checked").val();
  var add_new_address = $("input[name='add_new_address']:checked").val();
  var payment_option = $("input[name='payment_option']:checked").val(); 
  if ((((choose_order == "delivery" || choose_order == "delivery_24") && ((add_new_address == "add_your_address" && $('#your_address').val() != '') || (add_new_address == "add_new_address" && $('#add_address_area').val() != '' && $('#add_address').val() != '' && $('#landmark').val() != '' && $('#zipcode').val() != '' && $('#city').val() != ''))) || choose_order == "pickup") && payment_option != '' && payment_option != undefined) 
  {   
    jQuery.ajax({
      type : "POST",
      dataType: 'json',
      url : BASEURL+'checkout/addOrder',
      data : $("#checkout_form").serialize(),
      cache: false, 
      beforeSend: function(){
        $('#quotes-main-loader').show();
      },   
      success: function(response) {
              $('#quotes-main-loader').hide();
              console.log("bizarre",response);
              if (response.result == "success") {
                $('#track_order').html(response.order_id);
                $('#order-confirmation').modal('show');
                console.log("response", response);
                // setTimeout(location.reload.bind(location), 300000);
              }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {     
        console.log(XMLHttpRequest);      
        alert(errorThrown);
      }
      });
  }
});
// check custom checkout item
function customCheckoutItemCount(entity_id,restaurant_id,action,cart_key){ 
  jQuery.ajax({
    type : "POST",
    dataType : 'json',
    url : BASEURL+'checkout/ajax_checkout',
    data : {"entity_id":entity_id,"restaurant_id":restaurant_id,"action":action,"cart_key":cart_key,'is_main_cart':'checkout'},
    beforeSend: function(){
        $('#quotes-main-loader').show();
    },
    success: function(response) {  
      $('#ajax_your_items').html(response.ajax_your_items);
      $('#ajax_order_summary').html(response.ajax_order_summary);

      if (action == "remove" && $('#total_cart_items').val() == null) { 
        $('#order_mode_content').hide();
      }
      else if (action == "minus" && $('#total_cart_items').val() == null && $('#item_count_check').val() == null) { 
        $('#order_mode_content').hide();
      }
      else
      {
        if (IS_USER_LOGIN == 1 ){
          //document.getElementById('delivery-form').style.display ='block';
        }
      }
      $('#quotes-main-loader').hide();
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {
      console.log(XMLHttpRequest);           
      alert(errorThrown);
    }
    });
}
/*checkout page js ends*/

//get item price
var totalPrice = 0;
var radiototalPrice = 0;
var checktotalPrice = 0;
function getItemPrice(id,price,is_multiple){ 
    if (is_multiple != 1) {
    $("#custom_items_form input[type=radio]:checked").each(function() {
      radiototalPrice = 0;
      var sThisVal = (this.checked ? $(this).attr("amount") : 0);
        radiototalPrice = parseFloat(radiototalPrice) + parseFloat(sThisVal);
    });
    }
    else
    {
      checktotalPrice = 0;
      $('.check_addons:checkbox:checked').each(function () { 
       var sThisVal = (this.checked ? $(this).attr("amount") : 0);
       checktotalPrice = parseFloat(checktotalPrice) + parseFloat(sThisVal);
    });
    }
    totalPrice = radiototalPrice + checktotalPrice;
    $('#totalPrice').html(totalPrice);
    $('#subTotal').val(totalPrice);
}
// get the addons to cart
function AddAddonsToCart(menu_id,item_id){ 
  var restaurant_id = $("#restaurant_id").val();
    var user_id = $("#user_id").val();
    var totalPrice = $('#subTotal').val();
    var valueArray = new Array();
    $('.check_addons:checkbox:checked').each(function () { 
      var addonValue = jQuery.parseJSON($(this).attr("addonValue"));
      var addons_category = $(this).attr("addons_category");
      var addons_category_id = $(this).attr("addons_category_id");    
      if (valueArray.length > 0) {
        jQuery.each( valueArray, function( key, value ) { 
          var new_addons_list = new Array();
          if (value.addons_category_id == addons_category_id) {
            var addonslist = value.addons_list;
            if (Array.isArray(value.addons_list) == false) {
              new_addons_list.push({
                "add_ons_id": value.addons_list.add_ons_id, 
                "add_ons_name": value.addons_list.add_ons_name,
                "add_ons_price": value.addons_list.add_ons_price
              });
            }
            else
            { 
              if (addonslist.length > 0) {
                jQuery.each(addonslist, function( key, value ) { 
                  new_addons_list.push(value);
                });
              } 
            }
            
            new_addons_list.push(addonValue);
            value.addons_list = new_addons_list;
          }
          else
          {
            valueArray.push({
              'addons_category_id':addons_category_id,
              'addons_category':addons_category,
              'addons_list':addonValue
            });
          }
        });
      }
      else
      {
        valueArray.push({
              'addons_category_id':addons_category_id,
              'addons_category':addons_category,
              'addons_list':addonValue
        });
      }
    });
    $("#custom_items_form input[type=radio][class='radio_addons']:checked").each(function() { 
      var addonValue = jQuery.parseJSON($(this).attr("addonValue"));
      var addons_category = $(this).attr("addons_category");
      var addons_category_id = $(this).attr("addons_category_id");
      var new_addons_list = new Array();
      if (valueArray.length > 0) { 
        jQuery.each( valueArray, function( key, value ) {
          if (value.addons_category_id == addons_category_id) {
            new_addons_list.push(value.addons_list);
            new_addons_list.push(addonValue);
            valueArray.splice(key, 1);
          }
        });
      }
      if (new_addons_list.length > 0) {
        addonValue = new_addons_list;
      }
      valueArray.push({
            'addons_category_id':addons_category_id,
            'addons_category':addons_category,
            'addons_list':addonValue
      });
    });
    var arr = [];
    var addons_category_id_arr = [];
      if (valueArray.length > 0) { 
        jQuery.each( valueArray, function( key, value ) { 
          var addons = value.addons_list;
          var addons_count = addons.length;
          addons_category_id_arr.push(value.addons_category_id);
          arr.push({
            'addons_category_id':value.addons_category_id,
            'key':key,
            'addons_count':(addons_count)?addons_count:0
          });
        });
      }
      var unique_addons_category = [];
      $.each(addons_category_id_arr, function(i, el){
          if($.inArray(el, unique_addons_category) === -1) unique_addons_category.push(el);
      });
      var maxval = [];
      var arrkeys = [];
      if (unique_addons_category.length > 0) {
        jQuery.each( unique_addons_category, function( key, value ) {
          var max = 0;
          var keyvalue = '';
          if (arr.length > 0) {
            jQuery.each( arr, function( arrkey, arrvalue ) {
              if (arrvalue.addons_category_id == value) {
                if(max <= arrvalue.addons_count){
                  max = arrvalue.addons_count;
                  keyvalue = arrvalue.key;
                }
              }
            });
            maxval.push({ 
              'id':value,
              'addons_count': max,
              'key': keyvalue
            });
            arrkeys.push(keyvalue);
          }
        });
      }
      var finalValueArray = [];
      // to unset the duplicate keys
      if (valueArray.length > 0) {
        jQuery.each( valueArray, function( key, value ) {
          if (arrkeys.length > 0) {
            if(jQuery.inArray(key, arrkeys) !== -1) { 
              finalValueArray.push(value);
            }
          }
          else
          {
            finalValueArray = valueArray;
          }
        });
      }

    // send addons array to cart
    if (finalValueArray.length > 0) { 
      jQuery.ajax({
        type : "POST",
        url : BASEURL+'cart/addToCart',
        data : {'menu_id':menu_id,'user_id':user_id,'restaurant_id':restaurant_id,'totalPrice':totalPrice,'add_ons_array':finalValueArray},
        beforeSend: function(){
            $('#quotes-main-loader').show();
        },
        success: function(response) {
          $('#quotes-main-loader').hide();
          $('#myModal').modal('hide');
          $('#your_cart').html(response);
          $('.'+item_id).html(ADDED);
          $('.'+item_id).removeClass('add');
          $('.'+item_id).addClass('added');
        },
        error: function(XMLHttpRequest, textStatus, errorThrown) {           
          alert(errorThrown);
        }
        });
    }
}

function addReview(restaurant_id){
  $('#reviewModal').modal('show');
}

// form check availability submit
$("#review_form").on("submit", function(event) {
  event.preventDefault();
  if ($("input[name=rating]:checked").val() != '' && $('#review_text').val() != '') {
    jQuery.ajax({
      type : "POST", 
      dataType: "html",
      url : BASEURL+'restaurant/addReview',  
      data : $('#review_form').serialize(),
      beforeSend: function(){
          $('#quotes-main-loader').show();
      },
      success: function(response) {
        $('#quotes-main-loader').hide();
        if (response == 'success') {
          location.reload();
        }
      },
      error: function(XMLHttpRequest, textStatus, errorThrown) {           
        alert(errorThrown);
      }
    });
  }
  else
  {
    return false;
  }
});