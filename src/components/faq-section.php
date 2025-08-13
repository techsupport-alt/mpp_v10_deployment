<section class="py-16 md:py-24 bg-background" id="faq">
    <div class="container mx-auto px-4">
        <!-- Section Header -->
        <div class="text-center mb-16 fade-in-up">
            <h2 class="font-heading font-bold text-3xl md:text-4xl lg:text-5xl text-foreground mb-6">
                Frequently Asked Questions
            </h2>
            <p class="text-muted-foreground text-lg md:text-xl max-w-3xl mx-auto leading-relaxed">
                Find answers to common questions about the 84-Day Marathon Praise & Prayer Movement
            </p>
        </div>

        <div class="max-w-4xl mx-auto">
            <!-- Search and Filter -->
            <div class="mb-12 fade-in-up fade-in-delay-200">
                <!-- Search Bar -->
                <div class="relative mb-6">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <i data-lucide="search" class="w-5 h-5 text-muted-foreground"></i>
                    </div>
                    <input
                        type="text"
                        id="faqSearch"
                        placeholder="Search questions..."
                        class="w-full pl-10 pr-4 py-3 bg-background border-2 border-border rounded-lg focus:border-mmp-orange focus:outline-none text-foreground placeholder-muted-foreground transition-colors"
                    />
                </div>

                <!-- Category Filter -->
                <div class="flex flex-wrap gap-2 justify-center" id="faqCategories">
                    <button 
                        data-category="all"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 bg-mmp-orange text-white border-mmp-orange"
                    >
                        <i data-lucide="message-circle" class="w-4 h-4 mr-2 inline"></i>
                        All Questions
                    </button>
                    <button 
                        data-category="general"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 border-border hover:border-mmp-orange hover:text-mmp-orange text-white"
                    >
                        <i data-lucide="heart" class="w-4 h-4 mr-2 inline"></i>
                        General
                    </button>
                    <button 
                        data-category="participation"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 border-border hover:border-mmp-orange hover:text-mmp-orange text-white"
                    >
                        <i data-lucide="users" class="w-4 h-4 mr-2 inline"></i>
                        Participation
                    </button>
                    <button 
                        data-category="schedule"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 border-border hover:border-mmp-orange hover:text-mmp-orange text-white"
                    >
                        <i data-lucide="calendar" class="w-4 h-4 mr-2 inline"></i>
                        Schedule
                    </button>
                    <button 
                        data-category="locations"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 border-border hover:border-mmp-orange hover:text-mmp-orange text-white"
                    >
                        <i data-lucide="map-pin" class="w-4 h-4 mr-2 inline"></i>
                        Locations
                    </button>
                    <button 
                        data-category="diaspora"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 border-border hover:border-mmp-orange hover:text-mmp-orange text-white"
                    >
                        <i data-lucide="globe" class="w-4 h-4 mr-2 inline"></i>
                        Diaspora
                    </button>
                    <button 
                        data-category="support"
                        class="font-heading font-medium px-4 py-2 rounded-lg transition-all duration-300 border-border hover:border-mmp-orange hover:text-mmp-orange text-white"
                    >
                        <i data-lucide="help-circle" class="w-4 h-4 mr-2 inline"></i>
                        Support
                    </button>
                </div>
            </div>

            <!-- FAQ List -->
            <div class="space-y-4 fade-in-up fade-in-delay-300" id="faqList">
                <!-- FAQ items will be populated by JavaScript -->
            </div>

            <!-- Contact Support -->
            <div class="mt-16 text-center fade-in-up fade-in-delay-400">
                <div class="bg-gradient-to-r from-mmp-cream to-mmp-orange-cream bg-card border-[var(--mmp-orange)]/20 shadow-2xl border-2 border-mmp-orange/20 rounded-lg">
                    <div class="p-8 md:p-12">
                        <h3 class="font-heading font-bold text-2xl md:text-3xl text-foreground mb-4">
                            Still Have Questions?
                        </h3>
                        <p class="text-muted-foreground text-lg leading-relaxed mb-6 max-w-2xl mx-auto">
                            Our support team is here to help. Reach out to us for personalized assistance 
                            with joining the prayer movement or any other questions.
                        </p>
                        <div class="flex flex-col sm:flex-row gap-4 justify-center">
                            <a href="/contact" class="bg-mmp-orange hover:bg-mmp-orange-dark text-white font-heading font-semibold px-8 py-4 rounded-lg hover-lift">
                                <i data-lucide="message-circle" class="w-5 h-5 mr-2 inline"></i>
                                Contact Support
                            </a>

                            <a href="/join" class="border-2 border-mmp-orange text-mmp-orange hover:bg-mmp-orange-dark hover:text-white font-heading font-semibold px-8 py-4 rounded-lg hover-scale">
                                <i data-lucide="heart" class="w-5 h-5 mr-2 inline"></i>
                                Join Prayer Movement
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<script>
// FAQ Data
const faqs = [
    // General FAQs
    {
        id: '1',
        question: 'What is Marathon Praise & Prayer (MPP)?',
        answer: 'MPP is a Spirit-led, global movement of continuous praise and intercession, designed to awaken the Church and prepare nations for the return of Jesus. It mobilizes believers across denominations and countries to raise revival altars through unceasing worship, sacrificial service, and prophetic prayer.',
        category: 'general'
    },
    {
        id: '2', 
        question: 'When does MPP begin?',
        answer: 'For 2026, MPP kicks off on January 6th, and runs without interruption for 84 days until March 31st. Every day includes scheduled worship segments, themed prayer pillars, and national or diaspora-led intercessory sessions.',
        category: 'schedule'
    },
    {
        id: '3',
        question: 'Who can be part of MPP?',
        answer: 'Everyone! MPP is open to all believers — churches, ministries, individuals, youth groups, families — regardless of denomination, age, or location. Whether you\'re in Nigeria or part of the global diaspora, you\'re invited to host, pray, or serve.',
        category: 'participation'
    },
    {
        id: '4',
        question: 'How do I join the prayer sessions?',
        answer: 'You can sign up for prayer slots (daily or weekly), join the 24/7 Global Prayer Chain, attend in-person sessions in your region, or country, or participate in virtual sessions via livestream.',
        category: 'participation'
    },
    {
        id: '5',
        question: 'What are the volunteer opportunities available during MPP?',
        answer: 'You can join a Volunteer Praise Team (VPT) for worship cycles, serve in logistics, intercession, hospitality, or technical roles, and mobilize your local community.',
        category: 'volunteering'
    },
    {
        id: '6',
        question: 'What kind of prayers will be offered daily?',
        answer: 'Daily prayer points focus on spiritual renewal, family restoration, national revival, and global transformation. These are distributed through our newsletter, website, and social media channels. Again, participants should receive a Prayer Manual for consistency.',
        category: 'participation'
    },
    {
        id: '7',
        question: 'Will MPP happen in multiple places?',
        answer: 'Yes! MPP will have coordinated sessions across all 36 Nigerian states, as well as in diaspora altars hosted by RCCG branches and partner ministries in the USA, UK, Europe, Asia, and beyond. Check our locations page to find or host a gathering near you.',
        category: 'locations'
    },
    {
        id: '8',
        question: 'Can I join from outside Nigeria?',
        answer: 'Absolutely. We\'ve created a special Diaspora Framework to enable full participation. You can reach out to learn more about your nation.',
        category: 'diaspora'
    },
    {
        id: '9',
        question: 'How can I support the MPP movement?',
        answer: 'You can support by praying daily, volunteering in any capacity, donating towards logistics and operations, spreading the word online and in your church, partnering with us as a church, ministry, or mission agency.',
        category: 'support'
    },
    {
        id: '10',
        question: 'Still have questions?',
        answer: 'Reach out to our support team. We\'re here to guide you every step of the way.',
        category: 'support'
    }
];

