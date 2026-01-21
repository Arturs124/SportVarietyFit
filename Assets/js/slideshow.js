let slideIndex = 0;
let direction = 1;
let slides = [];
let slidesContainer = null;

function showSlides() {
    slidesContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
}

function nextSlide() {
    slideIndex = (slideIndex + 1) % slides.length;
    showSlides();
}

function prevSlide() {
    slideIndex = (slideIndex - 1 + slides.length) % slides.length;
    showSlides();
}

function autoSlide() {
    if (direction === 1 && slideIndex === slides.length - 1) {
        direction = -1;
    } else if (direction === -1 && slideIndex === 0) {
        direction = 1;
    }
    slideIndex += direction;
    showSlides();
}

document.addEventListener('DOMContentLoaded', () => {
    slides = document.querySelectorAll('.slides img');
    slidesContainer = document.querySelector('.slides');

    if (!slides.length || !slidesContainer) return;

    showSlides();
    setInterval(autoSlide, 7000);

    const prevBtn = document.querySelector('.prev');
    const nextBtn = document.querySelector('.next');

    if (prevBtn) prevBtn.addEventListener('click', prevSlide);
    if (nextBtn) nextBtn.addEventListener('click', nextSlide);
});