"use strict";

(function ($) {

    /*
    // live chat
    */
	$('#live-chat-btn').on('click', function(){
	var chatbullFrameDOM = $('#chatbull-frame').contents();
	chatbullFrameDOM.find('#chat-cmodule-widget-chaticon').children('.cmodule-chat-btn').click();
	});

	/*
	// Not found search
	*/
	$(".not-found__search").submit(function(event) {
		event.preventDefault();
		var terms = $("#searchTxtKeyword").val().trim();
		if (0 != terms.length) {
			document.location = "/search/" + escape(terms);
		}
		return false;
	});

	/*
	// Search
	*/
	$('#searchTxtKeyword').on('keyup', function() {
		var val = $(this).val();
		if (val.length > 0) {
			$.ajax({
				url: '/s',
				method: 'get',
				data: {s: val},
				success: function(r) {
					var contents = "";
					for(let x in r.data) {
						contents += "<a class='suggestions__item suggestions__product' href='"+ r.data[x].link +"'>";

						contents += "<div class='suggestions__product-image'><img src='"+ r.data[x].image +"' width='96' height='96' /></div>";
						contents += "<div class='suggestions__product-info'><div class='suggestions__product-name'>"+ r.data[x].name +"</div></div>";
						contents += "<div class='suggestions__product-price'>"+ r.data[x].price +"</div>";

						contents += "</a>";
					}

					if (r.total > 5) {

						contents += "<div class='m-3'><a href='/search?s="+ val +"' class='btn btn-primary btn-block'>View All ("+ r.total +")</a></div>";

					}

					$('#search-content-error').html(contents);
				}
			});
		} else {
			$('#search-content').html("");
		}
	});
	$('#search-input').on('keyup', function() {
		var val = $(this).val();
		if (val.length > 0) {
			$.ajax({
				url: '/s',
				method: 'get',
				data: {s: val},
				success: function(r) {
					var contents = "";
					for(let x in r.data) {
						contents += "<a class='suggestions__item suggestions__product' href='"+ r.data[x].link +"'>";

						contents += "<div class='suggestions__product-image'><img src='"+ r.data[x].image +"' width='96' height='96' /></div>";
						contents += "<div class='suggestions__product-info'><div class='suggestions__product-name'>"+ r.data[x].name +"</div></div>";
						contents += "<div class='suggestions__product-price'>"+ r.data[x].price +"</div>";

						contents += "</a>";
					}

					if (r.total > 5) {

						contents += "<div class='m-3'><a href='/search?s="+ val +"' class='btn btn-primary btn-block'>View All ("+ r.total +")</a></div>";

					}

					$('#search-content').html(contents);
				}
			});
		} else {
			$('#search-content').html("");
		}
	});

	$('body').on('click', '.more-info', function(e) {
		e.preventDefault();
		console.log("test");
		$(this).popover('toggle');
	});
	$("input[name='color_cart']").click(function(){
		window.location.href=location.protocol + '//' + location.host + location.pathname+'?colorid='+$('input:radio[name=color_cart]:checked').val();
	});
	$("input[name='sub_pro']").click(function(){
		window.location.href=location.protocol + '//' + location.host + location.pathname+'?subproduct='+$('input:radio[name=sub_pro]:checked').attr('data-key');
	});
})(jQuery);