// Initialize FAQ functionality
document.addEventListener('DOMContentLoaded', function() {
    const faqList = document.getElementById('faqList');
    const faqSearch = document.getElementById('faqSearch');
    const categoryButtons = document.querySelectorAll('#faqCategories button');
    
    let selectedCategory = 'all';
    let searchTerm = '';
    
    // Render FAQs based on current filters
    function renderFAQs() {
        const filteredFAQs = faqs.filter(faq => {
            const matchesCategory = selectedCategory === 'all' || faq.category === selectedCategory;
            const matchesSearch = searchTerm === '' || 
                faq.question.toLowerCase().includes(searchTerm.toLowerCase()) ||
                faq.answer.toLowerCase().includes(searchTerm.toLowerCase());
            
            return matchesCategory && matchesSearch;
        });
        
        faqList.innerHTML = '';
        
        if (filteredFAQs.length === 0) {
            faqList.innerHTML = `
                <div class="border-2 border-border rounded-lg">
                    <div class="p-8 text-center">
                        <i data-lucide="search" class="w-12 h-12 text-muted-foreground mx-auto mb-4"></i>
                        <h3 class="font-heading font-semibold text-xl text-foreground mb-2">
                            No questions found
                        </h3>
                        <p class="text-muted-foreground">
                            Try adjusting your search or filter criteria
                        </p>
                    </div>
                </div>
            `;
        } else {
            filteredFAQs.forEach(faq => {
                const faqItem = document.createElement('div');
                faqItem.className = 'border-2 border-border rounded-lg transition-all duration-300 cursor-pointer hover:shadow-md hover:border-mmp-orange/50';
                faqItem.innerHTML = `
                    <div class="flex items-center justify-between p-6 cursor-pointer" onclick="toggleFAQ('${faq.id}')">
                        <h3 class="font-heading font-semibold text-lg text-foreground pr-4 flex-grow">
                            ${faq.question}
                        </h3>
                        <div id="chevron-${faq.id}" class="flex-shrink-0 transition-transform duration-300">
                            <i data-lucide="chevron-down" class="w-5 h-5 text-muted-foreground"></i>
                        </div>
                    </div>
                    <div id="answer-${faq.id}" class="hidden px-6 pb-6 border-t border-border">
                        <div class="pt-4">
                            <p class="text-muted-foreground leading-relaxed text-base">
                                ${faq.answer}
                            </p>
                        </div>
                    </div>
                `;
                faqList.appendChild(faqItem);
            });
        }
        
        // Initialize Lucide icons
        if (window.lucide) {
            window.lucide.createIcons();
        }
    }
    
    // Toggle FAQ answer visibility
    window.toggleFAQ = function(faqId) {
        const answer = document.getElementById(`answer-${faqId}`);
        const chevron = document.getElementById(`chevron-${faqId}`);
        
        if (answer.classList.contains('hidden')) {
            answer.classList.remove('hidden');
            chevron.classList.add('rotate-180');
            chevron.querySelector('i').classList.add('text-mmp-orange');
            chevron.querySelector('i').classList.remove('text-muted-foreground');
        } else {
            answer.classList.add('hidden');
            chevron.classList.remove('rotate-180');
            chevron.querySelector('i').classList.remove('text-mmp-orange');
            chevron.querySelector('i').classList.add('text-muted-foreground');
        }
    };
    
    // Handle category filter
    categoryButtons.forEach(button => {
        button.addEventListener('click', function() {
            selectedCategory = this.dataset.category;
            
            // Update button styles
            categoryButtons.forEach(btn => {
                if (btn.dataset.category === selectedCategory) {
                    btn.classList.add('bg-mmp-orange', 'text-white', 'border-mmp-orange');
                    btn.classList.remove('border-border', 'hover:border-mmp-orange', 'hover:text-mmp-orange');
                } else {
                    btn.classList.remove('bg-mmp-orange', 'text-white', 'border-mmp-orange');
                    btn.classList.add('border-border', 'hover:border-mmp-orange', 'hover:text-mmp-orange');
                }
            });
            
            renderFAQs();
        });
    });
    
    // Handle search input
    faqSearch.addEventListener('input', function() {
        searchTerm = this.value;
        renderFAQs();
    });
    
    // Initial render
    renderFAQs();
});
</script>
