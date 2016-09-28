//+++++++++++++++++++++++++++++++++++
//									+
//		      main.js				+
//									+
//+++++++++++++++++++++++++++++++++++


function initSlide() {
	var swiper = new Swiper('.swiper-container', {
        pagination: '.swiper-pagination',
        nextButton: '.swiper-button-next',
        prevButton: '.swiper-button-prev',
        paginationClickable: true,
        spaceBetween: 30,
        centeredSlides: true,
        autoplay: 2500,
        autoplayDisableOnInteraction: false
    });
}

$(document).ready(function() {
	//初始化Slide。
	initSlide();

	setTimeout("rotate()", 1000)

});

function rotate() {
	Img.rotate('order', 360);
}


var Img = function() {
	var T$ = function(id) {
		return document.getElementById(id);
	}
	var ua = navigator.userAgent, isIE = /msie/i.test(ua) && !window.opera;
	var i = 0, sinDeg = 0, cosDeg = 0, timer = null;
	var rotate = function(target, degree) {
		target = T$(target);
		var orginW = target.clientWidth, orginH = target.clientHeight;
		clearInterval(timer);
		function run(angle) {
			if (isIE) {// IE
				cosDeg = Math.cos(angle * Math.PI / 180);
				sinDeg = Math.sin(angle * Math.PI / 180);
				with (target.filters.item(0)) {
					M11 = M22 = cosDeg;
					M12 = -( M21 = sinDeg);
				}
				target.style.top = (orginH - target.offsetHeight) / 2 + 'px';
				target.style.left = (orginW - target.offsetWidth) / 2 + 'px';
			} else if (target.style.MozTransform !== undefined) {// Mozilla
				target.style.MozTransform = 'rotate(' + angle + 'deg)';
			} else if (target.style.OTransform !== undefined) {// Opera
				target.style.OTransform = 'rotate(' + angle + 'deg)';
			} else if (target.style.webkitTransform !== undefined) {// Chrome Safari
				target.style.webkitTransform = 'rotate(' + angle + 'deg)';
			} else {
				target.style.transform = "rotate(" + angle + "deg)";
			}
		}

		timer = setInterval(function() {
			i += 10;
			run(i);
			if (i > degree - 1) {
				i = 0;
				clearInterval(timer);
			}
		}, 10);
	}
	return {
		rotate : rotate
	}
}();
