// Main JavaScript Application
class MPPApp {
    constructor() {
        this.currentPage = 'home';
        this.isDarkMode = true; // Set to true for default dark mode
        this.testimonials = [
            {
                id: 1,
                quote: "During the 83-hour marathon praise, I witnessed the most powerful move of God in my life. Chains were broken, healing manifested, and my family was completely restored. This movement is changing Nigeria!",
                name: "Pastor Sarah Adebayo",
                title: "Lagos State Coordinator",
                color: "orange"
            },
            {
                id: 2,
                quote: "The prayer coverage across Nigeria has been unprecedented. In our state alone, we've seen over 200 salvations and countless miracles. God is moving mightily through this 84-day movement!",
                name: "Evangelist John Okafor",
                title: "Anambra State Leader",
                color: "brown"
            },
            {
                id: 3,
                quote: "My barrenness of 12 years was broken during the Day 3 Reach 4 Christ prayers. Today, I carry a testimony of God's faithfulness. Every prayer warrior needs to be part of this movement!",
                name: "Mrs. Grace Yakubu",
                title: "Prayer Partner, Kaduna",
                color: "orange"
            }
        ];
        this.currentTestimonial = 0;
        this.testimonialInterval = null;
        
        this.faqs = [
            {
                id: 'what-is-mpp',
                question: 'What is Marathon Praise & Prayer (MPP)?',
                answer: 'MPP is a 84-day continuous prayer and worship movement calling believers across Nigeria to join in 24/7 intercession for national transformation and spiritual awakening. It brings together churches, individuals, and communities in unified prayer for revival.',
                category: 'General'
            },
            {
                id: 'when-does-event-start',
                question: 'When does the 84-day marathon start?',
                answer: 'The 84-day Marathon Praise & Prayer officially begins on [Start Date] and runs continuously for 84 days until [End Date]. Each day features multiple prayer sessions and worship gatherings across the nation.',
                category: 'Schedule'
            },
            {
                id: 'who-can-participate',
                question: 'Who can participate in MPP?',
                answer: 'Everyone is welcome! MPP is open to all believers regardless of denomination, age, or location. Whether you\'re an individual, part of a church, or leading a community group, you can join this movement of prayer and worship.',
                category: 'Participation'
            },
            {
                id: 'how-to-join',
                question: 'How can I join the prayer sessions?',
                answer: 'You can join through multiple ways: attend physical gatherings in your area, participate in online prayer sessions, sign up for a specific prayer time slot, or join the 24/7 prayer chain from your location.',
                category: 'Participation'
            },
            {
                id: 'volunteer-opportunities',
                question: 'What volunteer opportunities are available?',
                answer: 'We have various volunteer roles including prayer coordination, event organization, social media support, technical assistance, worship team participation, and local community mobilization. Each role contributes to the success of this movement.',
                category: 'Volunteering'
            },
            {
                id: 'prayer-points',
                question: 'What can I expect from the daily prayer points?',
                answer: 'Daily prayer points focus on national transformation, spiritual awakening, government leaders, economic breakthrough, security, unity, and church revival. These are distributed through our newsletter, website, and social media channels.',
                category: 'Prayer'
            }
        ];
        this.openFAQs = ['what-is-mpp'];
        
        this.init();
    }

    init() {
        this.initDarkMode();
        this.initNavigation();
        this.initAnimations();
        this.initTestimonialSlider();
        this.initFAQs();
        this.initNewsletterForm();
        this.initVolunteerForm();
        this.initMobileMenu();
        
        // Initialize Lucide icons after DOM manipulation
        setTimeout(() => {
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
        }, 100);
    }

    // Dark Mode functionality
    initDarkMode() {
        const stored = localStorage.getItem('dark-mode');
        const prefersDark = window.matchMedia('(prefers-color-scheme: dark)').matches;
        // Set dark mode as default (true by default)
        this.isDarkMode = stored ? stored === 'true' : true;
        
        if (this.isDarkMode) {
            document.documentElement.classList.add('dark');
            this.updateDarkModeIcon();
        }

        // Desktop dark mode toggle - commented out for now
        // const toggle = document.getElementById('dark-mode-toggle');
        // if (toggle) {
        //     toggle.addEventListener('click', () => this.toggleDarkMode());
        // }

        // Mobile dark mode toggle - commented out for now
        // const mobileToggle = document.getElementById('mobile-dark-mode-toggle');
        // if (mobileToggle) {
        //     mobileToggle.addEventListener('click', () => this.toggleDarkMode());
        // }
    }

