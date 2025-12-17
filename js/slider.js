class Slider {
    constructor() {
        this.slides = [];
        this.currentSlide = 0; // index of the current group (page) of slides
        this.isAuto = true;
        this.autoInterval = null;
        this.slideInterval = 7000; // 7 seconds for slower sliding
        
        this.init();
    }
    
    init() {
        // Get slides from products or use default
        this.loadSlides();
        
        // Setup controls
        this.setupControls();
        
        // Start auto slide
        this.startAutoSlide();
    }
    
    loadSlides() {
        // For demo, you can add default slides or fetch from products
        const slidesContainer = document.querySelector('.slides');
        const indicators = document.querySelector('.slide-indicators');
        
        // This would be replaced with actual product images
        const defaultSlides = [
            { image: 'images/men5.jpg', title: 'Summer Collection' },
            { image: 'images/lady3.jpg', title: 'Winter Fashion' },
            { image: 'images/kid2.jpg', title: 'Casual Wear' },
            { image: 'images/men4.jpg', title: '' },
            { image: 'images/lady.jpg', title: 'Street wear' },
            { image: 'images/ladie1.jpg', title: 'Street Fashion' },
            { image: 'images/men3.jpg', title: 'Evening' },
            { image: 'images/kid3.jpg', title: 'Style' },
            { image: 'images/kid1.jpg', title: 'EStyle' },
            { image: 'images/fashion.jpg', title: 'EveningS' },
            { image: 'images/fashion1.jpg', title: 'Eveyle' },
            { image: 'images/kid4.jpg', title: 'ing Style' },
            { image: 'images/fashion3.jpg', title: 'Evening Style' },
            { image: 'images/fashion4.jpg', title: 'Evening Style' },
            { image: 'images/fashion5.jpg', title: 'Evening Style' },
            { image: 'images/fashion6.jpg', title: 'Evening Style' },
            { image: 'images/fashion10.jpg', title: 'Evening Style' },
            { image: 'images/fashion7.jpg', title: 'Evening Style' },
            { image: 'images/fashion8.jpg', title: 'Evening Style' },
            { image: 'images/fashion9.jpg', title: 'Evening Style' },
            { image: 'images/men.jpg', title: 'Evening Style' },
        ];
        
        this.slides = defaultSlides;
        
        // Create slides
        this.slides.forEach((slide, index) => {
            const slideElement = document.createElement('div');
            slideElement.className = 'slide';
            slideElement.innerHTML = `
                <img src="${slide.image}" alt="${slide.title}">
                <div class="slide-content">
                    <h2>${slide.title}</h2>
                </div>
            `;
            slidesContainer.appendChild(slideElement);
        });

        // Create indicators per group (3 slides per group)
        const slidesPerGroup = 3;
        const groupCount = Math.ceil(this.slides.length / slidesPerGroup);

        for (let i = 0; i < groupCount; i++) {
            const indicator = document.createElement('div');
            indicator.className = `indicator ${i === 0 ? 'active' : ''}`;
            indicator.dataset.index = i;
            indicator.addEventListener('click', () => this.goToSlide(i));
            indicators.appendChild(indicator);
        }
    }
    
    setupControls() {
        // Previous button
        document.querySelector('.prev-btn').addEventListener('click', () => {
            this.prevSlide();
            this.restartAutoSlide();
        });
        
        // Next button
        document.querySelector('.next-btn').addEventListener('click', () => {
            this.nextSlide();
            this.restartAutoSlide();
        });
    }
    
    startAutoSlide() {
        if (this.isAuto) {
            this.stopAutoSlide();
            this.autoInterval = setInterval(() => {
                this.nextSlide();
            }, this.slideInterval);
        }
    }
    
    stopAutoSlide() {
        if (this.autoInterval) {
            clearInterval(this.autoInterval);
            this.autoInterval = null;
        }
    }
    
    restartAutoSlide() {
        if (this.isAuto) {
            this.stopAutoSlide();
            this.startAutoSlide();
        }
    }
    
    nextSlide() {
        const slidesPerGroup = 3;
        const groupCount = Math.ceil(this.slides.length / slidesPerGroup);
        this.currentSlide = (this.currentSlide + 1) % groupCount;
        this.updateSlider();
    }
    
    prevSlide() {
        const slidesPerGroup = 3;
        const groupCount = Math.ceil(this.slides.length / slidesPerGroup);
        this.currentSlide = (this.currentSlide - 1 + groupCount) % groupCount;
        this.updateSlider();
    }
    
    goToSlide(index) {
        this.currentSlide = index;
        this.updateSlider();
        this.restartAutoSlide();
    }
    
    updateSlider() {
        const slidesContainer = document.querySelector('.slides');
        const indicators = document.querySelectorAll('.indicator');
        const slidesPerGroup = 3;
        
        // Move slides by group: each group is 100% width (contains 3 slides at 33.3333% each)
        slidesContainer.style.transform = `translateX(-${this.currentSlide * 100}%)`;
        
        // Update indicators
        indicators.forEach((indicator, index) => {
            indicator.classList.toggle('active', index === this.currentSlide);
        });
    }
}

// Initialize slider when DOM is loaded
document.addEventListener('DOMContentLoaded', () => {
    new Slider();
});