/**=====================
    Custom Swiper js
==========================**/
var swiper = new Swiper(".mySwiper", {
    effect: "cards",
    grabCursor: true,
    loop: true,
    centeredSlides: true,
    autoplay: {
        delay: 3000,
    },
    pagination: {
        el: ".swiper-pagination",
        dynamicBullets: true,
        clickable: true,
    },
});

var sliderNine = new Swiper(".slider-9", {
    slidesPerView: 8,
    spaceBetween: 20,
    loop: true,
    freeMode: true,
});