function freightCalculator() {
	var form = document.getElementById('freight-form');
    var qty = document.getElementById('product-qty-input').value;
    var startpoint=(form.starting)?form.starting.value:'';
	$.ajax({
		'url' : '/freight',
		'method': 'POST',
		'data': {
			'destination': form.destination.value,
			'mass': form.mass.value,
			'startpoint':startpoint,
			'width': form.width.value,
			'height': form.height.value,
			'depth': form.depth.value,
			'value': form.value.value,
			'type': 1,
			'currency': 'USD'
		},

		'success': function(r) {
			$('#freight-calculator-content').removeClass('d-none');
			if (r.length > 0) {
                console.log(r);
                // let popoverContentItems = ['registered', 'receipted', 'tracking', 'express', 'insurance'];
                let popoverContentItems = ['registered', 'sign_on_delivery', 'tracking', 'express', 'insurance'];

				var html = "<table class='table table-striped table-sm small'>";

                html += "<thead>";
                html += "<tr class='bg-dark text-white'>";
                html += "<th scope='col'>Price(USD)</th>";
                html += "<th scope='col'>Service</th>";
                html += "</tr>";
                html += "</thead>";

                html += "<tbody>";
                for (let i in r) {
                    let popoverContent = "<ul class=\"list-unstyled\">";

                    for(var x in popoverContentItems) {
                        popoverContent += "<li>";

                        if (r[i][popoverContentItems[x]] == 'n' || r[i][popoverContentItems[x]] == null) {
                            popoverContent += "<span class=\"mr-2\"><i class=\"fas fa-times text-danger\"></i></span>";
                        } else {
                            popoverContent += "<span class=\"mr-1\"><i class=\"fas fa-check text-success\"></i></span>";
                        }
                        popoverContent += "<span>";
                        // popoverContent += popoverContentItems[x].replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g, function (s) {
                        //     return s.toUpperCase();
                        // });

						// UPdate on 27th Aug 2023 by Swapnil
						if(popoverContentItems[x] == 'sign_on_delivery')
						{
							popoverContent += 'Signature on Delivery';
						}else if(popoverContentItems[x] == 'insurance'){
							popoverContent += 'Insurance Available'
						} else {
							popoverContent += popoverContentItems[x].replace(/(^([a-zA-Z\p{M}]))|([ -][a-zA-Z\p{M}])/g, function (s) {
								return s.toUpperCase();
							});
						}
                        popoverContent += "</span>";

                        popoverContent += "</li>";
                    }

                    popoverContent += "<li><hr/></li>";
                    popoverContent += "<li><b>Delivery Time: </b>"+ r[i]['delivery_time'] +"</li>";

                    popoverContent += "</ul>";
					if (r[i].freight_notes !== '') {
						var freight_notes = " <a href='#' class='more-info far fa-question-circle mr-2' data-toggle='popover' data-trigger='focus' data-html='true' title='Freight Notes' data-content='" + r[i].freight_notes + "' style='vertical-align:middle;color:black;font-size:14px;margin-left:2px;'></a>";
					} else {
						var freight_notes = '';
					}
                    html += "<tr>";
                    html += "<td>$"+ (qty * r[i].offer_price).toFixed(2) +"</td>";
                    html += "<td>"+ r[i].service +" &nbsp; <a href='#' style='vertical-align:middle;' data-toggle='popover' data-trigger='focus' data-html='true' title='Information' data-content='"+ popoverContent +"' class='more-info badge badge-primary badge-pill'>!</a>"+ freight_notes +"</td>";
                    html += "</tr>";
                }

                html += "</tbody>";

                html += "</table>";

                $('#freight-calculator-content').html(html);
			} else {
				$('#freight-calculator-content').html('No service available for shipping to the destination.');
			}
		}
	});

	return false;
}
function quoteCalculator() {
	var form = document.getElementById('quote-form');
	var startpoint=(form.starting)?form.starting.value:'';
	var shipping=(form.shipping)?form.shipping.value:'';
	var location=(form.location)?form.location.value:'';
	var product_id=(form.product_id)?form.product_id.value:'';
	var cubic_weight=(form.cubic_weight)?form.cubic_weight.value:1;
	var shipping_weight=(form.shipping_weight)?form.shipping_weight.value:1;
	$.ajax({
		'url' : '/freight',
		'method': 'POST',
		'data': {
			'destination': form.destination.value,
			'location': location,
			'id': product_id,
			'startpoint':startpoint,
			'startpoint':startpoint,
			'shipping':shipping,
			'cubic_weight':cubic_weight,
			'shipping_weight':shipping_weight,
			'type': 2,
			'currency': 'USD'
		},

		'success': function(r) {
			console.log(r);
			$('#freight-quote-content').removeClass('d-none');
			if (r.length > 0) {
                let popoverContentItems = ['registered', 'receipted', 'tracking', 'express', 'insurance'];

				var html = "<table class='table table-striped table-sm small'>";

                html += "<thead>";
                html += "<tr class='bg-dark text-white'>";
                html += "<th scope='col'>Price(USD)</th>";
                html += "<th scope='col'>Service</th>";
                html += "<th scope='col'>Size/Weight</th>";
                html += "<th scope='col'>Destination</th>";
                html += "</tr>";
                html += "</thead>";

                html += "<tbody>";
                for (let i in r) {
                
                    html += "<tr>";
					if(shipping == 'Air')
					{
						html += "<td>$"+ r[i].price_display_air +' <i class="far fa-question-circle fa-question-circle-checkout" data-toggle="tooltip" data-placement="top" title="" data-original-title="This shipment total freight costs was '+r[i].price+', the per kg cost was $'+r[i].price_per_kg+' usd"></i>'+"</td>";
					} else {
						html += "<td>"+ r[i].price_display +' <i class="far fa-question-circle fa-question-circle-checkout" data-toggle="tooltip" data-placement="top" title="" data-original-title="This shipment total freight costs was $'+r[i].sale_price+' USD, the average cubic meter cost was '+r[i].price_org+'"></i>'+"</td>";
					}
                    html += "<td>"+ r[i].type +"-"+r[i].container_flags+"</td>";
					if(shipping == 'Air')
					{
						html += "<td>"+r[i].shipping_weight+" kgs </td>";
					} else {
						html += "<td>"+ r[i].cubic_weight +" &#13221;/"+r[i].shipping_weight+" kgs </td>";
					}
                    
                    html += "<td>"+ r[i].port_from +"</td>";
                    html += "</tr>";
                }
                html += "<tr>";
				if(shipping == 'Air')
				{
					html += "<td colspan='4'>The volume/shipping of this machine is <strong style='color:red;'>"+parseFloat(shipping_weight).toFixed(2)+" kgs</strong>. Prices shown above are averaged based on the per kg air freight cost of each shipment. Please use above as a guide only, freight will need to be negotiated with the transport companies at time of shipment.";
				} else {
					html += "<td colspan='4'>The cubic metre size of this machine is <strong style='color:red;'>"+parseFloat(cubic_weight).toFixed(2)+" m3</strong>. Prices shown above are averaged based on the m3 cost of each shipment. Please use above as a guide only, freight will need to be negotiated with the transport companies at time of shipment.";
				}
                
                html += "</td>"; 
                html += "</tbody>";

                html += "</table>";
         
                $('#freight-quote-content').html(html);
                $('[data-toggle="tooltip"]').tooltip();
			} else {
				$('#freight-quote-content').html('<p>Sorry we do not have current freight quotations</p><p>For single machine orders, the estimates for transport costs are from $400 USD (from China) and $1600 USD (from Japan) as minimum. However, if you plan to order multiple games, there are great savings when ordering in container volumns (freight per machine averages between $200 to $350 USD). Please contact our team for exact freight costs.</p>');

			}
		}
	});

	return false;
}

