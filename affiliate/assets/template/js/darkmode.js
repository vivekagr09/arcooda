var admin_theme_selected = localStorage.getItem("admin_theme");
if (admin_theme_selected == 1) {
  $(".navbar-menu-wrapper .header-right").removeClass('admin_top_bar_color');
  $(".navbar .navbar-menu-wrapper").removeClass('admin_top_bar_color');
  $(".navbar .navbar-brand-wrapper").removeClass('admin_logo_color');
  $(".body-common-class").removeClass('admin_side_bar_color');
  $(".sidebar-offcanvas").removeClass('admin_side_bar_color');
  $(".sidebar-offcanvas .navbar-nav").removeClass('admin_side_bar_color');
  $(".dashboard-wrap").removeClass('admin_side_bar_color');
  $(".dashboard-wrap").css('background-color', '#3a3f51');
  $(".body-common-class").css('background-color', '#3a3f51');
  $(".footer-bg .dashboard-footer").removeClass('admin_footer_color');

  $('.admin_theme_selected').removeClass('fa fa-toggle-off');
  $('.admin_theme_icon').removeClass('fas fa-sun');
  $('.admin_theme_selected').addClass('fa fa-toggle-on');
  $('.admin_theme_icon').addClass('fas fa-moon');
}else{
  $(".dashboard-wrap").css('background-color', '');
  $(".body-common-class").css('background-color', '');
  $(".navbar-menu-wrapper .header-right").addClass('admin_top_bar_color');
  $(".navbar .navbar-menu-wrapper").addClass('admin_top_bar_color');
  $(".navbar .navbar-brand-wrapper").addClass('admin_logo_color');
  $(".body-common-class").addClass('admin_side_bar_color');
  $(".sidebar-offcanvas").addClass('admin_side_bar_color');
  $(".sidebar-offcanvas .navbar-nav").addClass('admin_side_bar_color');
  $(".dashboard-wrap").addClass('admin_side_bar_color');
  $(".footer-bg .dashboard-footer").addClass('admin_footer_color');

  $('.admin_theme_selected').removeClass('fa fa-toggle-on');
  $('.admin_theme_icon').removeClass('fas fa-moon');
  $('.admin_theme_selected').addClass('fa fa-toggle-off');
  $('.admin_theme_icon').addClass('fas fa-sun');
}

