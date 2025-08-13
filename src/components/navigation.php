<nav class="fixed w-full z-50 bg-transparent backdrop-blur-sm transition-all duration-300" id="main-nav">
    <div class="container mx-auto px-6">
        <div class="flex items-center justify-between h-20">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="#" class="flex items-center space-x-3 hover:opacity-80 transition-opacity">
                    <div class="w-12 h-12 rounded-lg overflow-hidden flex items-center justify-center">
                        <img src="assets/images/mpp_logo.png" alt="84 Days Marathon Praise & Prayer" class="w-10 h-10 object-contain">
                    </div>
                    <div class="hidden sm:block">
                        <h1 class="text-lg font-heading font-bold text-white"></h1>
                        <p class="text-xs text-white/80"></p>
                    </div>
                </a>
            </div>

            <!-- Desktop Navigation -->
            <div class="hidden md:flex items-center space-x-6" id="desktop-nav">
                <a href="#" class="nav-link text-white hover:text-[var(--mmp-orange)] transition-colors font-medium">Home</a>
                <a href="#about" class="nav-link text-white hover:text-[var(--mmp-orange)] transition-colors font-medium">About</a>
                <a href="#participate" class="nav-link text-white hover:text-[var(--mmp-orange)] transition-colors font-medium">Participate</a>
                <a href="#volunteer" class="nav-link text-white hover:text-[var(--mmp-orange)] transition-colors font-medium">Volunteer</a>
                <a href="#faq" class="nav-link text-white hover:text-[var(--mmp-orange)] transition-colors font-medium">FAQ</a>
            </div>

            <!-- CTA Buttons -->
            <div class="hidden lg:flex items-center" id="desktop-cta">
                <a href="#pray" class="bg-[var(--mmp-orange)] hover:bg-[var(--mmp-orange-dark)] text-white px-4 py-2 lg:px-6 lg:py-3 rounded-full transition-all duration-300 hover-lift font-heading font-semibold text-sm lg:text-base">
                    Join Prayer
                </a>
                
                <!-- Dark Mode Toggle - Commented out for now -->
                <!-- <button id="dark-mode-toggle" class="w-10 h-10 lg:w-12 lg:h-12 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                    <i data-lucide="moon" class="w-4 h-4 lg:w-5 lg:h-5 text-white dark-mode-icon" id="dark-icon"></i>
                    <i data-lucide="sun" class="w-4 h-4 lg:w-5 lg:h-5 text-white light-mode-icon hidden" id="light-icon"></i>
                </button> -->
            </div>

            <!-- Mobile Menu Toggle -->
            <button class="lg:hidden w-10 h-10 flex items-center justify-center" id="mobile-menu-toggle">
                <i data-lucide="menu" class="w-6 h-6 text-white"></i>
                <i data-lucide="x" class="w-6 h-6 text-white hidden"></i>
            </button>
        </div>

        <!-- Mobile Navigation -->
        <div class="lg:hidden mobile-nav hidden bg-black/80 backdrop-blur-sm border-t border-white/20" id="mobile-nav">
            <div class="px-6 py-4 space-y-4">
                <a href="#" class="block text-white hover:text-[var(--mmp-orange)] transition-colors font-medium py-2">Home</a>
                <a href="#about" class="block text-white hover:text-[var(--mmp-orange)] transition-colors font-medium py-2">About</a>
                <a href="#participate" class="block text-white hover:text-[var(--mmp-orange)] transition-colors font-medium py-2">Participate</a>
                <a href="#volunteer" class="block text-white hover:text-[var(--mmp-orange)] transition-colors font-medium py-2">Volunteer</a>
                <a href="#faq" class="block text-white hover:text-[var(--mmp-orange)] transition-colors font-medium py-2">FAQ</a>
                
                <div class="border-t border-white/20 pt-4 mt-4">
                    <!-- Mobile Dark Mode Toggle - Commented out for now -->
                    <!-- <div class="flex items-center justify-between py-2 mb-4">
                        <span class="text-white font-medium">Dark Mode</span>
                        <button id="mobile-dark-mode-toggle" class="w-10 h-10 bg-white/20 hover:bg-white/30 rounded-full flex items-center justify-center transition-colors">
                            <i data-lucide="moon" class="w-4 h-4 text-white mobile-dark-mode-icon"></i>
                            <i data-lucide="sun" class="w-4 h-4 text-white mobile-light-mode-icon hidden"></i>
                        </button>
                    </div> -->
                    
                    <a href="#pray" class="w-full bg-[var(--mmp-orange)] hover:bg-[var(--mmp-orange-dark)] text-white px-6 py-3 rounded-full transition-all duration-300 font-heading font-semibold">
                        Join Prayer
                    </a>
                </div>
            </div>
        </div>
    </div>
</nav>