function currencyCalculator(){
	var form = document.getElementById('currency-calculator');
	$.ajax({
		'url' : '/calculate-currency',
		'method': 'POST',
		'data': {
			'amount': form.amount.value,
			'from_currency': form.from_currency.value,
			'to_currency': form.to_currency.value,
		},

		'success': function(r) {
			if(r){
				$('#currency_result').val(r);
			}else{
				$('#currency_result').val('');
			}
		}
	});
}
function closewanted(key){
	if(key==1){
		window.location.href='/';
	}else{
		window.location.href='/wanted';
	}
}

$('#destination-method-input').change(function(){
	if($(this).val()=='Air'){
		$('.air_option').show();
		$('.sea_option').hide();
		$('.sea_option_dest').hide();
		$('.air_option_dest').show();
		const first = document.getElementsByClassName('air_option')[0];
		$('#starting-input-freight').val(first.value);
		const second = document.getElementsByClassName('air_option_dest')[0];
		$('#destination-quote-input').val(second.value);
	}else{
		$('.air_option').hide();
		$('.sea_option').show();
		$('.sea_option_dest').show();
		$('.air_option_dest').hide();
		const first = document.getElementsByClassName('sea_option')[0];
		$('#starting-input-freight').val(first.value);
		const second = document.getElementsByClassName('sea_option_dest')[0];
		$('#destination-quote-input').val(second.value);
	}
});

function showAllVideos(l) {
	$(".video").show();
	$(".video_members").hide();
	$(".video-nav-item.active").removeClass("active");
	$(l).parent().addClass("active");
}
function showMembersVideos(l) {
	$(".video_public").hide();
	$(".video_members").show();
	$(".video-nav-item.active").removeClass("active");
	$(l).parent().addClass("active");
}
function showAllOwnerVideos(l) {
	$(".videoowner").show();
	$(".nowonervideo").hide();
	$(".video-nav-item.active").removeClass("active");
	$(l).parent().addClass("active");
}
function loadYoutubeowner(elem, id) {
	$("#youtube-owner-frame").attr("src", "https://www.youtube.com/embed/" + id);
	$(".active.videoowner").removeClass("active");
	$(elem).parent().addClass("active");
}
function showPublicownerVideos(l) {
	$(".videoowner").hide();
	$(".nowonervideo").hide();
	$(".video_owner_public").show();
	$(".video-nav-item.active").removeClass("active");
	$(l).parent().addClass("active");
}
function showOwnersVideos(l) {
	$(".videoowner").hide();
	$(".video_owner_members").show();
	$(".video-nav-item.active").removeClass("active");
	$(l).parent().addClass("active");
}
$(document).ready(function(){
	$(".videoowner").hide();
	$(".video_owner_members").show();
})
function showPublicVideos(l) {
	$(".video_members").hide();
	$(".video_public").show();
	$(".video-nav-item.active").removeClass("active");
	$(l).parent().addClass("active");
}
function loadYoutube(elem, id) {
	$("#youtube-frame").attr("src", "https://www.youtube.com/embed/" + id);
	$(".active.video").removeClass("active");
	$(elem).parent().addClass("active");
}

