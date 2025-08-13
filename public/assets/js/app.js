/* ====================================== */
/* MAIN MPP APPLICATION */
/* Coordinating class for the Marathon Praise & Prayer website */
/* ====================================== */

import { UPDATES, CONFIG } from './constants.js';
import { DOM, Storage, Theme, Animation, Notification } from './utils.js';
import { TestimonialSlider } from './components/testimonials.js';
import { FAQComponent } from './components/faq.js';
import { FormsManager } from './components/forms.js';

/**
 * Main application class that coordinates all functionality
 */
class MPPApplication {
    constructor() {
        this.currentPage = 'home';
        this.isDarkMode = true; // Set to true for default dark mode
        this.components = {};
        
        this.init();
    }
    
    /**
     * Initialize the entire application
     */
    init() {
        console.log('Initializing MPP Application...');
        
        this.initDarkMode();
        this.initNavigation();
        this.initAnimations();
        this.initComponents();
        this.initMobileMenu();
        
        // Initialize icons after DOM manipulation
        setTimeout(() => {
            Animation.initIcons();
        }, 100);
        
        console.log('MPP Application initialized successfully!');
    }
    
    /**
     * Initialize dark mode functionality
     */
    initDarkMode() {
        const stored = Storage.getItem(CONFIG.DARK_MODE_STORAGE_KEY);
        const prefersDark = Theme.prefersDarkMode();
        // Set dark mode as default (true by default)
        this.isDarkMode = stored ? stored === 'true' : true;
        
        Theme.applyDarkMode(this.isDarkMode);
        this.updateDarkModeIcon();

        // Dark mode toggle commented out for now
        // const toggle = DOM.getElementById('dark-mode-toggle');
        // if (toggle) {
        //     toggle.addEventListener('click', () => this.toggleDarkMode());
        // }
    }
    
    /**
     * Toggle dark mode
     */
    toggleDarkMode() {
        this.isDarkMode = Theme.toggle(this.isDarkMode);
        Storage.setItem(CONFIG.DARK_MODE_STORAGE_KEY, this.isDarkMode.toString());
        this.updateDarkModeIcon();
        
        console.log(`Dark mode ${this.isDarkMode ? 'enabled' : 'disabled'}`);
    }
    
    /**
     * Update dark mode toggle icon
     */
    updateDarkModeIcon() {
        const darkIcon = DOM.getElementById('dark-icon');
        const lightIcon = DOM.getElementById('light-icon');
        
        if (darkIcon && lightIcon) {
            if (this.isDarkMode) {
                DOM.addClass(darkIcon, 'hidden');
                DOM.removeClass(lightIcon, 'hidden');
            } else {
                DOM.removeClass(darkIcon, 'hidden');
                DOM.addClass(lightIcon, 'hidden');
            }
        }
    }
    
    /**
     * Initialize navigation system
     */
    initNavigation() {
        // Set up global navigation functions
        window.navigateTo = (page) => {
            this.currentPage = page;
            this.showPage(page);
            DOM.scrollToTop();
        };

        window.scrollToSection = (sectionId) => {
            if (this.currentPage !== 'home') {
                this.navigateTo('home');
                setTimeout(() => {
                    DOM.scrollToElement(sectionId);
                }, 100);
            } else {
                DOM.scrollToElement(sectionId);
            }
        };
    }
    
    /**
     * Show specific page
     */
    showPage(page) {
        const pages = document.querySelectorAll('.page-content');
        pages.forEach(p => DOM.addClass(p, 'hidden'));
        
        const targetPage = DOM.getElementById(`${page}-page`);
        if (targetPage) {
            DOM.removeClass(targetPage, 'hidden');
            
            if (page === 'pray') {
                this.loadPrayerForm();
            }
        }
    }
    
    /**
     * Initialize mobile menu
     */
    initMobileMenu() {
        const toggle = DOM.getElementById('mobile-menu-toggle');
        const menu = DOM.getElementById('mobile-nav');
        
        if (toggle && menu) {
            toggle.addEventListener('click', () => {
                DOM.toggleClass(menu, 'hidden');
            });
        }
    }
    
