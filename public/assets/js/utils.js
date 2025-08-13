/* ====================================== */
/* UTILITY FUNCTIONS */
/* Helper functions for the MPP Application */
/* ====================================== */

/**
 * DOM manipulation utilities
 */
export const DOM = {
    /**
     * Get element by ID with error handling
     */
    getElementById: (id) => {
        const element = document.getElementById(id);
        if (!element) {
            console.warn(`Element with ID '${id}' not found`);
        }
        return element;
    },

    /**
     * Get elements by class name
     */
    getElementsByClass: (className) => {
        return document.getElementsByClassName(className);
    },

    /**
     * Add class to element safely
     */
    addClass: (element, className) => {
        if (element && element.classList) {
            element.classList.add(className);
        }
    },

    /**
     * Remove class from element safely
     */
    removeClass: (element, className) => {
        if (element && element.classList) {
            element.classList.remove(className);
        }
    },

    /**
     * Toggle class on element safely
     */
    toggleClass: (element, className) => {
        if (element && element.classList) {
            element.classList.toggle(className);
        }
    },

    /**
     * Smooth scroll to element
     */
    scrollToElement: (elementId) => {
        const element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    },

    /**
     * Scroll to top of page
     */
    scrollToTop: () => {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
};

/**
 * Animation utilities
 */
export const Animation = {
    /**
     * Add fade-in animation with delay
     */
    addFadeIn: (element, delay = 0) => {
        if (element) {
            element.style.animationDelay = `${delay}s`;
            element.classList.add('fade-in-up');
        }
    },

    /**
     * Initialize Lucide icons
     */
    initIcons: () => {
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    },

    /**
     * Setup intersection observer for scroll animations
     */
    setupScrollAnimations: () => {
        const observerOptions = {
            threshold: 0.1,
            rootMargin: "0px 0px -50px 0px",
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add("animate-in");
                }
            });
        }, observerOptions);

        const fadeElements = document.querySelectorAll('[class*="fade-in"]');
        fadeElements.forEach((el) => observer.observe(el));

        return observer;
    }
};

/**
 * Local storage utilities
 */
export const Storage = {
    /**
     * Get item from localStorage with error handling
     */
    getItem: (key) => {
        try {
            return localStorage.getItem(key);
        } catch (error) {
            console.warn(`Error getting localStorage item '${key}':`, error);
            return null;
        }
    },

    /**
     * Set item in localStorage with error handling
     */
    setItem: (key, value) => {
        try {
            localStorage.setItem(key, value);
            return true;
        } catch (error) {
            console.warn(`Error setting localStorage item '${key}':`, error);
            return false;
        }
    },

    /**
     * Remove item from localStorage
     */
    removeItem: (key) => {
        try {
            localStorage.removeItem(key);
            return true;
        } catch (error) {
            console.warn(`Error removing localStorage item '${key}':`, error);
            return false;
        }
    }
};

/**
 * Form utilities
 */
export const Form = {
    /**
     * Get form data as object
     */
    getFormData: (form) => {
        if (!form) return {};
        
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (data[key]) {
                // Handle multiple values (like checkboxes)
                if (Array.isArray(data[key])) {
                    data[key].push(value);
                } else {
                    data[key] = [data[key], value];
                }
            } else {
                data[key] = value;
            }
        }
        
        return data;
    },

    /**
     * Validate email format
     */
    validateEmail: (email) => {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    },

    /**
     * Validate phone number (Nigerian format)
     */
    validatePhone: (phone) => {
        const phoneRegex = /^(\+234|0)[789][01]\d{8}$/;
        return phoneRegex.test(phone.replace(/\s+/g, ''));
    },

    /**
     * Clear form fields
     */
    clearForm: (form) => {
        if (form && form.reset) {
            form.reset();
        }
    }
};

/**
 * Notification utilities
 */
