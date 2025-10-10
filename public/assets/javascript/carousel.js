let currentSlideIndex = 0;
const $slides = $('.carousel-slide');
const $indicators = $('.indicator');

function showSlide(index) {
    $slides.removeClass('active');
    $indicators.removeClass('active');
    
    if ($slides.eq(index).length) {
        $slides.eq(index).addClass('active');
        $indicators.eq(index).addClass('active');
    }
}

function nextSlide() {
    currentSlideIndex = (currentSlideIndex + 1) % $slides.length;
    showSlide(currentSlideIndex);
}

function previousSlide() {
    currentSlideIndex = (currentSlideIndex - 1 + $slides.length) % $slides.length;
    showSlide(currentSlideIndex);
}

function currentSlide(index) {
    currentSlideIndex = index - 1;
    showSlide(currentSlideIndex);
}

setInterval(() => {
    if ($slides.length > 1) {
        nextSlide();
    }
}, 5000);
