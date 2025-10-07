<div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0">
    <div class="mb-8">
        {{ $logo }}
    </div>

    <div class="w-full sm:max-w-md mt-6 px-8 py-10 bg-white/80 backdrop-blur-sm shadow-xl border border-white/20 overflow-hidden sm:rounded-2xl">
        {{ $slot }}
    </div>
    
    <!-- Decorative elements -->
    <div class="absolute top-0 left-0 w-full h-full overflow-hidden pointer-events-none">
        <div class="absolute -top-40 -right-40 w-80 h-80 bg-blue-400/20 rounded-full blur-3xl"></div>
        <div class="absolute -bottom-40 -left-40 w-80 h-80 bg-indigo-400/20 rounded-full blur-3xl"></div>
    </div>
</div>