    toggleDarkMode() {
        this.isDarkMode = !this.isDarkMode;
        document.documentElement.classList.toggle('dark', this.isDarkMode);
        localStorage.setItem('dark-mode', this.isDarkMode.toString());
        this.updateDarkModeIcon();
    }

    updateDarkModeIcon() {
        // Desktop icons
        const darkIcon = document.getElementById('dark-icon');
        const lightIcon = document.getElementById('light-icon');
        
        if (darkIcon && lightIcon) {
            if (this.isDarkMode) {
                darkIcon.classList.add('hidden');
                lightIcon.classList.remove('hidden');
            } else {
                darkIcon.classList.remove('hidden');
                lightIcon.classList.add('hidden');
            }
        }

        // Mobile icons
        const mobileDarkIcons = document.querySelectorAll('.mobile-dark-mode-icon');
        const mobileLightIcons = document.querySelectorAll('.mobile-light-mode-icon');
        
        mobileDarkIcons.forEach(icon => {
            if (this.isDarkMode) {
                icon.classList.add('hidden');
            } else {
                icon.classList.remove('hidden');
            }
        });

        mobileLightIcons.forEach(icon => {
            if (this.isDarkMode) {
                icon.classList.remove('hidden');
            } else {
                icon.classList.add('hidden');
            }
        });
    }

    // Navigation functionality
    initNavigation() {
        window.navigateTo = (page) => {
            this.currentPage = page;
            this.showPage(page);
            window.scrollTo(0, 0);
        };

        window.scrollToSection = (sectionId) => {
            if (this.currentPage !== 'home') {
                this.navigateTo('home');
                setTimeout(() => {
                    this.scrollToElement(sectionId);
                }, 100);
            } else {
                this.scrollToElement(sectionId);
            }
        };
    }

    showPage(page) {
        const pages = document.querySelectorAll('.page-content');
        pages.forEach(p => p.classList.add('hidden'));
        
        const targetPage = document.getElementById(`${page}-page`);
        if (targetPage) {
            targetPage.classList.remove('hidden');
            
            if (page === 'pray') {
                this.loadPrayerForm();
            }
        }
    }

    scrollToElement(elementId) {
        const element = document.getElementById(elementId);
        if (element) {
            element.scrollIntoView({ behavior: 'smooth' });
        }
    }

    // Responsive navigation functionality
    initMobileMenu() {
        const toggle = document.getElementById('mobile-menu-toggle');
        const menu = document.getElementById('mobile-nav');
        const desktopNav = document.getElementById('desktop-nav');
        const mediumNav = document.getElementById('medium-nav');
        const desktopCta = document.getElementById('desktop-cta');
        
        if (toggle && menu) {
            // Mobile menu toggle functionality
            toggle.addEventListener('click', () => {
                menu.classList.toggle('hidden');
                
                // Animate hamburger icon
                const menuIcon = toggle.querySelector('[data-lucide="menu"]');
                const closeIcon = toggle.querySelector('[data-lucide="x"]');
                
                if (menu.classList.contains('hidden')) {
                    // Show menu icon, hide close icon
                    if (menuIcon) menuIcon.style.display = 'block';
                    if (closeIcon) closeIcon.style.display = 'none';
                } else {
                    // Show close icon, hide menu icon
                    if (menuIcon) menuIcon.style.display = 'none';
                    if (closeIcon) closeIcon.style.display = 'block';
                }
            });
            
            // Close mobile menu when clicking outside
            document.addEventListener('click', (event) => {
                if (!toggle.contains(event.target) && !menu.contains(event.target)) {
                    menu.classList.add('hidden');
                    const menuIcon = toggle.querySelector('[data-lucide="menu"]');
                    const closeIcon = toggle.querySelector('[data-lucide="x"]');
                    if (menuIcon) menuIcon.style.display = 'block';
                    if (closeIcon) closeIcon.style.display = 'none';
                }
            });
            
            // Close mobile menu on window resize if screen becomes large enough
            window.addEventListener('resize', () => {
                if (window.innerWidth >= 1024) { // lg breakpoint
                    menu.classList.add('hidden');
                    const menuIcon = toggle.querySelector('[data-lucide="menu"]');
                    const closeIcon = toggle.querySelector('[data-lucide="x"]');
                    if (menuIcon) menuIcon.style.display = 'block';
                    if (closeIcon) closeIcon.style.display = 'none';
                }
            });
        }
        
        // Enhanced responsive detection
        this.initResponsiveNavigation();
    }
    
