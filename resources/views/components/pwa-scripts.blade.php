<div id="pwa-install-prompt" class="hidden fixed bottom-24 sm:bottom-10 left-6 right-6 lg:left-auto lg:right-10 lg:max-w-sm z-[9999] transform translate-y-24 opacity-50 transition-all duration-700 ease-out">
    <div class="bg-white dark:bg-zinc-900 rounded-[2.5rem] p-6 shadow-[0_20px_50px_rgba(0,0,0,0.1)] dark:shadow-[0_20px_50px_rgba(0,0,0,0.3)] border border-zinc-100 dark:border-zinc-800 flex flex-col gap-5 pointer-events-auto overflow-hidden relative group">
        <!-- Decorative Glow -->
        <div class="absolute top-0 right-0 p-12 bg-[var(--color-brand-purple)]/10 rounded-full -mr-16 -mt-16 blur-3xl transition-all group-hover:bg-[var(--color-brand-purple)]/20"></div>
        
        <div class="flex items-center gap-5 relative z-10">
            <div class="size-14 bg-gradient-to-tr from-[var(--color-brand-purple)] to-purple-500 rounded-2xl flex items-center justify-center shrink-0 shadow-lg shadow-purple-500/20">
                <img src="{{ asset('favicon.ico') }}" alt="Allsers" class="size-9">
            </div>
            <div class="flex-1">
                <h3 class="font-black text-zinc-900 dark:text-zinc-100 text-lg tracking-tight">{{ __('Install Allsers') }}</h3>
                <p class="text-xs text-zinc-500 dark:text-zinc-400 mt-0.5 leading-relaxed font-medium">
                    {{ __('Access Allsers from your home screen just like a native app.') }}
                </p>
            </div>
        </div>

        <div class="flex gap-3 relative z-10 mt-1">
            <button id="pwa-install-btn" class="flex-1 bg-[var(--color-brand-purple)] hover:opacity-90 text-white text-sm font-black py-3.5 rounded-2xl transition-all shadow-lg shadow-purple-500/20 active:scale-95">
                {{ __('Install Now') }}
            </button>
            <button id="pwa-close-btn" class="px-6 bg-zinc-50 dark:bg-zinc-800/50 hover:bg-zinc-100 dark:hover:bg-zinc-800 text-zinc-500 dark:text-zinc-400 text-sm font-bold py-3.5 rounded-2xl transition-all active:scale-95">
                {{ __('Later') }}
            </button>
        </div>
    </div>
</div>

<script>
    // Service Worker Registration
    if ('serviceWorker' in navigator) {
        window.addEventListener('load', () => {
            navigator.serviceWorker.register('/sw.js')
                .then(reg => console.log('Service Worker registered', reg))
                .catch(err => console.log('Service Worker registration failed', err));
        });
    }

    // PWA Install Prompt Logic
    let deferredPrompt;
    const promptElement = document.getElementById('pwa-install-prompt');
    const installBtn = document.getElementById('pwa-install-btn');
    const closeBtn = document.getElementById('pwa-close-btn');

    window.addEventListener('beforeinstallprompt', (e) => {
        // Prevent Chrome 67 and earlier from automatically showing the prompt
        e.preventDefault();
        // Stash the event so it can be triggered later.
        deferredPrompt = e;

        // Check if user has already dismissed the prompt in this session
        if (sessionStorage.getItem('pwa_prompt_dismissed')) return;

        // Show the custom prompt after a short delay
        setTimeout(() => {
            promptElement.classList.remove('translate-y-32', 'opacity-0', 'pointer-events-none');
            promptElement.classList.add('translate-y-0', 'opacity-1');
        }, 3000);
    });

    installBtn.addEventListener('click', async () => {
        if (!deferredPrompt) return;
        
        // Show the install prompt
        deferredPrompt.prompt();
        
        // Wait for the user to respond to the prompt
        const { outcome } = await deferredPrompt.userChoice;
        console.log(`User response to the install prompt: ${outcome}`);
        
        // We've used the prompt, and can't use it again
        deferredPrompt = null;
        
        // Hide our custom UI
        hidePrompt();
    });

    closeBtn.addEventListener('click', () => {
        hidePrompt();
        // Store dismissal in session so it doesn't show again until reload/new session
        sessionStorage.setItem('pwa_prompt_dismissed', 'true');
    });

    function hidePrompt() {
        promptElement.classList.add('translate-y-100', 'opacity-0', 'pointer-events-none');
        promptElement.classList.remove('translate-y-0', 'opacity-1');
    }

    // Handle iOS users separately (they don't support beforeinstallprompt)
    const isIos = () => {
        const userAgent = window.navigator.userAgent.toLowerCase();
        return /iphone|ipad|ipod/.test(userAgent);
    }
    const isInStandaloneMode = () => ('standalone' in window.navigator) && (window.navigator.standalone);

    if (isIos() && !isInStandaloneMode()) {
        if (!sessionStorage.getItem('pwa_prompt_dismissed')) {
            // Customize the prompt for iOS
            const promptTitle = promptElement.querySelector('h3');
            const promptDesc = promptElement.querySelector('p');
            const installButtonText = promptElement.querySelector('#pwa-install-btn');

            promptTitle.textContent = "Add to Home Screen";
            promptDesc.textContent = "Tap the share icon in your browser and select 'Add to Home Screen' to install Allsers.";
            installButtonText.style.display = 'none'; // iOS users have to do it manually

            setTimeout(() => {
                promptElement.classList.remove('translate-y-32', 'opacity-50', 'pointer-events-none');
                promptElement.classList.add('translate-y-0', 'opacity-1');
            }, 5000);
        }
    }
</script>