$(document).ready(function(){
	$('#machine-slider').slick({
	  centerMode: false,
	  infinite: false,
	  centerPadding: '60px',
	  slidesToShow: 3,
	  arrows:true,
	});
});
function openComment(elem) {
	$(elem).parent().next().toggle();
	return false;
}
$(function(){
	$("#news_notification").change(function () {
    var isChecked = $("#news_notification").is(":checked");
	    if (isChecked) {
	        var data=1;
	    } else {
	         var data=0;
	    }
	    $.ajax({
			url:     "/ajax/notification_subscribe.php",
			type:    "post",
			data:    { data: data },
			success: function (r) {
				alert(r);
			}
		});
	});
});
function comment(btn, bid, cid,product_id) {
	var comment = $(btn).parent().find("textarea[name=comment]").val();
	if (comment.length > 0) {
		$.ajax({
			url:        "/ajax/comment.php",
			type:       "post",
			data:       { comment: comment, blog_id: bid, reply: cid ,product_id:product_id},
			dataType:   "json",
			beforeSend: function () {
				$(btn).attr("disabled", "disabled");
			},
			success:    function (r) {
				if (r["status"] == "200") {
					if (parseInt(cid) > 0) {
						$(btn).parents(".comments").after("<div class='comments-container'>" + r["response"] + "</div>");
						$(btn).parent().hide();
					} else {
						$(btn).next().children(".comments-container").prepend(r["response"]);
					}
					$(btn).removeAttr("disabled");
					$(btn).parent().find("textarea[name=comment]").val("");
					if ($(btn).prev().hasClass("alert")) {
						$(btn).prev().remove();
					}
				} else {
					alert("Comment error: Please try again.");
					location.reload();
				}
			}
		});
	} else {
		if ($(btn).prev().hasClass("alert")) {
			$(btn).prev().remove();
		}
		$(btn).before('<div class="alert alert-danger">Please say something to comment.</div>');
	}
}
function comment(btn, bid, cid,product_id) {
	var comment = $(btn).parent().find("textarea[name=comment]").val();
	if (comment.length > 0) {
		$.ajax({
			url:        "/ajax/comment.php",
			type:       "post",
			data:       { comment: comment, blog_id: bid, reply: cid ,product_id:product_id},
			dataType:   "json",
			beforeSend: function () {
				$(btn).attr("disabled", "disabled");
			},
			success:    function (r) {
				if (r["status"] == "200") {
					if (parseInt(cid) > 0) {
						$(btn).parents(".comments").after("<div class='comments-container'>" + r["response"] + "</div>");
						$(btn).parent().hide();
					} else {
						$(btn).next().children(".comments-container").prepend(r["response"]);
					}
					$(btn).removeAttr("disabled");
					$(btn).parent().find("textarea[name=comment]").val("");
					if ($(btn).prev().hasClass("alert")) {
						$(btn).prev().remove();
					}
				} else {
					alert("Comment error: Please try again.");
					location.reload();
				}
			}
		});
	} else {
		if ($(btn).prev().hasClass("alert")) {
			$(btn).prev().remove();
		}
		$(btn).before('<div class="alert alert-danger">Please say something to comment.</div>');
	}
}
function loadMoreBlogs(btn, offset) {
	$.ajax({
		url:     "/ajax/loadMoreBlogs.php",
		type:    "get",
		data:    { from: offset },
		success: function (r) {
			$(btn).parent(".load-more-blogs").replaceWith(r);
		}
	});
}
$(document).on("click",".readmore",function(e){
	var dataid=$(this).attr('dataid');
	if($('#displayless-'+dataid).hasClass('displayless')){
		$('#displayless-'+dataid).removeClass('displayless');
		$(this).html('show less');
	}else{
		$('#displayless-'+dataid).addClass('displayless');
		$(this).html('read more');
	}

});
$("#dispaly-more-number").click(function(){
	$("#more-serial-no").toggle();
});
$(document).ready(function(){
       var hash = document.location.hash;
    if (hash) {
        
        $('.product-tabs__item a[href="'+hash+'"]').click();
        $('html, body').animate({
              scrollTop: $(hash).offset().top
        }, 1000);
    }
});
function showmedef(){
	var hash='#product-tab-includedItem';
	$('.product-tabs__item a[href="'+hash+'"]').click();
    $('html, body').animate({
          scrollTop: $(hash).offset().top
    }, 1000);
}
function scrolltobuilder(){

	 $('html, body').animate({
          scrollTop: $('#product_builder_div').offset().top
    }, 1000);
}
$('.setup-instruction-image').on('click', function(e){
  $('.setup-instruction-image').css('border','')
  $(this).css('border', '1px solid #2e79ff');
});
$(document).on('click',".available_options_class",function(){
	var current_price=$('.product-price-update').html();
    var update_price=$(this).attr('data-value');
    if($(this).is(":checked")){
    	var new_price=parseFloat(current_price)+parseFloat(update_price);
    }else{
    	var new_price=parseFloat(current_price)-parseFloat(update_price);
    }
    $('.product-price-update').html(new_price.toFixed(2));
})
 $("[name='account-type']").change(function() {
    if($(this).val()=='company'){
    	$('.company-data').show();
    	$('#account-profile-edit-organization').attr('required',true);
    }else{
    	$('#account-profile-edit-organization').attr('required',false);
    	$('.company-data').hide();
    }
 });
  $("[name='courier_id']").change(function() {
    if($(this).val()==0){
    	$('.courier_name-add').show();
    	$('#courier_name').attr('required',true);
    }else{
    	$('#courier_name').attr('required',false);
    	$('.courier_name-add').hide();
    }
 });
 function updateshipingAddressType(id,obj){
 	//console.log(obj.value);
 	if(obj.value=='commercial'){
 		$('.address_type'+id).show();
    	$('#organization'+id).attr('required',true);
 	}else{
 		$('.address_type'+id).hide();
    	$('#organization'+id).attr('required',false);
 	}
 }



 $(document).ready(function () {

 
 	/* For Produyct Detail Page */ 

	var MemberCurrencyVal=parseFloat($("#membercurrencyval").val());

	if(isNaN(MemberCurrencyVal)){

	}
	else{

	var ProductPrice=parseFloat($("input[name=product_price]").val());
	var getusercurrency=$('#getusercurrency').val();

	var ConvertedCurrency=(ProductPrice*MemberCurrencyVal).toFixed(2);
	 //console.log(ConvertedCurrency);
	 $("#convertedcurrency").text(ConvertedCurrency);

	 $("#currencytooltip").tooltip('hide')
      .attr('data-original-title', "Approximate price is "+ConvertedCurrency+" "+getusercurrency+"")
      ;
       
	let currencyIconHtml=	$("#CurrencyICon").html();
		$("#CurrencyICon").remove();
		$("#labelUSD").html('  USD    '+currencyIconHtml);	

		setTimeout(function(){
			$(".currencytooltip").tooltip()
		},500)
	}



	/* end product details page  */
	
  $(function () {
        const input = $('.not-found__search-input');
        const suggestions = $('.search__dropdown--suggestions-error');

        input.on('focus', function() {
            suggestions.addClass('search__dropdown--open');
        });
        input.on('blur', function() {
            suggestions.removeClass('search__dropdown--open');
        });
    });	
		
  $("#newPassword").on('keyup', function(){
    var number = /([0-9])/;
    var alphabets = /([a-zA-Z])/;
    var special_characters = /([~,!,@,#,$,%,^,&,*,-,_,+,=,?,>,<])/;
    if ($('#newPassword').val().length < 8) {
        $('#password-strength-status').removeClass();
      // $('#password-strength-status').addClass('weak-password');
        $('#password-strength-status').html("Weak (should be atleast 8 characters.)");
    }
    else {

    	$('#password-strength-status').removeClass();
          //  $('#password-strength-status').addClass('strong-password');
           //$('#password-strength-status').html("ok");
           $('#password-strength-status').hide();

           
        // if ($('#newPassword').val().match(number) && $('#newPassword').val().match(alphabets) && $('#newPassword').val().match(special_characters)) {
        //     $('#password-strength-status').removeClass();
        //     $('#password-strength-status').addClass('strong-password');
        //     $('#password-strength-status').html("Strong");
        // } else {
        //     $('#password-strength-status').removeClass();
        //     $('#password-strength-status').addClass('medium-password');
        //     $('#password-strength-status').html("Medium (should include alphabets, numbers and special characters or some combination.)");
        // }
    }
  });
});