    /**
     * Initialize scroll-triggered animations
     */
    initAnimations() {
        this.animationObserver = Animation.setupScrollAnimations();
    }
    
    /**
     * Initialize all components
     */
    initComponents() {
        // Initialize testimonial slider
        this.components.testimonials = new TestimonialSlider();
        window.testimonialSlider = this.components.testimonials;
        
        // Initialize FAQ component
        this.components.faq = new FAQComponent();
        window.faqComponent = this.components.faq;
        
        // Initialize forms manager
        this.components.forms = new FormsManager();
        
        // Initialize latest updates
        this.initLatestUpdates();
    }
    
    /**
     * Initialize latest updates section
     */
    initLatestUpdates() {
        this.renderLatestUpdates();
    }
    
    /**
     * Render latest updates
     */
    renderLatestUpdates() {
        const container = DOM.getElementById('updates-container');
        if (!container) return;

        container.innerHTML = `
            <div class="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
                ${UPDATES.map((update, index) => `
                    <div class="bg-card rounded-2xl overflow-hidden border border-[var(--mmp-${update.color})]/20 hover:border-[var(--mmp-${update.color})] transition-all duration-300 hover-lift fade-in-up shadow-lg" style="animation-delay: ${index * CONFIG.ANIMATION_DELAY_INCREMENT}s">
                        <div class="p-6">
                            <div class="flex items-center gap-3 mb-4">
                                <div class="w-10 h-10 bg-[var(--mmp-${update.color})]/20 rounded-full flex items-center justify-center">
                                    <i data-lucide="${update.icon}" class="w-5 h-5 text-[var(--mmp-${update.color})]"></i>
                                </div>
                                <div>
                                    <div class="text-xs font-medium text-[var(--mmp-${update.color})] uppercase tracking-wide">${update.type}</div>
                                    <div class="text-xs text-muted-foreground">${update.date}</div>
                                </div>
                            </div>
                            <h3 class="font-heading text-lg font-bold text-card-foreground mb-3">
                                ${update.title}
                            </h3>
                            <p class="text-muted-foreground text-sm leading-relaxed mb-4">
                                ${update.content}
                            </p>
                            <div class="flex items-center justify-between">
                                <button class="text-[var(--mmp-${update.color})] hover:text-[var(--mmp-${update.color}-dark)] font-medium text-sm transition-colors">
                                    Read More
                                </button>
                                <div class="flex items-center gap-1 text-muted-foreground text-xs">
                                    <i data-lucide="eye" class="w-4 h-4"></i>
                                    <span>${update.views}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                `).join('')}
            </div>
        `;

        Animation.initIcons();
    }
    
    /**
     * Load prayer form into prayer page
     */
    loadPrayerForm() {
        const container = DOM.getElementById('prayer-form-container');
        if (!container) return;

        container.innerHTML = this.components.forms.generatePrayerFormHTML();
        
        // Reinitialize the prayer form
        this.components.forms.initPrayerForm();
        
        Animation.initIcons();
    }
    
    /**
     * Destroy application and cleanup
     */
    destroy() {
        // Cleanup components
        if (this.components.testimonials) {
            this.components.testimonials.destroy();
        }
        
        // Cleanup observers
        if (this.animationObserver) {
            this.animationObserver.disconnect();
        }
        
        // Clear global references
        window.navigateTo = null;
        window.scrollToSection = null;
        window.testimonialSlider = null;
        window.faqComponent = null;
        
        console.log('MPP Application destroyed');
    }
}

/**
 * Initialize application when DOM is ready
 */
document.addEventListener('DOMContentLoaded', () => {
    window.app = new MPPApplication();
    
    // Initialize icons after everything is loaded
    if (typeof lucide !== 'undefined') {
        lucide.createIcons();
    }
});

/**
 * Handle page unload cleanup
 */
window.addEventListener('beforeunload', () => {
    if (window.app && window.app.destroy) {
        window.app.destroy();
    }
});

// Export for module use if needed
export default MPPApplication;