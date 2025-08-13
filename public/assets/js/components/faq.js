/* ====================================== */
/* FAQ COMPONENT */
/* Handles FAQ accordion functionality */
/* ====================================== */

import { FAQS, CONFIG } from '../constants.js';
import { DOM, Animation } from '../utils.js';

/**
 * FAQ accordion component
 */
export class FAQComponent {
    constructor() {
        this.faqs = FAQS;
        this.openItems = ['what-is-mmp']; // Initially open FAQ
        this.container = null;
        
        this.init();
    }
    
    /**
     * Initialize the FAQ component
     */
    init() {
        this.container = DOM.getElementById('faq-container');
        if (this.container) {
            this.render();
        }
    }
    
    /**
     * Render all FAQ items
     */
    render() {
        if (!this.container) return;
        
        this.container.innerHTML = this.faqs.map((faq, index) => 
            this.renderFAQItem(faq, index)
        ).join('');

        // Reinitialize icons
        Animation.initIcons();
    }
    
    /**
     * Render individual FAQ item
     */
    renderFAQItem(faq, index) {
        const isOpen = this.openItems.includes(faq.id);
        const delay = index * CONFIG.ANIMATION_DELAY_INCREMENT;
        
        return `
            <div class="bg-card border-2 border-border rounded-2xl overflow-hidden transition-all duration-300 hover:border-[var(--mmp-orange)] hover:shadow-xl fade-in-up" style="animation-delay: ${delay}s">
                <button onclick="window.faqComponent.toggle('${faq.id}')" class="w-full p-6 text-left flex items-center justify-between hover:bg-[var(--mmp-orange-pale)]/10 dark:hover:bg-[var(--mmp-orange)]/10 transition-colors duration-200">
                    <div class="flex-1">
                        <div class="flex items-center gap-3 mb-2">
                            <span class="text-xs font-medium text-[var(--mmp-white)] bg-[var(--mmp-brown)] px-2 py-1 rounded-full font-heading">
                                ${faq.category}
                            </span>
                        </div>
                        <h3 class="font-heading font-semibold text-card-foreground leading-tight">
                            ${faq.question}
                        </h3>
                    </div>
                    <div class="ml-4">
                        ${isOpen 
                            ? '<i data-lucide="chevron-up" class="w-5 h-5 text-[var(--mmp-orange)]"></i>'
                            : '<i data-lucide="chevron-down" class="w-5 h-5 text-muted-foreground"></i>'
                        }
                    </div>
                </button>
                
                ${isOpen ? this.renderFAQContent(faq) : ''}
            </div>
        `;
    }
    
    /**
     * Render FAQ content when expanded
     */
    renderFAQContent(faq) {
        return `
            <div class="px-6 pb-6 fade-in-up">
                <p class="text-muted-foreground leading-relaxed">
                    ${faq.answer}
                </p>
            </div>
        `;
    }
    
    /**
     * Toggle FAQ item open/closed state
     */
    toggle(faqId) {
        if (this.openItems.includes(faqId)) {
            this.openItems = this.openItems.filter(id => id !== faqId);
        } else {
            this.openItems.push(faqId);
        }
        
        this.render();
    }
    
    /**
     * Open specific FAQ item
     */
    open(faqId) {
        if (!this.openItems.includes(faqId)) {
            this.openItems.push(faqId);
            this.render();
        }
    }
    
    /**
     * Close specific FAQ item
     */
    close(faqId) {
        this.openItems = this.openItems.filter(id => id !== faqId);
        this.render();
    }
    
    /**
     * Open all FAQ items
     */
    openAll() {
        this.openItems = this.faqs.map(faq => faq.id);
        this.render();
    }
    
    /**
     * Close all FAQ items
     */
    closeAll() {
        this.openItems = [];
        this.render();
    }
    
    /**
     * Search FAQs by question or answer
     */
    search(query) {
        if (!query) return this.faqs;
        
        const searchTerm = query.toLowerCase();
        return this.faqs.filter(faq => 
            faq.question.toLowerCase().includes(searchTerm) ||
            faq.answer.toLowerCase().includes(searchTerm) ||
            faq.category.toLowerCase().includes(searchTerm)
        );
    }
    
    /**
     * Filter FAQs by category
     */
    filterByCategory(category) {
        if (!category) return this.faqs;
        return this.faqs.filter(faq => faq.category === category);
    }
    
    /**
     * Get all categories
     */
    getCategories() {
        return [...new Set(this.faqs.map(faq => faq.category))];
    }
}