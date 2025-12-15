class Slider {
    constructor() {
        this.slides = [];
        this.currentSlide = 0;
        this.isAuto = true;
        this.autoInterval = null;
        this.slideInterval = 4000; // 4 seconds
        
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
            { image: 'images/slide1.jpeg', title: 'Summer Collection' },
            { image: 'images/slide2.jpeg', title: 'Winter Fashion' },
            { image: 'images/slide3.jpeg', title: 'Casual Wear' },
            { image: 'images/slide4.jpeg', title: 'Evening Style' },
            { image: 'images/slide5.jpeg', title: 'Street wear' },
            { image: 'images/images.jpeg', title: 'Street Fashion' }
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
            
            // Create indicator
            const indicator = document.createElement('div');
            indicator.className = `indicator ${index === 0 ? 'active' : ''}`;
            indicator.dataset.index = index;
            indicator.addEventListener('click', () => this.goToSlide(index));
            indicators.appendChild(indicator);
        });
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
        this.currentSlide = (this.currentSlide + 1) % this.slides.length;
        this.updateSlider();
    }
    
    prevSlide() {
        this.currentSlide = (this.currentSlide - 1 + this.slides.length) % this.slides.length;
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
        
        // Move slides
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