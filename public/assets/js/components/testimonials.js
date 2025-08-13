/* ====================================== */
/* TESTIMONIALS COMPONENT */
/* Handles testimonial slider functionality */
/* ====================================== */

import { TESTIMONIALS, CONFIG } from '../constants.js';
import { DOM, Animation, ArrayUtils } from '../utils.js';

/**
 * Testimonials slider component
 */
export class TestimonialSlider {
    constructor() {
        this.testimonials = TESTIMONIALS;
        this.currentIndex = 0;
        this.interval = null;
        this.container = null;
        
        this.init();
    }
    
    /**
     * Initialize the testimonial slider
     */
    init() {
        this.container = DOM.getElementById('testimonials-container');
        if (this.container) {
            this.render();
            this.startAutoplay();
        }
    }
    
    /**
     * Render the current testimonial
     */
    render() {
        if (!this.container) return;
        
        const current = this.testimonials[this.currentIndex];
        
        this.container.innerHTML = `
            <div class="text-center">
                <div class="mb-8">
                    <div class="w-20 h-20 ${current.color === 'orange' ? 'bg-[var(--mmp-orange)]' : 'bg-[var(--mmp-brown)]'} rounded-full flex items-center justify-center mx-auto mb-6 breathe">
                        <i data-lucide="quote" class="w-10 h-10 text-white"></i>
                    </div>
                    
                    <blockquote class="text-xl md:text-2xl text-muted-foreground italic leading-relaxed mb-8 max-w-4xl mx-auto">
                        "${current.quote}"
                    </blockquote>
                    
                    <div class="flex items-center justify-center gap-4">
                        <div class="w-16 h-16 ${current.color === 'orange' ? 'bg-[var(--mmp-brown)]' : 'bg-[var(--mmp-orange)]'} rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold text-card-foreground text-lg font-heading">${current.name}</div>
                            <div class="${current.color === 'orange' ? 'text-[var(--mmp-orange)]' : 'text-[var(--mmp-brown)]'} text-sm">
                                ${current.title}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            ${this.renderNavigationDots()}
            ${this.renderNavigationArrows()}
        `;

        // Reinitialize icons
        Animation.initIcons();
    }
    
    /**
     * Render navigation dots
     */
    renderNavigationDots() {
        return `
            <div class="flex justify-center space-x-2 mt-8">
                ${this.testimonials.map((_, index) => `
                    <button onclick="window.testimonialSlider.goToSlide(${index})" class="w-3 h-3 rounded-full transition-all duration-300 ${
                        index === this.currentIndex 
                            ? 'bg-[var(--mmp-orange)]' 
                            : 'bg-muted hover:bg-[var(--mmp-brown)]'
                    }"></button>
                `).join('')}
            </div>
        `;
    }
    
    /**
     * Render navigation arrows
     */
    renderNavigationArrows() {
        return `
            <button onclick="window.testimonialSlider.previous()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-card/90 hover:bg-[var(--mmp-orange)] border border-border rounded-full flex items-center justify-center transition-colors group shadow-lg">
                <i data-lucide="chevron-left" class="w-6 h-6 text-muted-foreground group-hover:text-white"></i>
            </button>
            <button onclick="window.testimonialSlider.next()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-card/90 hover:bg-[var(--mmp-orange)] border border-border rounded-full flex items-center justify-center transition-colors group shadow-lg">
                <i data-lucide="chevron-right" class="w-6 h-6 text-muted-foreground group-hover:text-white"></i>
            </button>
        `;
    }
    
    /**
     * Go to next testimonial
     */
    next() {
        this.currentIndex = ArrayUtils.getNextIndex(this.currentIndex, this.testimonials.length);
        this.render();
        this.restartAutoplay();
    }
    
    /**
     * Go to previous testimonial
     */
    previous() {
        this.currentIndex = ArrayUtils.getPreviousIndex(this.currentIndex, this.testimonials.length);
        this.render();
        this.restartAutoplay();
    }
    
    /**
     * Go to specific slide
     */
    goToSlide(index) {
        this.currentIndex = index;
        this.render();
        this.restartAutoplay();
    }
    
    /**
     * Start automatic slideshow
     */
    startAutoplay() {
        if (this.interval) {
            clearInterval(this.interval);
        }
        
        this.interval = setInterval(() => {
            this.next();
        }, CONFIG.TESTIMONIAL_AUTOPLAY_INTERVAL);
    }
    
    /**
     * Restart autoplay (called when user interacts)
     */
    restartAutoplay() {
        this.startAutoplay();
    }
    
    /**
     * Stop autoplay
     */
    stopAutoplay() {
        if (this.interval) {
            clearInterval(this.interval);
            this.interval = null;
        }
    }
    
    /**
     * Destroy the component
     */
    destroy() {
        this.stopAutoplay();
        this.container = null;
    }
}