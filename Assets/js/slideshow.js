let slideIndex = 0;
let direction = 1;

function showSlides() {
    const slides = document.querySelectorAll('.slides img');
    const slidesContainer = document.querySelector('.slides');
    slidesContainer.style.transform = `translateX(${-slideIndex * 100}%)`;
}

function autoSlide() {
    const slides = document.querySelectorAll('.slides img');
    if (direction === 1 && slideIndex === slides.length - 1) {
        direction = -1;
    } else if (direction === -1 && slideIndex === 0) {
        direction = 1;
    }
    slideIndex = (slideIndex + direction + slides.length) % slides.length;
    showSlides();
}

document.addEventListener('DOMContentLoaded', () => {
    showSlides();
    setInterval(autoSlide, 7000);

    document.querySelector('.prev').addEventListener('click', prevSlide);
    document.querySelector('.next').addEventListener('click', nextSlide);
});