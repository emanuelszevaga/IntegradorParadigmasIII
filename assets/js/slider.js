const slides = document.querySelectorAll('.slide');
const dots = document.querySelectorAll('.dot');
let current = 0;

function showSlide(index) {
    slides.forEach((slide, i) => {
        slide.classList.remove('active');
        dots[i].classList.remove('active');
        if (i === index) {
        slide.classList.add('active');
        dots[i].classList.add('active');
        }
    });
    }

    function nextSlide() {
    current = (current + 1) % slides.length;
    showSlide(current);
    }

    dots.forEach((dot, i) => {
    dot.addEventListener('click', () => {
        current = i;
        showSlide(current);
    });
    });

// auto-slide cada 6 segundos
setInterval(nextSlide, 6000);

    let slideInterval = setInterval(nextSlide, 6000);

    const sliderContainer = document.querySelector('.slider-container');
    if (sliderContainer) {
    sliderContainer.addEventListener('mouseenter', () => clearInterval(slideInterval));
    sliderContainer.addEventListener('mouseleave', () => {
        slideInterval = setInterval(nextSlide, 6000);
    });
    }