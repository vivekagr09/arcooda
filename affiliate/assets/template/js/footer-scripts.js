// Function 1: Preload images script
window.addEventListener('load', function() {
    // Function to preload images
    function preloadImages() {
        // Find all image elements on the page
        const imageElements = document.querySelectorAll('img');
        
        // Loop through each image element and preload the image
        imageElements.forEach((imageElement) => {
            const img = new Image();
            img.src = imageElement.src;
        });
    }
    // Run the preload function
    preloadImages();
});

// Function 2: Nav Auto-Scroll script
$(document).ready(function() {
  let activeMenuItem = $('.navbar-nav .nav-item.dropdown .dropdown-menu a.active');
  if (activeMenuItem.length > 0) {
    activeMenuItem.closest('.dropdown-menu').addClass('show');
    $('.scroll-bar').animate({
      scrollTop: activeMenuItem.offset().top - ($('.scroll-bar').height() / 2) + (activeMenuItem.outerHeight() / 2)
    }, 1800);
  }
  $('.navbar-nav .nav-item.dropdown .nav-link.dropdown').on('click', function() {
    let parentDropdown = $(this).siblings('.dropdown-menu');
    if (!parentDropdown.hasClass('show')) {
      setTimeout(() => {
        $('.scroll-bar').animate({
          scrollTop: $(this).offset().top - ($('.scroll-bar').height() / 2) + ($(this).outerHeight() / 2)
        }, 1800);
      }, 200);
    }
  });
});

// Function 3: Summernote toggleStyle script
  function toggleStyle() {
    var styleEle = $("style#fixed");
    if (styleEle.length == 0) {
      $("<style id=\"fixed\">.note-editor .dropdown-toggle::after { all: unset; } .note-editor .note-dropdown-menu { box-sizing: content-box; } .note-editor .note-modal-footer { box-sizing: content-box; }</style>")
        .prependTo("body");
    } else {
      styleEle.remove();
    }
  }
  toggleStyle();


// Function-awayes last: Demo mode script
window.addEventListener('load', function() {
    setTimeout(function(){
        if (document.body.getAttribute('data-demo-mode') === 'true') {
            var demoFlyMessage = document.createElement('div');
            demoFlyMessage.className = 'demo-fly-message';

            var contentDiv = document.createElement('div');
            contentDiv.className = 'content';
            
            var pElement = document.createElement('p');
            pElement.textContent = 'Demo Mode';

            var iElement = document.createElement('i');
            iElement.className = 'bi bi-info-circle-fill fs-6';
            
            contentDiv.appendChild(iElement);
            contentDiv.appendChild(pElement);
            demoFlyMessage.appendChild(contentDiv);
            document.body.appendChild(demoFlyMessage);
        }
    }, 500);
});