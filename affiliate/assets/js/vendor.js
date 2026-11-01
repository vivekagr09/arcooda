

/*---------------------------------------------------------------------
              CounterUp 2
-----------------------------------------------------------------------*/
if (window.counterUp !== undefined) {
  const counterUp = window.counterUp["default"];
  const counterUp2 = document.querySelectorAll( '.counter' )
  Array.from(counterUp2, (el) => {
    if (typeof Waypoint !== typeof undefined) {
      const waypoint = new Waypoint({
        element: el,
        handler: function () {
          counterUp(el, {
            duration: 1000,
            delay: 10,
          });
          this.destroy();
        },
        offset: "bottom-in-view",
      });
    }
  })
}

/*---------------------------------------------------------------------
              SliderTab
-----------------------------------------------------------------------*/

Array.from(document.querySelectorAll('[data-toggle="slider-tab"]'), (elem) => {
  if (typeof SliderTab !== typeof undefined) {
    new SliderTab(elem)
  }
})

let Scrollbar
if (typeof Scrollbar !== typeof null) {
  if (document.querySelectorAll(".data-scrollbar").length) {
    Scrollbar = window.Scrollbar
    Scrollbar.init(document.querySelector('.data-scrollbar'), {
      continuousScrolling: false,
    })
  }
}

/*---------------------------------------------------------------------
  Data tables
-----------------------------------------------------------------------*/
/*---------------------------------------------------------------------
              Resize Plugins
-----------------------------------------------------------------------*/

const resizePlugins = () => {
  // sidebar-mini
  const tabs = document.querySelectorAll('.nav')
  const sidebarResponsive = document.querySelector('.sidebar-default')
  if (window.innerWidth < 1025) {
    Array.from(tabs, (elem) => {
      if (!elem.classList.contains('flex-column') && elem.classList.contains('nav-tabs') && elem.classList.contains('nav-pills')) {
        elem.classList.add('flex-column', 'on-resize');
      }
    })
    if(sidebarResponsive !== null) {
      if (!sidebarResponsive.classList.contains('sidebar-mini')) {
        sidebarResponsive.classList.add('sidebar-mini','on-resize')
      }
    }
  } else {
    Array.from(tabs, (elem) => {
      if (elem.classList.contains('on-resize')) {
        elem.classList.remove('flex-column', 'on-resize');
      }
    })
    if(sidebarResponsive !== null) {
      if (sidebarResponsive.classList.contains('sidebar-mini') && sidebarResponsive.classList.contains('on-resize')) {
        sidebarResponsive.classList.remove('sidebar-mini','on-resize')
      }
    }
  }
}


/*---------------------------------------------------------------------
              Sidebar Toggle
-----------------------------------------------------------------------*/
const sidebarToggle = (elem) => {
  elem.addEventListener('click', (e) => {
    const sidebar = document.querySelector('.sidebar')
    if (sidebar.classList.contains('sidebar-mini')) {
      sidebar.classList.remove('sidebar-mini')
    } else {
      sidebar.classList.add('sidebar-mini')
    }
  })
}

const sidebarToggleBtn = document.querySelectorAll('[data-toggle="sidebar"]')
const sidebar = document.querySelector('.sidebar-default')
if (sidebar !== null) {
  const sidebarActiveItem = sidebar.querySelectorAll('.active')
  Array.from(sidebarActiveItem, (elem) => {
    if (!elem.closest('ul').classList.contains('iq-main-menu')) {
      const childMenu = elem.closest('ul')
      childMenu.classList.add('show')
      const parentMenu = childMenu.closest('li').querySelector('.nav-link')
      parentMenu.classList.add('collapsed')
      parentMenu.setAttribute('aria-expanded', true)
    }
  })
}
Array.from(sidebarToggleBtn, (sidebarBtn) => {
  sidebarToggle(sidebarBtn)
})

/*------------------------
Back To Top
--------------------------*/
$('#back-to-top').fadeOut();
  $(window).on("scroll", function() {
    if ($(this).scrollTop() > 250) {
      $('#back-to-top').fadeIn(1400);
    } 
    else {
      $('#back-to-top').fadeOut(400);
    }
  });
// scroll body to 0px on click
  $('#top').on('click', function() {
    $('top').tooltip('hide');
    $('body,html').animate({
      scrollTop: 0
    }, 0);
  return false;
});



/*---------------------------------------------------------------------
              Window Resize
-----------------------------------------------------------------------*/

window.addEventListener('resize', function(event) {
  resizePlugins()
});

/*-------------------------------
| | | | | DropDown
--------------------------------*/

function darken_screen(yesno){
  if( yesno == true ){
    if (document.querySelector('.screen-darken') !== null) {
      document.querySelector('.screen-darken').classList.add('active');
    }
  }
  else if(yesno == false){
    if (document.querySelector('.screen-darken') !== null) {
      document.querySelector('.screen-darken').classList.remove('active');
    }
  }
}
	
function close_offcanvas(){
  darken_screen(false);
  if (document.querySelector('.mobile-offcanvas.show') !== null) {
    document.querySelector('.mobile-offcanvas.show').classList.remove('show');
    document.body.classList.remove('offcanvas-active');
  }
}

function show_offcanvas(offcanvas_id){
  darken_screen(true);
  if(document.getElementById(offcanvas_id) !== null) {
    document.getElementById(offcanvas_id).classList.add('show');
    document.body.classList.add('offcanvas-active');
  }
}

document.addEventListener("DOMContentLoaded", function(){
  document.querySelectorAll('[data-trigger]').forEach(function(everyelement){
    let offcanvas_id = everyelement.getAttribute('data-trigger');
    everyelement.addEventListener('click', function (e) {
      e.preventDefault();
          show_offcanvas(offcanvas_id);
    });
  });
  if(document.querySelectorAll('.btn-close')) {
    document.querySelectorAll('.btn-close').forEach(function(everybutton){
      everybutton.addEventListener('click', function (e) { 
            close_offcanvas();
        });
    });
  }
  if(document.querySelector('.screen-darken')) {
    document.querySelector('.screen-darken').addEventListener('click', function(event){
      close_offcanvas();
    });
  }
});
if (document.querySelector('#navbarSideCollapse'))  {
  document.querySelector('#navbarSideCollapse').addEventListener('click', function () {
    document.querySelector('.offcanvas-collapse').classList.toggle('open')
  })
}

/*---------------------------------------------------------------------
Form Validation
-----------------------------------------------------------------------*/

// Example starter JavaScript for disabling form submissions if there are invalid fields
window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
          if (form.checkValidity() === false) {
              event.preventDefault();
              event.stopPropagation();
          }
          form.classList.add('was-validated');
        }, false);
      });
}, false);