// Function to change the admin theme
function change_admin_theme() {
  // Toggle the theme icon
  $('#admin_theme_action').toggleClass('fa-toggle-off');
  $('#admin_theme_action').toggleClass('fa-toggle-on');

  // Get the current theme
  var isDark = $("#admin_theme").val();
  
  // Sidebar classes to toggle
  var sidebar_classes = "sidebar-light sidebar-dark";
  var $body = $("body");

  // Update the admin_theme value
  $("#admin_theme").val(isDark == 0 ? 1 : 0);

  // Toggle the sidebar classes
  $("#theme-settings").toggleClass("open");
  $body.toggleClass(sidebar_classes);
  $body.toggleClass("sidebar-light");
  
  // Remove existing sidebar background options
  $(".sidebar-bg-options").removeClass("selected");

  // Update localStorage
  localStorage.removeItem('admin_theme');
  localStorage.setItem('admin_theme', isDark == 0 ? 1 : 0);

  // Update the colors and classes based on the current theme
  if ($('.admin_theme_selected').hasClass('fa fa-toggle-off')) {
    // Set styles for light mode
    $('#settings-trigger').css({'background-color': 'white', 'border-radius': '50%'});
    $(".nav-item.dropdown.user-right .dropdown-menu").css('background-color', '#000');
    $(".nav-item.dropdown.user-right .dropdown-menu .dropdown-item").css('color', '#FFF');
    $(".nav-item.dropdown .dropdown-menu .dropdown-item").css('color', '#FFF');
    //$('<style>.nav-item.dropdown .dropdown-menu .dropdown-item:hover { background-color: #555; color: #fff; }</style>').appendTo('head');
    
    // Existing code for light mode
    $(".navbar-menu-wrapper .header-right").removeClass('admin_top_bar_color');
    $(".navbar .navbar-menu-wrapper").removeClass('admin_top_bar_color');
    $(".navbar .navbar-brand-wrapper").removeClass('admin_logo_color');
    $(".body-common-class").removeClass('admin_side_bar_color');
    $(".sidebar-offcanvas").removeClass('admin_side_bar_color');
    $(".sidebar-offcanvas .navbar-nav").removeClass('admin_side_bar_color');
    $(".dashboard-wrap").removeClass('admin_side_bar_color');
    $(".dashboard-wrap").css('background-color', '#3a3f51');
    $(".body-common-class").css('background-color', '#3a3f51');
    $(".footer-bg .dashboard-footer").removeClass('admin_footer_color');

    $('.admin_theme_selected').removeClass('fa fa-toggle-off');
    $('.admin_theme_icon').removeClass('fas fa-sun');
    $('.admin_theme_selected').addClass('fa fa-toggle-on');
    $('.admin_theme_icon').addClass('fas fa-moon');
  } else {
    // Reset styles for dark mode
    $('#settings-trigger').css('background-color', 'transparent');
    $(".nav-item.dropdown.user-right .dropdown-menu").css('background-color', '');
    $(".nav-item.dropdown.user-right .dropdown-menu .dropdown-item").css('color', '');
    $(".nav-item.dropdown .dropdown-menu .dropdown-item").css('color', '');
    //$('<style>.nav-item.dropdown .dropdown-menu .dropdown-item:hover { background-color: ""; color: ""; }</style>').appendTo('head');

    // Existing code for dark mode
    $(".dashboard-wrap").css('background-color', '');
    $(".body-common-class").css('background-color', '');
    $(".navbar-menu-wrapper .header-right").addClass('admin_top_bar_color');
    $(".navbar .navbar-menu-wrapper").addClass('admin_top_bar_color');
    $(".navbar .navbar-brand-wrapper").addClass('admin_logo_color');
    $(".body-common-class").addClass('admin_side_bar_color');
    $(".sidebar-offcanvas").addClass('admin_side_bar_color');
    $(".sidebar-offcanvas .navbar-nav").addClass('admin_side_bar_color');
    $(".dashboard-wrap").addClass('admin_side_bar_color');
    $(".footer-bg .dashboard-footer").addClass('admin_footer_color');

    $('.admin_theme_selected').removeClass('fa fa-toggle-on');
    $('.admin_theme_icon').removeClass('fas fa-moon');
    $('.admin_theme_selected').addClass('fa fa-toggle-off');
    $('.admin_theme_icon').addClass('fas fa-sun');
  }
}

// Set initial styles based on localStorage
$(document).ready(function() {
  var isDark = localStorage.getItem('admin_theme') || 0;

  if (isDark == 1) { // Dark mode
    $('#settings-trigger').css({'background-color': 'white', 'border-radius': '50%'});
    $(".nav-item.dropdown.user-right .dropdown-menu").css('background-color', '#000');
    $(".nav-item.dropdown.user-right .dropdown-menu .dropdown-item").css('color', '#FFF');
    $(".nav-item.dropdown .dropdown-menu .dropdown-item").css('color', '#FFF');
    //$('<style>.nav-item.dropdown .dropdown-menu .dropdown-item:hover { background-color: #555; color: #fff; }</style>').appendTo('head');
  } else { // Light mode
    $('#settings-trigger').css('background-color', 'transparent');
    $(".nav-item.dropdown.user-right .dropdown-menu").css('background-color', '');
    $(".nav-item.dropdown.user-right .dropdown-menu .dropdown-item").css('color', '');
    $(".nav-item.dropdown .dropdown-menu .dropdown-item").css('color', '');
    //$('<style>.nav-item.dropdown .dropdown-menu .dropdown-item:hover { background-color: ""; color: ""; }</style>').appendTo('head');
  }
});