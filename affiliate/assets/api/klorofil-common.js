	var jQrs = jQuery.noConflict();
	jQrs(document).ready(function(){ 

	/*-----------------------------------/
	/*	TOP NAVIGATION AND LAYOUT
	/*----------------------------------*/

	jQrs('.btn-toggle-fullwidth').on('click', function() {
		if(!jQrs('body').hasClass('layout-fullwidth')) {
			jQrs('body').addClass('layout-fullwidth');

		} else {
			jQrs('body').removeClass('layout-fullwidth');
			jQrs('body').removeClass('layout-default'); // also remove default behaviour if set
		}

		jQrs(this).find('.lnr').toggleClass('lnr-menu lnr-cross');

		if(jQrs(window).innerWidth() < 1025) {
			if(!jQrs('body').hasClass('offcanvas-active')) {
				jQrs('body').addClass('offcanvas-active');
			} else {
				jQrs('body').removeClass('offcanvas-active');
			}
		}
	});

	jQrs(window).on('load', function() {
		if(jQrs(window).innerWidth() < 1025) {
			jQrs('.btn-toggle-fullwidth').find('.icon-arrows')
			.removeClass('icon-arrows-move-left')
			.addClass('icon-arrows-move-right');
		}

		// adjust right sidebar top position
		jQrs('.right-sidebar').css('top', jQrs('.navbar').innerHeight());

		// if page has content-menu, set top padding of main-content
		if(jQrs('.has-content-menu').length > 0) {
			jQrs('.navbar + .main-content').css('padding-top', jQrs('.navbar').innerHeight());
		}

		// for shorter main content
		if(jQrs('.main').height() < jQrs('#sidebar-nav').height()) {
			jQrs('.main').css('min-height', jQrs('#sidebar-nav').height());
		}
	});


	/*-----------------------------------/
	/*	SIDEBAR NAVIGATION
	/*----------------------------------*/

	jQrs('.sidebar .arrow span[data-toggle="collapse"]').on('click', function() {
		if(jQrs(this).hasClass('collapsed')) {
			jQrs(this).addClass('active');
		} else {
			jQrs(this).removeClass('active');
		}
	});

	if( jQrs('.sidebar-scroll').length > 0 ) {
		jQrs('.sidebar-scroll').slimScroll({
			height: '95%',
			wheelStep: 2,
		});
	}


	/*-----------------------------------/
	/*	PANEL FUNCTIONS
	/*----------------------------------*/

	// panel remove
	jQrs('.panel .btn-remove').click(function(e){

		e.preventDefault();
		jQrs(this).parents('.panel').fadeOut(300, function(){
			jQrs(this).remove();
		});
	});

	// panel collapse/expand
	var affectedElement = jQrs('.panel-body');

	jQrs('.panel .btn-toggle-collapse').clickToggle(
		function(e) {
			e.preventDefault();

			// if has scroll
			if( jQrs(this).parents('.panel').find('.slimScrollDiv').length > 0 ) {
				affectedElement = jQrs('.slimScrollDiv');
			}

			jQrs(this).parents('.panel').find(affectedElement).slideUp(300);
			jQrs(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		},
		function(e) {
			e.preventDefault();

			// if has scroll
			if( jQrs(this).parents('.panel').find('.slimScrollDiv').length > 0 ) {
				affectedElement = jQrs('.slimScrollDiv');
			}

			jQrs(this).parents('.panel').find(affectedElement).slideDown(300);
			jQrs(this).find('i.lnr-chevron-up').toggleClass('lnr-chevron-down');
		}
	);


	/*-----------------------------------/
	/*	PANEL SCROLLING
	/*----------------------------------*/

	if( jQrs('.panel-scrolling').length > 0) {
		jQrs('.panel-scrolling .panel-body').slimScroll({
			height: '430px',
			wheelStep: 2,
		});
	}

	if( jQrs('#panel-scrolling-demo').length > 0) {
		jQrs('#panel-scrolling-demo .panel-body').slimScroll({
			height: '175px',
			wheelStep: 2,
		});
	}

	/*-----------------------------------/
	/*	TODO LIST
	/*----------------------------------*/

	jQrs('.todo-list input').change( function() {
		if( jQrs(this).prop('checked') ) {
			jQrs(this).parents('li').addClass('completed');
		}else {
			jQrs(this).parents('li').removeClass('completed');
		}
	});


	/*-----------------------------------/
	/* TOASTR NOTIFICATION
	/*----------------------------------*/

	if(jQrs('#toastr-demo').length > 0) {
		toastr.options.timeOut = "false";
		toastr.options.closeButton = true;
		toastr['info']('Hi there, this is notification demo with HTML support. So, you can add HTML elements like <a href="#">this link</a>');

		jQrs('.btn-toastr').on('click', function() {
			jQrscontext = jQrs(this).data('context');
			jQrsmessage = jQrs(this).data('message');
			jQrsposition = jQrs(this).data('position');

			if(jQrscontext == '') {
				jQrscontext = 'info';
			}

			if(jQrsposition == '') {
				jQrspositionClass = 'toast-left-top';
			} else {
				jQrspositionClass = 'toast-' + jQrsposition;
			}

			toastr.remove();
			toastr[jQrscontext](jQrsmessage, '' , { positionClass: jQrspositionClass });
		});

		jQrs('#toastr-callback1').on('click', function() {
			jQrsmessage = jQrs(this).data('message');

			toastr.options = {
				"timeOut": "300",
				"onShown": function() { alert('onShown callback'); },
				"onHidden": function() { alert('onHidden callback'); }
			}

			toastr['info'](jQrsmessage);
		});

		jQrs('#toastr-callback2').on('click', function() {
			jQrsmessage = jQrs(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"onclick": function() { alert('onclick callback'); },
			}

			toastr['info'](jQrsmessage);

		});

		jQrs('#toastr-callback3').on('click', function() {
			jQrsmessage = jQrs(this).data('message');

			toastr.options = {
				"timeOut": "10000",
				"closeButton": true,
				"onCloseClick": function() { alert('onCloseClick callback'); }
			}

			toastr['info'](jQrsmessage);
		});
	}
});

// toggle function
jQrs.fn.clickToggle = function( f1, f2 ) {
	return this.each( function() {
		var clicked = false;
		jQrs(this).bind('click', function() {
			if(clicked) {
				clicked = false;
				return f2.apply(this, arguments);
			}

			clicked = true;
			return f1.apply(this, arguments);
		});
	});

}


