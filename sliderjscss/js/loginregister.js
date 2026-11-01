var LoginRegister = function () {
   var defaultUrl = {
        "account_url"  : "/account/",
        "contact_url"  : "/account/contacts",
        "freight_url"  : "/account/freight",
        "login_url"    : "/account/login",
        "notify_url"   : "/account/notify",
        "profile_url"  : "/account/profile",
        "register_url" : "/account/register",
        "reset_url"    : "/account/passwordFind",
        "shipping_url" : "/account/shipping",
        "username_url" : "/account/checkUsername",
    };

    var urlConfig = function (url) {
        return $.extend(true, defaultUrl, url);
    };

    var userContactFormValidate = function ($form) {
        var calcRequiredFields = function (formData) {
            var result = [
                "c_name",
                "c_homeTel",
                "c_email",
            ];

            return result;
        }
        var formData = $form.serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        $("input,textarea,select", $form).attr("required", false);
        $(calcRequiredFields(formData)).each(function() {
            $("[name='" + this + "']", $form).attr("required", true);
        });
    };

    var contactInit = function ($form) {
        userContactFormValidate($form);
    };

    var userFreightFormValidate = function ($form) {
        var calcRequiredFields = function (formData) {
            var result = [
                "f_courier_id",
                "f_account_num",
                //"f_notes",
            ];

            if ("0" == formData["f_courier_id"]) {
                result.push(...[
                    "f_organization"
                ]);
            }

            return result;
        }

        $("[name='f_courier_id']", $form).change(function() {
            var formData = $form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $("input,textarea,select", $form).attr("required", false);
            $(calcRequiredFields(formData)).each(function() {
                $("[name='" + this + "']", $form).attr("required", true);
            });
        }).first().change();
    };

    var freightInit = function ($form) {
        $("[name='f_courier_id']", $form).change(function() {
            $(".require_f_courier_id,.require_f_courier_id_0", $form).stop().hide();
            $(".require_f_courier_id,.require_f_courier_id_" + $(this).val(), $form).fadeIn();
        }).change();

        userFreightFormValidate($form);
    };

    var userNotifyFormValidate = function ($form) {
        var calcRequiredFields = function (formData) {
            var n_tel = [
                formData["n_homeTel"],
                formData["n_workTel"],
                formData["n_mobileTel"],
                formData["n_faxTel"],
            ].filter(function(v, i, s) {
                return v != "" && s.indexOf(v) == i;
            }).length;

            var result = [
                "n_givenName",
                "n_familyName",
                "n_organization",
                "n_streetAddress",
                "n_addressLevel2",
                "n_addressLevel1",
                "n_postalCode",
                "n_country",
                "n_addressType",
            ];
            if (n_tel < 2) {
                result.push(...[
                    "n_homeTel",
                    "n_workTel",
                    "n_mobileTel",
                    "n_faxTel",
                ]);
            }

            return result;
        }

        $("[name$='Tel']", $form).change(function() {
            var formData = $form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $("input,textarea,select", $form).attr("required", false);
            $(calcRequiredFields(formData)).each(function() {
                $("[name='" + this + "']", $form).attr("required", true);
            });
        }).first().change();
    };

    var notifyInit = function ($form) {
        userNotifyFormValidate($form);
    };

    var userShippingFormValidate = function ($form) {
        var calcRequiredFields = function (formData) {
            var s_tel = [
                formData["s_homeTel"],
                formData["s_workTel"],
                formData["s_mobileTel"],
                formData["s_faxTel"],
            ].filter(function(v, i, s) {
                return v != "" && s.indexOf(v) == i;
            }).length;

            var result = [
                "s_givenName",
                "s_familyName",
                "s_organization",
                "s_streetAddress",
                "s_addressLevel2",
                "s_addressLevel1",
                "s_postalCode",
                "s_country",
                "s_addressType",
                "s_email",
            ];
            if (s_tel < 2) {
                result.push(...[
                    "s_homeTel",
                    "s_workTel",
                    "s_mobileTel",
                    "s_faxTel",
                ]);
            }

            return result;
        }

        $("[name$='Tel']", $form).change(function() {
            var formData = $form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $("input,textarea,select", $form).attr("required", false);
            $(calcRequiredFields(formData)).each(function() {
                $("[name='" + this + "']", $form).attr("required", true);
            });
        }).first().change();
    };

    var shippingInit = function ($form) {
        userShippingFormValidate($form);
    };

    var userProfileFormValidate = function ($form) {
        var calcRequiredFields = function (formData) {
            var b_tel = [
                formData["b_homeTel"],
            ].filter(function(v, i, s) {
                return v != "" && s.indexOf(v) == i;
            }).length;

            var result = [
                "account_type",
            ];

            if ("private" == formData["account_type"] || "company" == formData["account_type"]) {
                result.push(...[
                    "b_givenName",
                    "b_familyName",
                    "b_streetAddress",
                    "b_addressLevel2",
                    "b_addressLevel1",
                    "b_postalCode",
                    "b_country",
                ]);
                if (b_tel < 2) {
                    result.push(...[
                        "b_homeTel"
                    ]);
                }
            }

            if ("company" == formData["account_type"]) {
                result.push(...[
                    "b_organization",
                    "b_addressType",
                ]);
            }
            return result;
        }

        $("[name='account_type'],[name$='Tel']", $form).change(function() {
            var formData = $form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $("input,textarea,select", $form).attr("required", false);
            $(calcRequiredFields(formData)).each(function() {
                $("[name='" + this + "']", $form).attr("required", true);
            });
        }).first().change();

        $('#profileBtn', $form).on('click', function () {
            //var valid = true;
            /*$("input,textarea,select", $form).each(function() {
                var my_validity = this.checkValidity();
                $(this).toggleClass('is-invalid', !my_validity);
                if (!my_validity) {
                    valid = false;
                    $(this).siblings(".invalid-feedback").text(this.validationMessage);
                }
            });*/
            /*if (!valid) {
                return false;
            }
            return true;*/
        });
    };

    var profileInit = function ($form) {
        $("[name='account_type']", $form).change(function() {
            $(".require_account_type,.require_account_type_private,.require_account_type_company", $form).stop().hide();
            $(".require_account_type,.require_account_type_" + $(this).val(), $form).fadeIn();
        }).change();

        /*$("[name='b_givenName'],[name='b_familyName']", $form).change(function() {
            var b_givenName  = $("[name='b_givenName']", $form).val();
            var b_familyName = $("[name='b_familyName']", $form).val();
            $("[name='b_organization']", $form).val($.trim(b_givenName + " " + b_familyName));
        });*/

        userProfileFormValidate($form);
    };

    var register = function ($form) {
        var $RegisterBtn = $("#register");
        $RegisterBtn.attr("disabled", true);

        var param = $form.serializeArray().reduce(function(obj, item) {
            obj[item.name] = item.value;
            return obj;
        }, {});
        param.action = "register";
        param.json   = true;

        $.ajax({
            url    : defaultUrl.register_url,
            type   : 'POST',
            data   : param,
            success: function (data) {
                //$RegisterBtn.removeAttr('disabled');
                console.log(data);

                /*
                if (data.code != 200) {
                    // Refresh captcha, add tips to all errors
                } else {
                    document.location = defaultUrl.account_url;
                }
                */
            },
            error  : function () {
                //$RegisterBtn.removeAttr('disabled');
                console.log('error');

                // Fallback to basic form submission
                //$form.submit();
            }
        });
    };

    var userRegisterFormValidate = function ($form) {
        var calcRequiredFields = function (formData) {
            var b_tel = [
                formData["b_homeTel"]
            ].filter(function(v, i, s) {
                return v != "" && s.indexOf(v) == i;
            }).length;

            var s_tel = [
                formData["s_homeTel"],
            ].filter(function(v, i, s) {
                return v != "" && s.indexOf(v) == i;
            }).length;

            var result = [
                "account_type",
                "username",
                "newPassword",
                "newPassword_repeat",
            ];

            if ("private" == formData["account_type"] || "company" == formData["account_type"]) {
                result.push(...[
                    "b_givenName",
                    "b_familyName",
                    "b_streetAddress",
                    "b_addressLevel2",
                    "b_addressLevel1",
                    "b_postalCode",
                    "b_country",
                    "billing_as_shipping",
                    "own_freight",
                    "b_addressType",
                ]);
                if (b_tel < 2) {
                    result.push(...[
                        "b_homeTel",
                    ]);
                }
                if ("n" == formData["billing_as_shipping"]) {
                    result.push(...[
                        "s_givenName",
                        "s_familyName",           
                        "s_streetAddress",
                        "s_addressLevel2",
                        "s_addressLevel1",
                        "s_postalCode",
                        "s_country",
                        "billing_as_shipping",
                        "s_addressType",
                        "s_email"
                    ]);
                    if (s_tel < 2) {
                        result.push(...[
                            "s_homeTel"
                        ]);
                    }
                }
            }

            if ("company" == formData["account_type"]) {
                result.push(...[
                    "b_organization",
                    
                ]);
                if ("n" == formData["billing_as_shipping"]) {
                    result.push(...[
                        "s_organization",
                    ]);
                }
            }

            if ("n" == formData["own_freight"]) {
                result.push(...[
                    "freight_priority",
                    "insurance_required",
                ]);
            } else if ("y" == formData["own_freight"]) {
                result.push(...[
                    "freight_priority",
                    "courier",
                ]);
                if (0 == formData["courier"]) {
                    result.push(...["courier_name"]);
                }
            }
            return result;
        }

        $("[name='account_type'],[name='billing_as_shipping'],[name='own_freight'],[name='courier'],[name$='Tel']", $form).change(function() {
            var formData = $form.serializeArray().reduce(function(obj, item) {
                obj[item.name] = item.value;
                return obj;
            }, {});
            $("input,textarea,select", $form).attr("required", false);
            $(calcRequiredFields(formData)).each(function() {
                $("[name='" + this + "']", $form).attr("required", true);
            });
        }).first().change();

        $('#registerBtn', $form).on('click', function () {
       /*     var valid = true;
            $("input,textarea,select", $form).each(function() {
                var my_validity = this.checkValidity();
                $(this).toggleClass('is-invalid', !my_validity);
                if (!my_validity) {
                    valid = false;
                    $(this).siblings(".invalid-feedback").text(this.validationMessage);
                }
            });
            if (!valid) {
                return false;
            }
            //register($form);
            return true;*/
        });
    };

    var registerInit = function ($form) {
        $("[name='account_type']", $form).change(function() {
            
            if (this.checked) {
                $(".require_account_type,.require_account_type_private,.require_account_type_company", $form).stop().hide();
                $(".require_account_type,.require_account_type_" + $(this).val(), $form).fadeIn();
            }
        }).change();

        $("[name='username']", $form).change(function() {
            var $elem = $(this);
            var $tip  = $elem.siblings(".invalid-feedback");
            var email = $elem.val();
            var valid = /^.+@.+[.].+$/.test(email)
            $elem.toggleClass('is-invalid', !valid);
            if (valid) {
                $.ajax({
                    url    : defaultUrl.username_url,
                    type   : 'POST',
                    data   : {username: email},
                    success: function (data) {
                        if (data == false) {
                            $tip.html("Email address already exists.<br />Please <a href=\"" + defaultUrl.login_url + "\">Login</a> or <a href=\"" + defaultUrl.reset_url + "\">Reset Password</a> to continue.");
                            $elem.toggleClass('is-invalid', true);
                        }
                    }
                });
            } else {
                $tip.html("Please enter a valid email address");
            }
        });

        $("[name='billing_as_shipping']", $form).change(function() {
            $(".require_billing_as_shipping,.require_billing_as_shipping_y,.require_billing_as_shipping_n", $form).stop().hide();
            if (this.checked) {
                $(".require_billing_as_shipping,.require_billing_as_shipping_" + $(this).val(), $form).fadeIn();
            }
        }).change();

        $("[name='own_freight']", $form).change(function() {
            
            if (this.checked) {
                $(".require_own_freight,.require_own_freight_y,.require_own_freight_n", $form).stop().hide();
                $(".require_own_freight,.require_own_freight_" + $(this).val(), $form).fadeIn();
            }
        }).change();

        /*$("[name='b_givenName'],[name='b_familyName']", $form).change(function() {
            var b_givenName  = $("[name='b_givenName']", $form).val();
            var b_familyName = $("[name='b_familyName']", $form).val();
            $("[name='b_organization']", $form).val($.trim(b_givenName + " " + b_familyName));
        });*/
        $("[name='courier']", $form).change(function() {
            var changeval  = $("[name='courier']", $form).val();
            if(changeval==0){
               $(".field-courier_name", $form).fadeIn(); 
            }else{
                 $(".field-courier_name", $form).stop().hide();
            }
        });
        userRegisterFormValidate($form);
    };

    return {
        "contactInit"             : contactInit,
        "freightInit"             : freightInit,
        "notifyInit"              : notifyInit,
        "profileInit"             : profileInit,
        "registerInit"            : registerInit,
        "shippingInit"            : shippingInit,
        "urlConfig"               : urlConfig,
    };
}();
