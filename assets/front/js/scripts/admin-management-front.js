// Front Login Validation
jQuery("#form_front_login").validate({  
  rules: {    
    phone_number: {
      required: true,
      phoneNumber: true,
      minlength: 6,
      maxlength: 15
    },
    password: {
      required: true,
      /*passwordcustome: true*/
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});

// Review Validation
jQuery("#review_form").validate({  
  rules: { 
    rating: {
      required: true
    }, 
    review_text: {
      required: true
    }, 
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});

// Front Forgot Password Validation
jQuery("#form_front_forgotpass").validate({  
  rules: {    
    /*phone_number_forgot: {
      required: true,
      phoneNumber: true
    },*/
    email_forgot: {
      required: true,
      emailcustom: true
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});

// Front Check Event Validation
jQuery("#check_event_availability").validate({  
  rules: {    
    no_of_people: {
      required: true,
      digits: true
    }, 
    dining_time: {
      required: true,
    }, 
    booking_date: {
      required: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});


// Front Registration Validation
jQuery("#form_front_registration").validate({  
  rules: { 
    name: {
      required: true
    },   
    email: {
      required: true,
      emailcustom: true
    },   
    phone_number: {
      required: true,
      phoneNumber: true,
      minlength: 6,
      maxlength: 15
    },
    password: {
      required: true,
      passwordcustome: true
    }
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});

// Front Contact Us Validation
jQuery("#form_front_contact_us").validate({  
  rules: { 
    name: {
      required: true
    },   
    email: {
      required: true,
      emailcustom: true
    },
    message: {
      required: true,
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }  
});


// Front Contact Us Validation
jQuery("#form_my_profile").validate({  
  rules: { 
    first_name: {
      required: true
    },   
    email: {
      required: true,
      emailcustom: true
    },
    phone_number: {
      required: true,
      digits: true,
      minlength: 6,
      maxlength: 15,
      //max: 15,
    },
    password:{
      required:{
        depends: function(){
          if($('#confirm_password').val() != ''){
              return true;
          }
        }
      },
      passwordcustome:true
    },
    confirm_password:{
      required:{
        depends: function(){
          if($('#password').val() != ''){
              return true;
          }
        }
      },
      equalTo:'#password'
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }  
});

// Front Login Validation
jQuery("#form_front_login_checkout").validate({  
  rules: {    
    login_phone_number: {
      required: true,
      phoneNumber: true,
      minlength: 6,
      maxlength: 15
    },
    login_password: {
      required: true,
      /*passwordcustome: true*/
    }
  },
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }    
});

// Front Registration Validation
jQuery("#form_front_registration_checkout").validate({  
  rules: { 
    name: {
      required: true
    },   
    phone_number: {
      required: true,
      phoneNumber: true
    },
    password: {
      required: true,
      passwordcustome: true
    }
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});

// Front Registration Validation
jQuery("#checkout_form").validate({  
  rules: { 
    choose_order: {
      required: true
    }, 
    payment_option__: {
      required: true
    }, 
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
      $(placement).append(error)
    } 
    else {
      if (element.attr('id') == "add_address_area" || element.attr('id') == "add_address" || element.attr('id') == "landmark" || element.attr('id') == "zipcode" || element.attr('id') == "city" || element.attr('id') == "your_address") {
        error.insertAfter(element);
      }
      else
      {
        error.insertBefore(element);
      }
    }
  }   
});

// Front Add Address Validation
jQuery("#form_add_address").validate({  
  rules: { 
    address_field: {
      required: true
    },   
    /*landmark: {
      required: true,
    },
    zipcode: {
      required: true,
    },
    city: {
      required: true,
    }*/
  } ,
  errorElement : 'div',
  errorPlacement: function(error, element) {
    var placement = $(element).data('error');
    if (placement) {
    $(placement).append(error)
    } else {
    error.insertAfter(element);
    }
  }   
});

// admin email exist check
function checkEmailExist(email,entity_id){
  $.ajax({
    type: "POST",
    url: BASEURL+"backoffice/users/checkEmailExist",
    data: 'email=' + email +'&entity_id='+entity_id,
    cache: false,
    success: function(html) {
      if(html > 0){
        $('#EmailExist').show();
        $('#EmailExist').html("User is already exist with this email id!");        
        $(':input[type="submit"]').prop("disabled",true);
      } else {
        $('#EmailExist').html("");
        $('#EmailExist').hide();        
        $(':input[type="submit"]').prop("disabled",false);
      }
    },
    error: function(XMLHttpRequest, textStatus, errorThrown) {                 
      $('#EmailExist').show();
      $('#EmailExist').html(errorThrown);
    }
  });
}
$.validator.addMethod("emailcustom",function(value,element)
{
  //return this.optional(element) || /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,4}\b$/i.test(value);
  return this.optional(element) || /^\b[A-Z0-9._%-]+@[A-Z0-9.-]+\.[A-Z]{2,10}\b$/i.test(value);
},"Please enter valid email address");

// custom password
$.validator.addMethod("passwordcustome",function(value,element)
{
  //return this.optional(element) || /^(?=.*[0-9])(?=.*[!@#$%^&*)(])(?=.*[A-Z])[a-zA-Z0-9!@#$%^&*)(]{8,}$/.test(value);
  return true;
},"Passwords must contain at least 8 characters, including uppercase, lowercase letters, symbols and numbers.");
// end here
 /^[+-]?\d+$/
// custom code for lesser than
jQuery.validator.addMethod('lesserThan', function(value, element, param) {  
  return ( parseInt(value) <= parseInt(jQuery(param).val()) );
}, 'Must be less than close time' );

// custom code for greater than
$.validator.addMethod("greaterThan", function(value, element, param) {
  return ( parseInt(value) >= parseInt(jQuery(param).val()) );    
}, "Must be greater than open time");

// custom code for greater than
$.validator.addMethod("greater", function(value, element, param) {
  return ( parseInt(value) > parseInt(jQuery(param).val()));    
}, "Must be greater than Amount");
// custom password
$.validator.addMethod("phoneNumber",function(value,element)
{
  return this.optional(element) || /^[+]?\d+$/.test(value);
},"Please enter valid phone number");
// end here

const isNumericInput = (event) => {
    const key = event.keyCode;
    return ((key >= 48 && key <= 57) || // Allow number line
        (key >= 96 && key <= 105) // Allow number pad
    );
};

const isModifierKey = (event) => {
    const key = event.keyCode;
    return (event.shiftKey === true || key === 35 || key === 36) || // Allow Shift, Home, End
        (key === 8 || key === 9 || key === 13 || key === 46) || // Allow Backspace, Tab, Enter, Delete
        (key > 36 && key < 41) || // Allow left, up, right, down
        (
            // Allow Ctrl/Command + A,C,V,X,Z
            (event.ctrlKey === true || event.metaKey === true) &&
            (key === 65 || key === 67 || key === 86 || key === 88 || key === 90)
        )
};

const enforceFormat = (event) => {
    // Input must be of a valid number format or a modifier key, and not longer than ten digits
    if(!isNumericInput(event) && !isModifierKey(event)){
        event.preventDefault();
    }
};

const formatToPhone = (event) => {
    if(isModifierKey(event)) {return;}

    // I am lazy and don't like to type things more than once
    const target = event.target;
    const input = target.value.replace(/\D/g,'').substring(0,10); // First ten digits of input only
    const zip = input.substring(0,3);
    const middle = input.substring(3,6);
    const last = input.substring(6,10);

    if(input.length > 6){target.value = `(${zip}) ${middle}-${last}`;}
    else if(input.length > 3){target.value = `(${zip}) ${middle}`;}
    else if(input.length > 0){target.value = `(${zip}`;}
};