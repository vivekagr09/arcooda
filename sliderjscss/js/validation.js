
 $(function() {
   $.validator.addMethod("pwcheck", function(value) {
     return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
         && /[a-z]/.test(value) // has a lowercase letter
         && /\d/.test(value) // has a digit
  });
    $("form[name='loginform']").validate({
      rules: {
        username: {
          required: true,
          email: true,
        },
        Password: {
          required: true,
          minlength: 8,
          pwcheck: true,
        }
      },
      // Specify validation error messages
      messages: {
        Password: {
          required: "Please provide a password",
          minlength: "Your password must be at least 8 characters long",
          pwcheck: "Das Passwort entspricht nicht den Kriterien!",
        },
        username: {
            required: "Please enter your email address.",
        }
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function(form) {
        form.submit();
      }
    });
     $("form[name='userRegisterForm']").validate({
       rules: {
          newPassword: {
              minlength: 8,
              //pwcheck: true,
          },
          newPassword_repeat: {
              minlength: 8,
              equalTo: "#newPassword"
          }
      },
      messages: {
         newPassword: {
            pwcheck: "The password does not meet the criteria!",
           
        },
      },
      dynamic: {
          settings: {
              trigger: 'focusout'
          },
          callback: {
              onSuccess: function (node, input, keyCode) {
                  if ($(input).val()) {
                      $(input).removeClass('is-invalid');
                  }
              },
              onError: function (node, input, keyCode, error) {
                  $(input).addClass('is-invalid');
              }
          }
      },

    });
    $.validator.addMethod("pwcheck", function(value) {
       return /^[A-Za-z0-9\d=!\-@._*]*$/.test(value) // consists of only these
           && /[a-z]/.test(value) // has a lowercase letter
           && /\d/.test(value) // has a digit
    });
    $("form[name='loginformheader']").validate({
      rules: {
        username: {
          required: true,
          email: true,
        },
        Password: {
          required: true,
          minlength: 8
        }
      },
      // Specify validation error messages
      messages: {
        Password: {
          required: "Please provide a password",
          minlength: "Your password must be at least 8 characters long"
        },
        username: {
            required: "Please enter your email address.",
            email: "Please enter a valid email address.",
        }
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function(form) {
        form.submit();
      }
    });
    $("form#passwordFind").validate({
      rules: {
        username: {
          required: true,
          email: true,
        }
      },
      // Specify validation error messages
      messages: {
        username: {
            required: "Please enter your email address.",
            email: "Please enter a valid email address.",
        }
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function(form) {
        form.submit();
      }
    });
    $("form[name='resetpassword']").validate({
      rules: {
        currentpassword: {
          required: true,
          minlength: 8
        },
        newpassword:{
          required: true,
          minlength: 8
        },
        confirmpassword : {
            minlength : 8,
            equalTo : "#newpassword"
        }
      },
      // Specify validation error messages
      messages: {
        currentpassword: {
          required: "Please provide your current  password",
          minlength: "Your password must be at least 8 characters long"
        },
        newpassword: {
          required: "Please provide a new password",
          minlength: "Your password must be at least 8 characters long"
        }
      },
      // Make sure the form is submitted to the destination defined
      // in the "action" attribute of the form when valid
      submitHandler: function(form) {
        form.submit();
      }
    });
    $(".validateformclass").each(function() {
      $(this).validate({
        rules: {
        },
        // Specify validation error messages
        messages: {
        },
        // Make sure the form is submitted to the destination defined
        // in the "action" attribute of the form when valid
        submitHandler: function(form) {
          form.submit();
        }
      });
    });
});