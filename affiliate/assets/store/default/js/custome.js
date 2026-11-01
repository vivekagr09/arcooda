// function updateSymbol(e) {
// var selected = $(".currency-selector option:selected");
// $(".currency-symbol").text(selected.data("symbol"));
// $(".currency-amount").prop("placeholder", selected.data("placeholder"));
// $(".currency-addon-fixed").text(selected.text());
// }

// $(".currency-selector").on("change", updateSymbol);

// updateSymbol();

//     $(function () {
//         var list = $(".js-dropdown-list");
//         var link = $(".js-link");
//         link.click(function (e) {
//           e.preventDefault();
//           list.slideToggle(200);
//         });
//         list.find("li").click(function () {
//           var text = $(this).html();
//           var icon = '<i class="fa fa-chevron-down"></i>';
//           link.html(text + icon);
//           list.slideToggle(200);
//           if (text === "* Reset") {
//             link.html("Select one option" + icon);
//           }
//         });
//       });
	  
	  
	  
// 	  $(function() {
//   var list = $('.js-dropdown-list1');
//   var link = $('.js-link1');
//   link.click(function(e) {
//     e.preventDefault();
//     list.slideToggle(200);
//   });
//   list.find('li').click(function() {
//     var text = $(this).html();
//     var icon = '<i class="fa fa-chevron-down"></i>';
//     link.html(text+icon);
//     list.slideToggle(200);
//     if (text === '* Reset') {
//       link.html('Select one option'+icon);
//     }
//   });
// });


// $(document).ready(function(){
//   $(".cart-top").click(function(){
//     $(".cart-dropdown").slideToggle();
//   });
  
// });

// $(document).ready(function(){
//   $(".description-reviews-tabs a").click(function(){
//     $(".description-reviews-tabs a").removeClass('active');
//     $(this).addClass('active');
//   });
  
//   $(".checkout-payments-wrapper a").click(function(){
//     $(".checkout-payments-wrapper a").removeClass('active');
//     $(this).addClass('active');
//   });
  
//   $(".description-reviews-tabs a.rbtn").click(function(){
//     $(".discription-reviews-content").hide();
//     $(".product-reviews-all").show();
//   });
  
//   $(".description-reviews-tabs a.dbtn").click(function(){
//     $(".discription-reviews-content").show();
//     $(".product-reviews-all").hide();
//   });
  
// });


// window.onscroll = function() {myFunction()};

// var header = document.getElementById("myHeader");
// var sticky = header.offsetTop;

// function myFunction() {
//   if (window.pageYOffset > sticky) {
//     header.classList.add("sticky");
//   } else {
//     header.classList.remove("sticky");
//   }
// }

// $('.add').click(function () {
// 		if ($(this).prev().val() < 350) {
//     	$(this).prev().val(+$(this).prev().val() + 1);
// 		}
// });
// $('.sub').click(function () {
// 		if ($(this).next().val() > 1) {
//     	if ($(this).next().val() > 1) $(this).next().val(+$(this).next().val() - 1);
// 		}
// });

$('.carousel').carousel({
  interval: false,
});

if($('.custom-select').length) {
var x, i, j, l, ll, selElmnt, a, b, c;
/*look for any elements with the class "custom-select":*/
x = document.getElementsByClassName("custom-select");
l = x.length;
for (i = 0; i < l; i++) {
  selElmnt = x[i].getElementsByTagName("select")[0];
  ll = selElmnt.length;
  /*for each element, create a new DIV that will act as the selected item:*/
  a = document.createElement("DIV");
  a.setAttribute("class", "select-selected");
  a.innerHTML = selElmnt.options[selElmnt.selectedIndex].innerHTML;
  x[i].appendChild(a);
  /*for each element, create a new DIV that will contain the option list:*/
  b = document.createElement("DIV");
  b.setAttribute("class", "select-items select-hide");
  for (j = 1; j < ll; j++) {
    /*for each option in the original select element,
    create a new DIV that will act as an option item:*/
    c = document.createElement("DIV");
    c.innerHTML = selElmnt.options[j].innerHTML;
    c.addEventListener("click", function(e) {
        /*when an item is clicked, update the original select box,
        and the selected item:*/
        var y, i, k, s, h, sl, yl;
        s = this.parentNode.parentNode.getElementsByTagName("select")[0];
        sl = s.length;
        h = this.parentNode.previousSibling;
        for (i = 0; i < sl; i++) {
          if (s.options[i].innerHTML == this.innerHTML) {
            s.selectedIndex = i;
            h.innerHTML = this.innerHTML;
            y = this.parentNode.getElementsByClassName("same-as-selected");
            yl = y.length;
            for (k = 0; k < yl; k++) {
              y[k].removeAttribute("class");
            }
            this.setAttribute("class", "same-as-selected");
            break;
          }
        }
        h.click();
    });
    b.appendChild(c);
  }
  x[i].appendChild(b);

}

  a.addEventListener("click", function(e) {
      /*when the select box is clicked, close any other select boxes,
      and open/close the current select box:*/
      e.stopPropagation();
      closeAllSelect(this);
      this.nextSibling.classList.toggle("select-hide");
      this.classList.toggle("select-arrow-active");
    });
}
function closeAllSelect(elmnt) {

  var x, y, i, xl, yl, arrNo = [];
  x = document.getElementsByClassName("select-items");
  y = document.getElementsByClassName("select-selected");
  xl = x.length;
  yl = y.length;
  for (i = 0; i < yl; i++) {
    if (elmnt == y[i]) {
      arrNo.push(i)
    } else {
      y[i].classList.remove("select-arrow-active");
    }
  }
  for (i = 0; i < xl; i++) {
    if (arrNo.indexOf(i)) {
      x[i].classList.add("select-hide");
    }
  }
}

document.addEventListener("click", closeAllSelect);