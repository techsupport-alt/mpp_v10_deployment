/* ====================================== */
/* FORMS COMPONENT */
/* Handles all form functionality */
/* ====================================== */

import { NIGERIAN_STATES, VOLUNTEER_AREAS, AVAILABILITY_OPTIONS, PRAYER_TIME_SLOTS, CONFIG } from '../constants.js';
import { DOM, Form, API, Notification, Animation } from '../utils.js';

/**
 * Forms manager component
 */
export class FormsManager {
    constructor() {
        this.init();
    }
    
    /**
     * Initialize all forms
     */
    init() {
        this.initNewsletterForm();
        this.initVolunteerForm();
        this.initPrayerForm();
    }
    
    /**
     * Initialize newsletter signup form
     */
    initNewsletterForm() {
        const form = DOM.getElementById('newsletter-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleNewsletterSubmit(e));
        }
    }
    
    /**
     * Initialize volunteer registration form
     */
    initVolunteerForm() {
        const form = DOM.getElementById('volunteer-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleVolunteerSubmit(e));
        }
    }
    
    /**
     * Initialize prayer signup form
     */
    initPrayerForm() {
        const form = DOM.getElementById('prayer-signup-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handlePrayerSubmit(e));
        }
    }
    
    /**
     * Handle newsletter form submission
     */
    async handleNewsletterSubmit(e) {
        e.preventDefault();
        
        const email = DOM.getElementById('newsletter-email')?.value;
        
        if (!email) {
            Notification.error('Please enter your email address');
            return;
        }
        
        if (!Form.validateEmail(email)) {
            Notification.error('Please enter a valid email address');
            return;
        }
        
        this.showLoading();
        
        try {
            // Simulate API call for now
            await API.simulateSubmission(CONFIG.FORM_SIMULATION_DELAY);
            
            this.hideLoading();
            this.showNewsletterSuccess();
            Form.clearForm(e.target);
            
        } catch (error) {
            this.hideLoading();
            Notification.error('Failed to subscribe. Please try again.');
            console.error('Newsletter submission error:', error);
        }
    }
    
    /**
     * Handle volunteer form submission
     */
    async handleVolunteerSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        // Basic validation
        if (!formData.get('name') || !formData.get('email') || !formData.get('phone')) {
            Notification.error('Please fill in all required fields');
            return;
        }
        
        if (!Form.validateEmail(formData.get('email'))) {
            Notification.error('Please enter a valid email address');
            return;
        }
        
        this.showLoading();
        