    // Advanced responsive navigation detection
    initResponsiveNavigation() {
        const nav = document.querySelector('nav .container > div');
        const desktopNav = document.getElementById('desktop-nav');
        const mediumNav = document.getElementById('medium-nav');
        const desktopCta = document.getElementById('desktop-cta');
        const toggle = document.getElementById('mobile-menu-toggle');
        
        function checkNavOverflow() {
            if (!nav || !desktopNav || !mediumNav) return;
            
            const logoElement = nav.querySelector('.flex.items-center.space-x-3');
            if (!logoElement) return;
            
            const navWidth = nav.clientWidth;
            const logoWidth = logoElement.offsetWidth;
            const ctaWidth = desktopCta ? desktopCta.offsetWidth : 0;
            const toggleWidth = toggle ? toggle.offsetWidth : 0;
            
            const availableSpace = navWidth - logoWidth - ctaWidth - toggleWidth - 100; // 100px padding
            const desktopNavWidth = desktopNav.scrollWidth;
            const mediumNavWidth = mediumNav.scrollWidth;
            
            // If desktop nav doesn't fit, show medium nav
            if (availableSpace < desktopNavWidth && window.innerWidth >= 1024) {
                desktopNav.classList.add('hidden');
                mediumNav.classList.remove('hidden', 'lg:hidden');
                mediumNav.classList.add('flex');
            }
            // If medium nav doesn't fit either, force mobile menu
            else if (availableSpace < mediumNavWidth && window.innerWidth >= 768) {
                desktopNav.classList.add('hidden');
                mediumNav.classList.add('hidden');
                toggle.classList.remove('lg:hidden');
                toggle.classList.add('block');
            }
            // Normal responsive behavior
            else {
                desktopNav.classList.remove('hidden');
                mediumNav.classList.add('hidden', 'lg:hidden');
                toggle.classList.add('lg:hidden');
                toggle.classList.remove('block');
            }
        }
        
        // Check on load and resize
        window.addEventListener('load', checkNavOverflow);
        window.addEventListener('resize', checkNavOverflow);
        
        // Use ResizeObserver for more precise detection
        if (window.ResizeObserver && nav) {
            const resizeObserver = new ResizeObserver(checkNavOverflow);
            resizeObserver.observe(nav);
        }
    }

    // Animation functionality
    initAnimations() {
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
    }

    // Testimonial slider functionality
    initTestimonialSlider() {
        this.renderTestimonials();
        this.startTestimonialAutoplay();
    }

