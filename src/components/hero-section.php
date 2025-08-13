<section class="relative min-h-screen flex items-center justify-center overflow-hidden bg-[var(--mpp-black)] page-transition">
    <!-- Background Video/Image with Overlay -->
    <div class="absolute inset-0 z-0">
        <img
            src="https://images.unsplash.com/photo-1516450360452-9312f5e86fc7?w=1920&h=1080&fit=crop&crop=center"
            alt="Nigerian worship gathering with raised hands"
            class="w-full h-full object-cover fade-in" />
        <div class="absolute inset-0 bg-gradient-to-b from-black/80 via-black/70 to-black/80"></div>
    </div>

    <!-- Floating Particles -->
    <div class="absolute inset-0 pointer-events-none">
        <div class="floating-particle absolute top-1/4 left-1/4 w-3 h-3 bg-[var(--mpp-orange)] rounded-full opacity-70"></div>
        <div class="floating-particle absolute top-1/3 right-1/3 w-2 h-2 bg-[var(--mpp-orange-light)] rounded-full opacity-60" style="animation-delay: 1s;"></div>
        <div class="floating-particle absolute bottom-1/3 left-1/5 w-2.5 h-2.5 bg-[var(--mpp-orange)] rounded-full opacity-80" style="animation-delay: 2s;"></div>
        <div class="floating-particle absolute top-2/3 right-1/4 w-1.5 h-1.5 bg-[var(--mpp-orange-light)] rounded-full opacity-90" style="animation-delay: 3s;"></div>
    </div>

    <!-- Hero Content -->
    <div class="relative z-10 text-center max-w-5xl mx-auto px-6">
        <!-- Main Heading -->
        <div class="mb-8">
            <h1 class="font-serif text-5xl md:text-7xl lg:text-8xl font-bold text-white mb-6 leading-tight fade-in-up">
                84 Days, 2016 Hours
                <span class="block text-gradient-orange fade-in-up fade-in-delay-200">
                    Non-stop Worship
                </span>
                <span class="block text-[var(--mpp-orange)] fade-in-up fade-in-delay-400">
                    And Intercession
                </span>
            </h1>
        </div>

        <!-- Countdown Timer -->
        <div class="mb-12 fade-in-up fade-in-delay-600">
            <p class="text-lg text-gray-300 mb-4">MPP 2026 begins in:</p>
            <div class="flex items-center justify-center gap-3 mb-8 flex-wrap">
                <div class="bg-[var(--mmp-charcoal)]/90 backdrop-blur-sm rounded-xl p-4 border border-[var(--mmp-orange)]/40 pulse-glow hover-lift">
                    <div class="text-2xl md:text-3xl font-bold text-white" id="countdown-days">--</div>
                    <div class="text-xs text-[var(--mmp-orange)]">Days</div>
                </div>
                <div class="text-[var(--mmp-orange)] text-xl">:</div>
                <div class="bg-[var(--mmp-charcoal)]/90 backdrop-blur-sm rounded-xl p-4 border border-[var(--mmp-orange-light)]/40 hover-lift">
                    <div class="text-2xl md:text-3xl font-bold text-white" id="countdown-hours">--</div>
                    <div class="text-xs text-[var(--mmp-orange)]">Hours</div>
                </div>
                <div class="text-[var(--mmp-orange-light)] text-xl">:</div>
                <div class="bg-[var(--mmp-charcoal)]/90 backdrop-blur-sm rounded-xl p-4 border border-[var(--mmp-orange)]/40 hover-lift">
                    <div class="text-2xl md:text-3xl font-bold text-white" id="countdown-minutes">--</div>
                    <div class="text-xs text-[var(--mmp-orange)]">Minutes</div>
                </div>
                <div class="text-[var(--mmp-orange)] text-xl">:</div>
                <div class="bg-[var(--mmp-charcoal)]/90 backdrop-blur-sm rounded-xl p-4 border border-[var(--mmp-orange-light)]/40 hover-lift">
                    <div class="text-2xl md:text-3xl font-bold text-white" id="countdown-seconds">--</div>
                    <div class="text-xs text-[var(--mmp-orange)]">Seconds</div>
                </div>
            </div>
        </div>

        <!-- CTA Buttons -->
        <div class="flex flex-col sm:flex-row items-center justify-center gap-6 mb-12 fade-in-up fade-in-delay-700">
            <button class="border-2 mpp-orange-gradient text-white px-12 py-6 rounded-full hover:scale-105 transition-all duration-300 mpp-glow-orange font-semibold text-lg h-auto hover-glow">
                <i data-lucide="users" class="w-6 h-6 mr-3 inline"></i>
                Join the Movement
            </button>
            <button class="border-2 border-[var(--mpp-brown)] bg-[var(--mmp-orange)] hover:bg-[var(--mmp-orange-dark)] text-white px-12 py-6 rounded-full transition-all duration-300 backdrop-blur-sm text-lg h-auto hover-lift font-semibold">
                <i data-lucide="play" class="w-6 h-6 mr-3 inline"></i>
                Watch Vision Video
            </button>
        </div>

        <!-- Scroll Indicator -->
        <div class="animate-bounce fade-in fade-in-delay-800">
            <div class="w-6 h-10 border-2 border-[var(--mmp-orange)] rounded-full mx-auto flex justify-center">
                <div class="w-1 h-3 bg-[var(--mmp-orange)] rounded-full mt-2 animate-pulse"></div>
            </div>
        </div>
    </div>
</section>

<script>
    // Countdown Timer to January 6, 2026 at 12:00 PM (noon)
    function updateCountdown() {
        // Target date: January 6, 2026 at 12:00 PM UTC
        const targetDate = new Date('2026-01-06T12:00:00Z').getTime();
        const now = new Date().getTime();
        const timeLeft = targetDate - now;

        if (timeLeft > 0) {
            const days = Math.floor(timeLeft / (1000 * 60 * 60 * 24));
            const hours = Math.floor((timeLeft % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
            const minutes = Math.floor((timeLeft % (1000 * 60 * 60)) / (1000 * 60));
            const seconds = Math.floor((timeLeft % (1000 * 60)) / 1000);

            // Update the countdown display
            document.getElementById('countdown-days').textContent = days.toString().padStart(2, '0');
            document.getElementById('countdown-hours').textContent = hours.toString().padStart(2, '0');
            document.getElementById('countdown-minutes').textContent = minutes.toString().padStart(2, '0');
            document.getElementById('countdown-seconds').textContent = seconds.toString().padStart(2, '0');
        } else {
            // Event has started
            document.getElementById('countdown-days').textContent = '00';
            document.getElementById('countdown-hours').textContent = '00';
            document.getElementById('countdown-minutes').textContent = '00';
            document.getElementById('countdown-seconds').textContent = '00';

            // Optionally change the text to indicate the event has started
            document.querySelector('.text-gray-300').textContent = 'MPP 2026 is now live!';
        }
    }

    // Update countdown every second
    updateCountdown(); // Initial call
    setInterval(updateCountdown, 1000);
</script>