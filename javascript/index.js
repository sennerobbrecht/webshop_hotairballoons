let carouselIndex = 0;
    const carousel = document.querySelector('.product-carousel');
    const carouselCards = document.querySelectorAll('.product-card');

    function scrollCarousel(direction) {
        carouselIndex += direction;

        if (carouselIndex < 0) {
            carouselIndex = carouselCards.length - 5;
        } else if (carouselIndex >= carouselCards.length) {
            carouselIndex = 0;
        }

        const cardWidth = carouselCards[0].offsetWidth;
        carousel.style.transform = `translateX(-${carouselIndex * cardWidth}px)`;
    }