    renderTestimonials() {
        const container = document.getElementById('testimonials-container');
        if (!container) return;

        const currentTestimony = this.testimonials[this.currentTestimonial];
        
        container.innerHTML = `
            <div class="text-center">
                <div class="mb-8">
                    <div class="w-20 h-20 ${currentTestimony.color === 'orange' ? 'bg-[var(--mmp-orange)]' : 'bg-[var(--mmp-brown)]'} rounded-full flex items-center justify-center mx-auto mb-6 breathe">
                        <i data-lucide="quote" class="w-10 h-10 text-white"></i>
                    </div>
                    
                    <blockquote class="text-xl md:text-2xl text-muted-foreground italic leading-relaxed mb-8 max-w-4xl mx-auto">
                        "${currentTestimony.quote}"
                    </blockquote>
                    
                    <div class="flex items-center justify-center gap-4">
                        <div class="w-16 h-16 ${currentTestimony.color === 'orange' ? 'bg-[var(--mmp-brown)]' : 'bg-[var(--mmp-orange)]'} rounded-full flex items-center justify-center">
                            <i data-lucide="user" class="w-8 h-8 text-white"></i>
                        </div>
                        <div class="text-left">
                            <div class="font-semibold text-card-foreground text-lg font-heading">${currentTestimony.name}</div>
                            <div class="${currentTestimony.color === 'orange' ? 'text-[var(--mmp-orange)]' : 'text-[var(--mmp-brown)]'} text-sm">
                                ${currentTestimony.title}
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Dots -->
            <div class="flex justify-center space-x-2 mt-8">
                ${this.testimonials.map((_, index) => `
                    <button onclick="app.setTestimonial(${index})" class="w-3 h-3 rounded-full transition-all duration-300 ${
                        index === this.currentTestimonial 
                            ? 'bg-[var(--mmp-orange)]' 
                            : 'bg-muted hover:bg-[var(--mmp-brown)]'
                    }"></button>
                `).join('')}
            </div>

            <!-- Navigation Arrows -->
            <button onclick="app.previousTestimonial()" class="absolute left-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-card/90 hover:bg-[var(--mmp-orange)] border border-border rounded-full flex items-center justify-center transition-colors group shadow-lg">
                <i data-lucide="chevron-left" class="w-6 h-6 text-muted-foreground group-hover:text-white"></i>
            </button>
            <button onclick="app.nextTestimonial()" class="absolute right-4 top-1/2 -translate-y-1/2 w-12 h-12 bg-card/90 hover:bg-[var(--mmp-orange)] border border-border rounded-full flex items-center justify-center transition-colors group shadow-lg">
                <i data-lucide="chevron-right" class="w-6 h-6 text-muted-foreground group-hover:text-white"></i>
            </button>
        `;

        // Reinitialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    nextTestimonial() {
        this.currentTestimonial = (this.currentTestimonial + 1) % this.testimonials.length;
        this.renderTestimonials();
    }

    previousTestimonial() {
        this.currentTestimonial = (this.currentTestimonial - 1 + this.testimonials.length) % this.testimonials.length;
        this.renderTestimonials();
    }

    setTestimonial(index) {
        this.currentTestimonial = index;
        this.renderTestimonials();
    }

    startTestimonialAutoplay() {
        if (this.testimonialInterval) {
            clearInterval(this.testimonialInterval);
        }
        
        this.testimonialInterval = setInterval(() => {
            this.nextTestimonial();
        }, 8000);
    }

    // FAQ functionality
    initFAQs() {
        this.renderFAQs();
    }

    renderFAQs() {
        const container = document.getElementById('faq-container');
        if (!container) return;

        container.innerHTML = this.faqs.map((faq, index) => `
            <div class="bg-card border-2 border-border rounded-2xl overflow-hidden transition-all duration-300 hover:border-[var(--mmp-orange)] hover:shadow-xl fade-in-up" style="animation-delay: ${index * 0.1}s">
                <button onclick="app.toggleFAQ('${faq.id}')" class="w-full p-6 text-left flex items-center justify-between hover:bg-[var(--mmp-orange-pale)]/10 dark:hover:bg-[var(--mmp-orange)]/10 transition-colors duration-200">
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
                        ${this.openFAQs.includes(faq.id) 
                            ? '<i data-lucide="chevron-up" class="w-5 h-5 text-[var(--mmp-orange)]"></i>'
                            : '<i data-lucide="chevron-down" class="w-5 h-5 text-muted-foreground"></i>'
                        }
                    </div>
                </button>
                
                ${this.openFAQs.includes(faq.id) ? `
                    <div class="px-6 pb-6 fade-in-up">
                        <p class="text-muted-foreground leading-relaxed">
                            ${faq.answer}
                        </p>
                    </div>
                ` : ''}
            </div>
        `).join('');

        // Reinitialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    toggleFAQ(id) {
        if (this.openFAQs.includes(id)) {
            this.openFAQs = this.openFAQs.filter(item => item !== id);
        } else {
            this.openFAQs.push(id);
        }
        this.renderFAQs();
    }

    // Newsletter form functionality
    initNewsletterForm() {
        const form = document.getElementById('newsletter-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleNewsletterSubmit(e));
        }
    }

    handleNewsletterSubmit(e) {
        e.preventDefault();
        const email = document.getElementById('newsletter-email')?.value;
        
        if (email) {
            this.showLoading();
            
            // Simulate API call
            setTimeout(() => {
                this.hideLoading();
                this.showNewsletterSuccess();
                document.getElementById('newsletter-email').value = '';
            }, 1000);
        }
    }

    showNewsletterSuccess() {
        const container = document.getElementById('newsletter-form-container');
        if (container) {
            container.innerHTML = `
                <div class="bg-[var(--mmp-orange-cream)] dark:bg-[var(--mmp-orange)]/20 border border-[var(--mmp-orange)]/50 rounded-2xl p-8 fade-in-scale">
                    <div class="flex items-center justify-center gap-4">
                        <i data-lucide="check-circle" class="w-10 h-10 text-[var(--mmp-orange)] breathe"></i>
                        <span class="text-card-foreground font-semibold text-xl font-heading">Successfully subscribed!</span>
                    </div>
                </div>
            `;
            
            if (typeof lucide !== 'undefined') {
                lucide.createIcons();
            }
            
            // Reset form after 3 seconds
            setTimeout(() => {
                location.reload();
            }, 3000);
        }
    }

    // Volunteer form functionality
    initVolunteerForm() {
        const form = document.getElementById('volunteer-form');
        if (form) {
            form.addEventListener('submit', (e) => this.handleVolunteerSubmit(e));
        }
    }

    handleVolunteerSubmit(e) {
        e.preventDefault();
        this.showLoading();
        
        const formData = new FormData(e.target);
        
        // Convert FormData to JSON object with proper transformations
        const data = {};
        for (let [key, value] of formData.entries()) {
            data[key] = value;
        }
        
        // data.full_name = `${data.first_name} ${data.last_name}`.trim();
        // delete data.first_name;
        // delete data.last_name;
        
        // Handle checkboxes explicitly (they need to be boolean values)
        const checkboxes = e.target.querySelectorAll('input[type="checkbox"]');
        checkboxes.forEach(checkbox => {
            data[checkbox.name] = checkbox.checked;
        });
        
        // Ensure required fields have values
        data.country = data.country || 'Nigeria';
        data.start_date = data.start_date || new Date().toISOString().split('T')[0];
        
        // Convert duration to string if it's a number
        if (typeof data.duration === 'number') {
            data.duration = data.duration.toString();
        }
        
        // Send to PHP backend as JSON
        fetch('backend/api/volunteer-signup.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(data)
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading();
            if (data.success) {
                this.showSuccessMessage('Thank you for volunteering! We will contact you soon.');
                e.target.reset();
            } else {
                this.showErrorMessage(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            this.hideLoading();
            this.showErrorMessage('An error occurred. Please try again.');
        });
    }

    // Prayer form functionality
    loadPrayerForm() {
        const container = document.getElementById('prayer-form-container');
        if (!container) return;

        container.innerHTML = `
            <section class="py-20 bg-background relative overflow-hidden">
                <div class="container mx-auto px-6">
                    <div class="max-w-4xl mx-auto">
                        <!-- Header -->
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
                                            <option value="Abia">Abia</option>
                                            <option value="Adamawa">Adamawa</option>
                                            <option value="Akwa Ibom">Akwa Ibom</option>
                                            <option value="Anambra">Anambra</option>
                                            <option value="Bauchi">Bauchi</option>
                                            <option value="Bayelsa">Bayelsa</option>
                                            <option value="Benue">Benue</option>
                                            <option value="Borno">Borno</option>
                                            <option value="Cross River">Cross River</option>
                                            <option value="Delta">Delta</option>
                                            <option value="Ebonyi">Ebonyi</option>
                                            <option value="Edo">Edo</option>
                                            <option value="Ekiti">Ekiti</option>
                                            <option value="Enugu">Enugu</option>
                                            <option value="FCT">FCT</option>
                                            <option value="Gombe">Gombe</option>
                                            <option value="Imo">Imo</option>
                                            <option value="Jigawa">Jigawa</option>
                                            <option value="Kaduna">Kaduna</option>
                                            <option value="Kano">Kano</option>
                                            <option value="Katsina">Katsina</option>
                                            <option value="Kebbi">Kebbi</option>
                                            <option value="Kogi">Kogi</option>
                                            <option value="Kwara">Kwara</option>
                                            <option value="Lagos">Lagos</option>
                                            <option value="Nasarawa">Nasarawa</option>
                                            <option value="Niger">Niger</option>
                                            <option value="Ogun">Ogun</option>
                                            <option value="Ondo">Ondo</option>
                                            <option value="Osun">Osun</option>
                                            <option value="Oyo">Oyo</option>
                                            <option value="Plateau">Plateau</option>
                                            <option value="Rivers">Rivers</option>
                                            <option value="Sokoto">Sokoto</option>
                                            <option value="Taraba">Taraba</option>
                                            <option value="Yobe">Yobe</option>
                                            <option value="Zamfara">Zamfara</option>
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
                                        <option value="12am-3am">12:00 AM - 3:00 AM</option>
                                        <option value="3am-6am">3:00 AM - 6:00 AM</option>
                                        <option value="6am-9am">6:00 AM - 9:00 AM</option>
                                        <option value="9am-12pm">9:00 AM - 12:00 PM</option>
                                        <option value="12pm-3pm">12:00 PM - 3:00 PM</option>
                                        <option value="3pm-6pm">3:00 PM - 6:00 PM</option>
                                        <option value="6pm-9pm">6:00 PM - 9:00 PM</option>
                                        <option value="9pm-12am">9:00 PM - 12:00 AM</option>
                                        <option value="flexible">Flexible</option>
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

        // Initialize prayer form submission
        const prayerForm = document.getElementById('prayer-signup-form');
        if (prayerForm) {
            prayerForm.addEventListener('submit', (e) => this.handlePrayerSubmit(e));
        }

        // Reinitialize icons
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
    }

    handlePrayerSubmit(e) {
        e.preventDefault();
        this.showLoading();
        
        const formData = new FormData(e.target);
        
        // Send to PHP backend
        fetch('backend/api/prayer-signup.php', {
            method: 'POST',
            body: formData
        })
        .then(response => response.json())
        .then(data => {
            this.hideLoading();
            if (data.success) {
                this.showSuccessMessage('Thank you for joining the prayer movement! You will receive updates and prayer points via email.');
                e.target.reset();
            } else {
                this.showErrorMessage(data.message || 'An error occurred. Please try again.');
            }
        })
        .catch(error => {
            this.hideLoading();
            this.showErrorMessage('An error occurred. Please try again.');
        });
    }

    // Utility functions
    showLoading() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.classList.remove('hidden');
        }
    }

    hideLoading() {
        const spinner = document.getElementById('loading-spinner');
        if (spinner) {
            spinner.classList.add('hidden');
        }
    }

    showSuccessMessage(message) {
        this.showNotification(message, 'success');
    }

    showErrorMessage(message) {
        this.showNotification(message, 'error');
    }

    showNotification(message, type) {
        const notification = document.createElement('div');
        notification.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm fade-in-scale ${
            type === 'success' 
                ? 'bg-green-500 text-white' 
                : 'bg-red-500 text-white'
        }`;
        notification.innerHTML = `
            <div class="flex items-center gap-3">
                <i data-lucide="${type === 'success' ? 'check-circle' : 'x-circle'}" class="w-5 h-5"></i>
                <span>${message}</span>
            </div>
        `;
        
        document.body.appendChild(notification);
        
        if (typeof lucide !== 'undefined') {
            lucide.createIcons();
        }
        
        setTimeout(() => {
            notification.remove();
        }, 5000);
    }
}

// Initialize the application
let app;
document.addEventListener('DOMContentLoaded', () => {
    app = new MPPApp();
});

// Global functions for onclick handlers
window.navigateTo = (page) => app && app.navigateTo ? app.navigateTo(page) : null;
window.scrollToSection = (section) => app && app.scrollToSection ? app.scrollToSection(section) : null;
