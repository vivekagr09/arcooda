var base_url = $("#base_url").val();

$("input[name='theme[admin_side_bar_color]']").on("focusout", function(){
    $(".sidebar-offcanvas").css('background-color', $(this).val());
    $(".sidebar-offcanvas .navbar-nav").css('background-color', $(this).val());
    $(".sidebar-light").css('background-color', $(this).val());
    $(".dashboard-wrap").css('background-color', $(this).val());
});

$("input[name='theme[admin_side_scroll_bar_color]']").on("focusout", function(){
    $(".scroll-bar").css({'-webkit-scrollbar-thumb':'background-color: '+$(this).val()});
});

$("input[name='theme[admin_side_bar_text_color]']").on("focusout", function(){
    $(".nav-link .menu-title").css('color', $(this).val());
    $(".dropdown-menu .dropdown-item").css('color', $(this).val());
    $(".admin-balance .name").css('color', $(this).val());
    $(".admin-balance .designation").css('color', $(this).val());
});

$("input[name='theme[admin_top_bar_color]']").on("focusout", function(){
    $(".navbar-menu-wrapper .header-right").css('background-color', $(this).val());
    $(".navbar .navbar-menu-wrapper").css('background-color', $(this).val());
});

$("input[name='theme[admin_footer_color]']").on("focusout", function(){
    $(".footer-bg .dashboard-footer").css('background-color', $(this).val());
});

$("input[name='theme[admin_logo_color]']").on("focusout", function(){
    $(".navbar .navbar-brand-wrapper").css('background-color', $(this).val());
});

$("input[name='theme[admin_login_box_background_color]']").on("focusout", function(){
    $(".navbar .navbar-brand-wrapper").css('background-color', $(this).val());
});

$("input[name='theme[admin_login_background_color]']").on("focusout", function(){
    $(".login-main").css('background-color', $(this).val());
});

$(".default-theme-setting").on("click", function(){
    var setting = $(this).val();
    var color = '';

    if (setting == "user_side_bar_color") {
        color = "#FFFFFF";
        $("input[name='theme[user_side_bar_color]']").val(color);
    }else if (setting == 'user_side_bar_text_color') {
        color = "#3F567A";
        $("input[name='theme[user_side_bar_text_color]']").val(color);
    }else if (setting == 'user_side_bar_clock_text_color') {
        color = "#085445";
        $("input[name='theme[user_side_bar_clock_text_color]']").val(color);
    }else if (setting == 'user_side_bar_text_hover_color') {
        color = "#5EC394";
        $("input[name='theme[user_side_bar_text_hover_color]']").val(color);
    }else if (setting == 'user_top_bar_color') {
        color = "#FFFFFF";
        $("input[name='theme[user_top_bar_color]']").val(color);
    }else if (setting == 'user_footer_color') {
        color = "#FFFFFF";
        $("input[name='theme[user_footer_color]']").val(color);
    }
    else if (setting == 'user_button_color') {
        color = "#3d5674";
        $("input[name='theme[user_button_color]']").val(color);
    }
    else if (setting == 'user_button_hover_color') {
        color = "#085445";
        $("input[name='theme[user_button_hover_color]']").val(color);
    }
    
    else if (setting == 'admin_side_bar_color') {
        color = "#FFFFFF";
        $("input[name='theme[admin_side_bar_color]']").val(color);
        $(".sidebar-offcanvas").css('background-color', color);
        $(".sidebar-offcanvas .navbar-nav").css('background-color', color);
        $(".sidebar-light").css('background-color', color);
        $(".dashboard-wrap").css('background-color', color);
    }else if (setting == 'admin_side_bar_scroll_color') {
        color = "#007BFF";
        $("input[name='theme[admin_side_bar_scroll_color]']").val(color);
    }else if (setting == 'admin_side_bar_text_color') {
        color = "#686868";
        $("input[name='theme[admin_side_bar_text_color]']").val(color);
        $(".nav-link .menu-title").css('color', color);
        $(".dropdown-menu .dropdown-item").css('color', color);
        $(".admin-balance .name").css('color', color);
        $(".admin-balance .designation").css('color', color);
    }else if (setting == 'admin_side_bar_text_hover_color') {
        color = "#007BFF";
        $("input[name='theme[admin_side_bar_text_hover_color]']").val(color);
    }else if (setting == 'admin_top_bar_color') {
        color = "#FFFFFF";
        $("input[name='theme[admin_top_bar_color]']").val(color);
        $(".navbar-menu-wrapper .header-right").css('background-color', color);
        $(".navbar .navbar-menu-wrapper").css('background-color', color);
    }else if (setting == 'admin_footer_color') {
        color = "#F2F3F5";
        $("input[name='theme[admin_footer_color]']").val(color);
        $(".footer-bg .dashboard-footer").css('background-color', color);
    }else if (setting == 'admin_logo_color') {
        color = "#007BFF";
        $("input[name='theme[admin_logo_color]']").val(color);
        $(".navbar .navbar-brand-wrapper").css('background-color', color);
    }
    else if (setting == 'admin_button_color') {
        color = "#3d5674";
        $("input[name='theme[admin_button_color]']").val(color);
        $(".navbar .navbar-brand-wrapper").css('background-color', color);
    }
    else if (setting == 'admin_button_hover_color') {
        color = "#007BFF";
        $("input[name='theme[admin_button_hover_color]']").val(color);
        $(".navbar .navbar-brand-wrapper").css('background-color', color);
    }
     else if (setting == 'admin_login_box_background_color') {
        color = "#7a90a8";
        $("input[name='theme[admin_login_box_background_color]']").val(color);
        $(".navbar .navbar-brand-wrapper").css('background-color', color);
    }
    else if (setting == 'admin_login_background_color') {
        color = "#5e7590";
        $("input[name='theme[admin_login_background_color]']").val(color);
        $(".login-main").css('background-color', color);
    }

    


    if(color != '') {
        $.ajax({
            url:base_url+'admincontrol/default_theme_settings',
            type:'POST',
            dataType:'json',
            data:{'action':'default_theme_settings', setting:setting, color:color},
            success:function(json){
            },
        });
    }
});

$(".default-font-setting").on("click", function(){
    var setting = $(this).val();
    var font = '';

    if (setting == "admin_side_font") {
        font = "PT Sans";
        $(".class_admin_side_font").val(font).trigger("change");
    }else if (setting == 'user_side_font') {
        font = "sans-serif";
        $(".class_user_side_font").val(font).trigger("change");
    }else if (setting == 'front_side_font') {
        font = "sans-serif";
        $(".class_front_side_font").val(font).trigger("change");
    }else if (setting == 'cart_store_side_font') {
        font = "Jost";
        $(".class_cart_store_side_font").val(font).trigger("change");
    }else if (setting == 'sales_store_side_font') {
        font = "Roboto";
        $(".class_sales_store_side_font").val(font).trigger("change");
    }

    if(font != '') {
        $.ajax({
            url:base_url+'admincontrol/default_font_settings',
            type:'POST',
            dataType:'json',
            data:{'action':'default_font_settings', setting:setting, font:font},
            success:function(json){
            },
        });
    }
});