        try {
            // Try to submit to actual backend first
            try {
                const response = await API.submitForm('backend/api/volunteer-signup.php', formData);
                this.hideLoading();
                
                if (response.success) {
                    Notification.success('Thank you for volunteering! We will contact you soon.');
                    Form.clearForm(e.target);
                } else {
                    Notification.error(response.message || 'An error occurred. Please try again.');
                }
            } catch (apiError) {
                // Fallback to simulation if backend not available
                await API.simulateSubmission(CONFIG.FORM_SIMULATION_DELAY);
                this.hideLoading();
                Notification.success('Thank you for volunteering! We will contact you soon.');
                Form.clearForm(e.target);
            }
            
        } catch (error) {
            this.hideLoading();
            Notification.error('Failed to submit application. Please try again.');
            console.error('Volunteer submission error:', error);
        }
    }
    
    /**
     * Handle prayer signup form submission
     */
    async handlePrayerSubmit(e) {
        e.preventDefault();
        
        const formData = new FormData(e.target);
        
        // Basic validation
        if (!formData.get('name') || !formData.get('email') || !formData.get('phone')) {
            Notification.error('Please fill in all required fields');
            return;
        }
        
        if (!Form.validateEmail(formData.get('email'))) {
            Notification.error('Please enter a valid email address');
            return;
        }
        
        this.showLoading();
        
        try {
            // Try to submit to actual backend first
            try {
                const response = await API.submitForm('backend/api/prayer-signup.php', formData);
                this.hideLoading();
                
                if (response.success) {
                    Notification.success('Thank you for joining the prayer movement! You will receive updates and prayer points via email.');
                    Form.clearForm(e.target);
                } else {
                    Notification.error(response.message || 'An error occurred. Please try again.');
                }
            } catch (apiError) {
                // Fallback to simulation if backend not available
                await API.simulateSubmission(CONFIG.FORM_SIMULATION_DELAY);
                this.hideLoading();
                Notification.success('Thank you for joining the prayer movement! You will receive updates and prayer points via email.');
                Form.clearForm(e.target);
            }
            
        } catch (error) {
            this.hideLoading();
            Notification.error('Failed to submit registration. Please try again.');
            console.error('Prayer submission error:', error);
        }
    }
    
    /**
     * Generate prayer form HTML
     */
    generatePrayerFormHTML() {
        return `
            <section class="py-20 bg-background relative overflow-hidden">
                <div class="container mx-auto px-6">
                    <div class="max-w-4xl mx-auto">
                        <!-- Form Header -->
                        <div class="text-center mb-12 fade-in-up">
                            <h1 class="font-heading text-4xl md:text-5xl font-bold text-foreground mb-6">
                                Join the Prayer Movement
                            </h1>
                            <div class="w-24 h-1 bg-[var(--mmp-orange)] mx-auto rounded-full mb-8"></div>
                            <p class="text-xl text-muted-foreground max-w-3xl mx-auto">
                                Commit to being part of the 24/7 prayer coverage for Nigeria's transformation
                            </p>
                        </div>

                        <!-- Prayer Form -->
                        <div class="bg-card rounded-3xl p-8 border border-[var(--mmp-orange)]/20 shadow-xl fade-in-up fade-in-delay-200">
                            <form id="prayer-signup-form" class="space-y-6">
                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-card-foreground font-medium mb-2">Full Name *</label>
                                        <input type="text" name="name" required class="w-full bg-input-background border-2 border-border text-foreground placeholder:text-muted-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3">
                                    </div>
                                    <div>
                                        <label class="block text-card-foreground font-medium mb-2">Email Address *</label>
                                        <input type="email" name="email" required class="w-full bg-input-background border-2 border-border text-foreground placeholder:text-muted-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3">
                                    </div>
                                </div>

                                <div class="grid md:grid-cols-2 gap-6">
                                    <div>
                                        <label class="block text-card-foreground font-medium mb-2">Phone Number *</label>
                                        <input type="tel" name="phone" required class="w-full bg-input-background border-2 border-border text-foreground placeholder:text-muted-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3">
                                    </div>
                                    <div>
                                        <label class="block text-card-foreground font-medium mb-2">State *</label>
                                        <select name="state" required class="w-full bg-input-background border-2 border-border text-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3">
                                            <option value="">Select State</option>
                                            ${NIGERIAN_STATES.map(state => `<option value="${state}">${state}</option>`).join('')}
                                        </select>
                                    </div>
                                </div>

                                <div>
                                    <label class="block text-card-foreground font-medium mb-2">Church/Organization</label>
                                    <input type="text" name="church" class="w-full bg-input-background border-2 border-border text-foreground placeholder:text-muted-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3">
                                </div>

                                <div>
                                    <label class="block text-card-foreground font-medium mb-2">Preferred Prayer Time Slot</label>
                                    <select name="prayer_time" class="w-full bg-input-background border-2 border-border text-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3">
                                        <option value="">Select Time Slot</option>
                                        ${PRAYER_TIME_SLOTS.map(slot => `<option value="${slot.value}">${slot.label}</option>`).join('')}
                                    </select>
                                </div>

                                <div>
                                    <label class="block text-card-foreground font-medium mb-2">Prayer Commitment</label>
                                    <textarea name="commitment" rows="4" placeholder="Share your heart for prayer and what you're believing God for in this movement..." class="w-full bg-input-background border-2 border-border text-foreground placeholder:text-muted-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-xl px-4 py-3"></textarea>
                                </div>

                                <button type="submit" class="w-full bg-[var(--mmp-orange)] hover:bg-[var(--mmp-orange-dark)] text-white px-8 py-4 rounded-xl text-lg transition-all duration-300 hover-lift font-heading font-semibold">
                                    <i data-lucide="heart" class="w-6 h-6 mr-3 inline"></i>
                                    Join the Prayer Movement
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </section>
        `;
    }
    
    /**
     * Show newsletter success message
     */
    showNewsletterSuccess() {
        const container = DOM.getElementById('newsletter-form-container');
        if (container) {
            container.innerHTML = `
                <div class="bg-[var(--mmp-orange-cream)] dark:bg-[var(--mmp-orange)]/20 border border-[var(--mmp-orange)]/50 rounded-2xl p-8 fade-in-scale">
                    <div class="flex items-center justify-center gap-4">
                        <i data-lucide="check-circle" class="w-10 h-10 text-[var(--mmp-orange)] breathe"></i>
                        <span class="text-card-foreground font-semibold text-xl font-heading">Successfully subscribed!</span>
                    </div>
                </div>
            `;
            
            Animation.initIcons();
            
            // Reset form after 3 seconds
            setTimeout(() => {
                location.reload();
            }, 3000);
        }
    }
    
    /**
     * Show loading spinner
     */
    showLoading() {
        const spinner = DOM.getElementById('loading-spinner');
        if (spinner) {
            DOM.removeClass(spinner, 'hidden');
        }
    }
    
    /**
     * Hide loading spinner
     */
    hideLoading() {
        const spinner = DOM.getElementById('loading-spinner');
        if (spinner) {
            DOM.addClass(spinner, 'hidden');
        }
    }
}