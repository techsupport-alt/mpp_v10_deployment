<section class="py-20 bg-secondary dark:bg-[var(--mmp-dark-gray)] relative overflow-hidden">
    <!-- Animated Background Elements -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="floating-particle absolute top-10 left-20 w-3 h-3 bg-[var(--mmp-orange)] rounded-full opacity-50"></div>
        <div class="floating-particle absolute top-8 right-32 w-2 h-2 bg-[var(--mmp-brown-light)] rounded-full opacity-60" style="animation-delay: 1s"></div>
        <div class="floating-particle absolute bottom-8 left-32 w-4 h-4 bg-[var(--mmp-orange-light)] rounded-full opacity-45" style="animation-delay: 2s"></div>
    </div>

    <!-- Full Width Container -->
    <div class="w-full max-w-screen-2xl mx-auto px-6 relative">
        <!-- Newsletter Card -->
        <div class="relative overflow-hidden rounded-3xl w-full max-w-6xl mx-auto fade-in-up bg-card border border-[var(--mmp-orange)]/20 shadow-2xl">
            
            <!-- Content -->
            <div class="relative z-10 px-10 py-16 md:px-20 lg:px-24">
                <div class="flex flex-col lg:flex-row items-center gap-12 lg:gap-16">
                    
                    <!-- Left Side - Text Content -->
                    <div class="flex-1 text-center lg:text-left fade-in-left">
                        <!-- Icon -->
                        <div class="w-20 h-20 bg-[var(--mmp-orange)]/20 rounded-full flex items-center justify-center mx-auto lg:mx-0 mb-8 breathe border border-[var(--mmp-orange)]/40">
                            <i data-lucide="mail" class="w-10 h-10 text-[var(--mmp-orange)]"></i>
                        </div>

                        <!-- Heading -->
                        <h2 class="font-heading text-4xl md:text-5xl font-bold text-card-foreground mb-6 leading-tight">
                            Stay Connected to the 
                            <span class="block text-[var(--mmp-orange)]">Movement</span>
                        </h2>
                        
                        <!-- Subheading -->
                        <p class="text-xl text-muted-foreground mb-8 max-w-xl mx-auto lg:mx-0 leading-relaxed">
                            Get daily prayer points, breakthrough reports, and event updates delivered to your inbox
                        </p>

                        <!-- Social Proof -->
                        <div class="flex items-center justify-center lg:justify-start gap-8 text-sm text-muted-foreground">
                            <div class="flex items-center gap-2">
                                <i data-lucide="users" class="w-5 h-5 text-[var(--mmp-orange)]"></i>
                                <span class="font-medium">250K+ Subscribers</span>
                            </div>
                            <div class="flex items-center gap-2">
                                <i data-lucide="zap" class="w-5 h-5 text-[var(--mmp-brown)]"></i>
                                <span class="font-medium">Daily Updates</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Side - Signup Form -->
                    <div class="flex-1 w-full max-w-lg fade-in-right">
                        <div id="newsletter-form-container">
                            <form id="newsletter-form" class="space-y-6">
                                <div class="flex flex-col sm:flex-row gap-4">
                                    <div class="flex-1 relative">
                                        <input
                                            type="email"
                                            id="newsletter-email"
                                            name="email"
                                            placeholder="Enter your email address"
                                            required
                                            class="w-full bg-input-background border-2 border-border text-foreground placeholder:text-muted-foreground focus:border-[var(--mmp-orange)] focus:ring-[var(--mmp-orange)] rounded-2xl px-8 py-5 h-16 text-lg shadow-lg font-medium"
                                        />
                                        <div class="absolute right-6 top-1/2 -translate-y-1/2">
                                            <i data-lucide="mail" class="w-6 h-6 text-muted-foreground"></i>
                                        </div>
                                    </div>
                                    <button 
                                        type="submit"
                                        class="bg-card text-card-foreground border-2 border-[var(--mmp-orange)] hover:bg-[var(--mmp-orange)] hover:text-[var(--mmp-white)] px-10 py-5 rounded-2xl h-16 font-semibold text-lg transition-all duration-300 hover-lift shadow-2xl whitespace-nowrap font-heading"
                                    >
                                        <i data-lucide="send" class="w-6 h-6 mr-3 inline"></i>
                                        Subscribe
                                    </button>
                                </div>
                                
                                <p class="text-sm text-muted-foreground text-center sm:text-left leading-relaxed">
                                    Join the movement. Unsubscribe anytime. We respect your privacy.
                                </p>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>