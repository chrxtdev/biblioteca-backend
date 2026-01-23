<x-filament-widgets::widget>
    <div class="fi-wi-stats-overview-stat relative rounded-xl p-6 shadow-lg" 
         style="background: linear-gradient(to right, #22c55e, #0d9488); color: white;">
        <div class="relative z-10 flex items-center justify-between">
            <div>
                <p class="text-lg" style="opacity: 0.9; color: white;">{{ $this->getGreeting() }},</p>
                <h2 class="text-3xl font-bold tracking-tight" style="color: white;">{{ $this->getUserName() }} 👋</h2>
                <p class="mt-2 text-sm" style="opacity: 0.8; color: white;">{{ $this->getCurrentDate() }}</p>
            </div>
            
            <div class="hidden md:block">
                <div class="flex items-center gap-3 rounded-xl px-4 py-3" style="background: rgba(255,255,255,0.2);">
                    <svg class="h-8 w-8" fill="none" stroke="currentColor" viewBox="0 0 24 24" style="color: white;">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                    <div>
                        <p class="text-xs" style="opacity: 0.8; color: white;">Painel</p>
                        <p class="font-semibold" style="color: white;">Administrador</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-filament-widgets::widget>