export const Notification = {
    /**
     * Show notification message
     */
    show: (message, type = 'info', duration = 5000) => {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm fade-in-scale ${
            type === 'success' 
                ? 'bg-green-500 text-white' 
                : type === 'error'
                ? 'bg-red-500 text-white'
                : 'bg-blue-500 text-white'
        }`;
        
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <i data-lucide="${type === 'success' ? 'check-circle' : type === 'error' ? 'x-circle' : 'info'}" class="w-5 h-5"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        // Initialize icons for the notification
        Animation.initIcons();
        
        // Remove notification after duration
        setTimeout(() => {
            notification.remove();
        }, duration);
    },

    /**
     * Show success notification
     */
    success: (message, duration) => {
        Notification.show(message, 'success', duration);
    },

    /**
     * Show error notification
     */
    error: (message, duration) => {
        Notification.show(message, 'error', duration);
    }
};

/**
 * API utilities for form submissions
 */
export const API = {
    /**
     * Submit form data to backend
     */
    submitForm: async (endpoint, formData) => {
        try {
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });
            
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            
            return await response.json();
        } catch (error) {
            console.error('API submission error:', error);
            throw error;
        }
    },

    /**
     * Simulate API call (for development)
     */
    simulateSubmission: (delay = 1000) => {
        return new Promise((resolve) => {
            setTimeout(() => {
                resolve({ success: true, message: 'Form submitted successfully!' });
            }, delay);
        });
    }
};

/**
 * Theme utilities
 */
export const Theme = {
    /**
     * Check if user prefers dark mode
     */
    prefersDarkMode: () => {
        return window.matchMedia('(prefers-color-scheme: dark)').matches;
    },

    /**
     * Apply dark mode to document
     */
    applyDarkMode: (isDark) => {
        if (isDark) {
            document.documentElement.classList.add('dark');
        } else {
            document.documentElement.classList.remove('dark');
        }
    },

    /**
     * Toggle between dark and light mode
     */
    toggle: (currentMode) => {
        const newMode = !currentMode;
        Theme.applyDarkMode(newMode);
        return newMode;
    }
};

/**
 * Debounce utility for performance optimization
 */
export const debounce = (func, wait) => {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
};

/**
 * Array utilities
 */
export const ArrayUtils = {
    /**
     * Get next index in array (with wrapping)
     */
    getNextIndex: (currentIndex, arrayLength) => {
        return (currentIndex + 1) % arrayLength;
    },

    /**
     * Get previous index in array (with wrapping)
     */
    getPreviousIndex: (currentIndex, arrayLength) => {
        return (currentIndex - 1 + arrayLength) % arrayLength;
    },

    /**
     * Shuffle array randomly
     */
    shuffle: (array) => {
        const shuffled = [...array];
        for (let i = shuffled.length - 1; i > 0; i--) {
            const j = Math.floor(Math.random() * (i + 1));
            [shuffled[i], shuffled[j]] = [shuffled[j], shuffled[i]];
        }
        return shuffled;
    }
};

/**
 * String utilities
 */
export const StringUtils = {
    /**
     * Capitalize first letter of string
     */
    capitalize: (str) => {
        return str.charAt(0).toUpperCase() + str.slice(1);
    },

    /**
     * Truncate string to specified length
     */
    truncate: (str, length, suffix = '...') => {
        if (str.length <= length) return str;
        return str.substring(0, length) + suffix;
    },

    /**
     * Generate unique ID
     */
    generateId: (prefix = '') => {
        return prefix + Math.random().toString(36).substr(2, 9);
    }
};

/**
 * Device detection utilities
 */
export const Device = {
    /**
     * Check if device is mobile
     */
    isMobile: () => {
        return window.innerWidth <= 768;
    },

    /**
     * Check if device is tablet
     */
    isTablet: () => {
        return window.innerWidth > 768 && window.innerWidth <= 1024;
    },

    /**
     * Check if device is desktop
     */
    isDesktop: () => {
        return window.innerWidth > 1024;
    },

    /**
     * Get device type as string
     */
    getType: () => {
        if (Device.isMobile()) return 'mobile';
        if (Device.isTablet()) return 'tablet';
        return 'desktop';
    }
};