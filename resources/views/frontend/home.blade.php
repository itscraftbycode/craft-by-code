@extends('layouts.app')

@php
    $heroH1 = \App\Models\WebsiteSetting::get('hero_headline_1', 'WE DESIGN.');
    $heroH2 = \App\Models\WebsiteSetting::get('hero_headline_2', 'WE CODE.');
    $heroHGrad = \App\Models\WebsiteSetting::get('hero_headline_gradient', 'WE INNOVATE.');
    $heroSub = \App\Models\WebsiteSetting::get('hero_subtitle', 'We build custom digital products, AI automation tools, and MVPs that help startups and businesses grow faster.');
    $heroBadge = \App\Models\WebsiteSetting::get('brand_badge', 'TRUSTED BY STARTUPS & BUSINESSES WORLDWIDE');
    $statClients = \App\Models\WebsiteSetting::get('stat_clients_count', '50+');
    $statProjects = \App\Models\WebsiteSetting::get('stat_projects_count', '120+');
    $statYears = \App\Models\WebsiteSetting::get('stat_years_count', '5+');
    $statSat = \App\Models\WebsiteSetting::get('stat_satisfaction_count', '98%');
@endphp

@section('canonical', route('home'))

@section('content')
    <!-- 1. HERO SECTION -->
    <section class="relative overflow-hidden pt-8 pb-10 sm:pt-10 sm:pb-12 lg:pt-12 lg:pb-14 gradient-hero">
        <!-- Floating 3D Elements Decorative (Brain & Terminal) -->
        <div class="hidden lg:block absolute left-12 top-28 animate-bounce transition duration-1000" style="animation-duration: 4s;">
            <div class="w-16 h-16 rounded-2xl bg-white dark:bg-slate-900 shadow-xl dark:shadow-blue-950/50 border border-slate-100 dark:border-slate-800 flex items-center justify-center p-3">
                <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 flex items-center justify-center text-purple-600 dark:text-purple-400">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M12 2a4 4 0 0 0-4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4V6a4 4 0 0 0-4-4Z"/>
                        <path d="M6 10a4 4 0 0 0-4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4v-1a4 4 0 0 0-4-4Z"/>
                        <path d="M18 10a4 4 0 0 0-4 4v1a4 4 0 0 0 4 4 4 4 0 0 0 4-4v-1a4 4 0 0 0-4-4Z"/>
                    </svg>
                </div>
            </div>
        </div>

        <div class="hidden lg:block absolute right-16 top-24 animate-pulse transition duration-1000" style="animation-duration: 5s;">
            <div class="w-20 h-20 rounded-2xl bg-white dark:bg-slate-900 shadow-2xl dark:shadow-blue-950/50 border border-slate-100 dark:border-slate-800 flex flex-col justify-between p-3.5">
                <div class="flex items-center gap-1.5">
                    <span class="w-2 h-2 rounded-full bg-rose-400"></span>
                    <span class="w-2 h-2 rounded-full bg-amber-400"></span>
                    <span class="w-2 h-2 rounded-full bg-emerald-400"></span>
                </div>
                <div class="font-mono text-[11px] font-bold text-purple-600 dark:text-purple-300 bg-purple-50 dark:bg-purple-950/70 rounded px-1.5 py-0.5">
                    &gt;_ code
                </div>
            </div>
        </div>

        <div class="max-w-5xl mx-auto px-4 sm:px-6 lg:px-8 text-center relative z-10">
            <!-- Pill Badge -->
            <div class="inline-flex items-center gap-2 px-4 sm:px-5 py-1.5 rounded-full bg-slate-100 dark:bg-slate-800/95 backdrop-blur-sm border border-slate-200 dark:border-slate-700 text-slate-700 dark:text-slate-200 text-[10px] sm:text-[11px] font-semibold uppercase tracking-[0.14em] mb-6 sm:mb-7 shadow-[0_2px_10px_rgba(15,23,42,0.03)]">
                <span>{{ $heroBadge }}</span>
            </div>

            <!-- Main Headline -->
            <h1 class="text-4xl sm:text-6xl md:text-7xl font-black tracking-tight text-slate-900 dark:text-white leading-[1.08] mb-5 sm:mb-6">
                {{ $heroH1 }}<br>
                {{ $heroH2 }}<br>
                <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">{{ $heroHGrad }}</span>
            </h1>

            <!-- Subtitle -->
            <p class="max-w-2xl mx-auto text-base sm:text-lg text-slate-600 dark:text-slate-300 font-normal leading-relaxed mb-8 sm:mb-10">
                {{ $heroSub }}
            </p>

            <!-- Buttons -->
            <div class="flex flex-col sm:flex-row items-center justify-center gap-4">
                <a href="{{ route('services.index') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-slate-950 dark:bg-blue-600 text-white font-semibold text-sm hover:bg-blue-600 dark:hover:bg-blue-500 transition shadow-sm hover:shadow-md group">
                    <span>Explore Services</span>
                    <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M5 12h14M12 5l7 7-7 7"/>
                    </svg>
                </a>
                <a href="{{ route('contact') }}" 
                   class="w-full sm:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-white dark:bg-slate-900 text-slate-800 dark:text-slate-100 font-semibold text-sm border border-slate-200 dark:border-slate-700 hover:border-slate-300 dark:hover:border-slate-600 hover:bg-slate-50 dark:hover:bg-slate-800 transition shadow-sm group">
                    <span>Let's Talk</span>
                    <svg class="w-4 h-4 ml-2 text-slate-500 dark:text-slate-400 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                        <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                    </svg>
                </a>
            </div>
        </div>

        <!-- 4 Feature Highlights Row -->
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8 mt-14 lg:mt-20">
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4">
                <!-- Clean Code -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4 hover:shadow-md dark:hover:border-slate-700 transition">
                    <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><polyline points="16 18 22 12 16 6"/><polyline points="8 6 2 12 8 18"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Clean Code</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Scalable, maintainable and well-structured code.</p>
                    </div>
                </div>

                <!-- Creative Design -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4 hover:shadow-md dark:hover:border-slate-700 transition">
                    <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m18 2 4 4-12 12H6v-4L18 2z"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Creative Design</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Pixel-perfect designs that inspire and engage.</p>
                    </div>
                </div>

                <!-- Smart Solutions -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4 hover:shadow-md dark:hover:border-slate-700 transition">
                    <div class="w-12 h-12 rounded-xl bg-pink-50 dark:bg-pink-950/60 text-pink-600 dark:text-pink-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M12 2v2"/><path d="M12 20v2"/><path d="m4.93 4.93 1.41 1.41"/><path d="m17.66 17.66 1.41 1.41"/><path d="M2 12h2"/><path d="M20 12h2"/><path d="m6.34 17.66-1.41 1.41"/><path d="m19.07 4.93-1.41 1.41"/><circle cx="12" cy="12" r="5"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Smart Solutions</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Solving complex problems with smart thinking.</p>
                    </div>
                </div>

                <!-- Future Ready -->
                <div class="p-5 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-center gap-4 hover:shadow-md dark:hover:border-slate-700 transition">
                    <div class="w-12 h-12 rounded-xl bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 flex items-center justify-center flex-shrink-0">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m12 15-3-3m0 0 3-3m-3 3h12"/><path d="M4.5 16.5c-1.5 1.26-2 3.5-2 3.5s2.24-.5 3.5-2"/></svg>
                    </div>
                    <div class="min-w-0">
                        <h4 class="text-sm font-bold text-slate-900 dark:text-white">Future Ready</h4>
                        <p class="text-xs text-slate-500 dark:text-slate-400 mt-0.5">Innovative solutions for a digital-first world.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 2. TRUSTED BY GLOBAL BRANDS (Temporarily hidden) -->
    {{--
    <section class="py-12 border-y border-slate-100 dark:border-slate-800/80 bg-slate-50/50 dark:bg-slate-900/40">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <p class="text-center text-xs font-semibold uppercase tracking-wider text-slate-400 dark:text-slate-500 mb-8">
                Trusted by global brands &amp; fast-growing startups
            </p>
            <div class="flex flex-wrap items-center justify-center gap-6 sm:gap-12 lg:gap-20 opacity-70 hover:opacity-100 transition-opacity">
                <div class="flex items-center gap-2 font-bold text-lg sm:text-xl text-slate-700 dark:text-slate-300">
                    <span class="tracking-tight">Google</span>
                </div>
                <div class="flex items-center gap-2 font-semibold text-base sm:text-lg text-slate-700 dark:text-slate-300">
                    <div class="grid grid-cols-2 gap-0.5 w-4 h-4">
                        <div class="bg-red-500"></div><div class="bg-green-500"></div>
                        <div class="bg-blue-500"></div><div class="bg-yellow-500"></div>
                    </div>
                    <span>Microsoft</span>
                </div>
                <div class="font-bold text-lg sm:text-xl text-slate-700 dark:text-slate-300">aws</div>
                <div class="font-bold text-base sm:text-lg text-slate-700 dark:text-slate-300">#slack</div>
                <div class="font-bold text-base sm:text-lg text-slate-700 dark:text-slate-300">shopify</div>
                <div class="font-bold text-lg sm:text-xl text-slate-700 dark:text-slate-300">stripe</div>
            </div>
        </div>
    </section>
    --}}

    <!-- 3. OUR SERVICES SECTION -->
    <section class="pt-4 pb-16 sm:pt-6 sm:pb-20 lg:pt-8 lg:pb-24 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-12 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-2">Our Services</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    End-to-end solutions for your digital success
                </h2>
            </div>
            <a href="{{ route('services.index') }}" 
               class="inline-flex items-center text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 group">
                <span>View All Services</span>
                <svg class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6 sm:gap-8">
            @foreach($featuredServices as $service)
                <a href="{{ route('services.show', $service->slug) }}" 
                   class="group relative p-7 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 hover:border-blue-200 dark:hover:border-blue-700 shadow-sm hover:shadow-xl dark:hover:shadow-blue-950/40 transition-all duration-300 flex flex-col justify-between hover:-translate-y-1 block cursor-pointer">
                    <div>
                        <div class="flex items-center justify-between mb-6">
                            <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center group-hover:bg-blue-600 group-hover:text-white transition-colors duration-300">
                                <x-icon :name="$service->icon" class="w-6 h-6" />
                            </div>
                            @if($service->badge)
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800/80">
                                    {{ $service->badge }}
                                </span>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                            {{ $service->title }}
                        </h3>
                        <p class="text-sm text-slate-500 dark:text-slate-400 line-clamp-3 leading-relaxed mb-6">
                            {{ $service->short_description }}
                        </p>
                    </div>

                    <div class="pt-4 border-t border-slate-100/80 dark:border-slate-800 flex items-center justify-between">
                        <span class="text-xs font-semibold text-slate-400 dark:text-slate-500 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">View Service Details &rarr;</span>
                        <div class="w-8 h-8 rounded-full bg-slate-50 dark:bg-slate-800 group-hover:bg-blue-600 text-slate-400 group-hover:text-white flex items-center justify-center transition">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                        </div>
                    </div>
                </a>
            @endforeach
        </div>
    </section>

    <!-- 4. OUR PORTFOLIO SECTION -->
    <section class="py-16 sm:py-20 bg-slate-50/60 dark:bg-slate-900/40 border-y border-slate-100 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-12 gap-4">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-2">Our Portfolio</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                        A glimpse of our recent projects
                    </h2>
                </div>
                <a href="{{ route('portfolio.index') }}" 
                   class="inline-flex items-center text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 group">
                    <span>View All Projects</span>
                    <svg class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
            </div>

            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                @foreach($projects as $project)
                    <div class="group rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl dark:hover:shadow-blue-950/40 transition-all duration-300 flex flex-col justify-between">
                        <div class="relative h-48 bg-slate-900 overflow-hidden">
                            <img src="{{ $project->image }}" alt="{{ $project->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition-transform duration-500">
                            <div class="absolute top-3 left-3">
                                <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-slate-800 dark:text-white shadow-sm border border-white/20 dark:border-slate-700">
                                    {{ $project->category }}
                                </span>
                            </div>
                        </div>

                        <div class="p-5 flex flex-col justify-between flex-grow">
                            <div>
                                <h3 class="text-base font-bold text-slate-900 dark:text-white mb-1 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition-colors">
                                    {{ $project->title }}
                                </h3>
                                <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                    {{ $project->summary }}
                                </p>
                            </div>

                            <div class="mt-4 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                                <span class="text-xs text-slate-400 dark:text-slate-500 font-medium">Case Study</span>
                                <a href="{{ route('portfolio.show', $project->slug) }}" 
                                   class="text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 transition" 
                                   aria-label="View {{ $project->title }}">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                                </a>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </section>

    <!-- 5. WHY CHOOSE US & KEY STATS -->
    <section class="py-16 sm:py-20 lg:py-28 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12 items-center">
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-2">Why Choose Us</span>
                    <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight leading-tight">
                        We combine creativity with technology
                    </h2>
                </div>
                <p class="text-slate-600 dark:text-slate-300 text-sm sm:text-base leading-relaxed">
                    We're not just developers, we're your technology partners. We architect solutions with uncompromising attention to clean code, performance, and long-term business scalability.
                </p>
                <div>
                    <a href="{{ route('contact') }}" 
                       class="inline-flex items-center justify-center px-7 py-3 rounded-full bg-slate-950 dark:bg-blue-600 text-white font-semibold text-sm hover:bg-blue-600 dark:hover:bg-blue-500 transition shadow-sm group">
                        <span>Work With Us</span>
                        <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                            <path d="M5 12h14M12 5l7 7-7 7"/>
                        </svg>
                    </a>
                </div>
            </div>

            <div class="lg:col-span-7">
                <div class="grid grid-cols-2 gap-4 sm:gap-6">
                    <div class="p-5 sm:p-7 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition text-center sm:text-left flex flex-col justify-between">
                        <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center mb-4 mx-auto sm:mx-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                        </div>
                        <div class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white mb-1 tracking-tight stat-counter-value" data-stat-value="{{ $statClients }}">{{ $statClients }}</div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Happy Clients</div>
                    </div>

                    <div class="p-5 sm:p-7 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition text-center sm:text-left flex flex-col justify-between">
                        <div class="w-10 h-10 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center mb-4 mx-auto sm:mx-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><rect width="20" height="14" x="2" y="7" rx="2" ry="2"/><path d="M16 21V5a2 2 0 0 0-2-2h-4a2 2 0 0 0-2 2v16"/></svg>
                        </div>
                        <div class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white mb-1 tracking-tight stat-counter-value" data-stat-value="{{ $statProjects }}">{{ $statProjects }}</div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Projects Completed</div>
                    </div>

                    <div class="p-5 sm:p-7 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition text-center sm:text-left flex flex-col justify-between">
                        <div class="w-10 h-10 rounded-xl bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 flex items-center justify-center mb-4 mx-auto sm:mx-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M6 9H4.5a2.5 2.5 0 0 1 0-5H6"/><path d="M18 9h1.5a2.5 2.5 0 0 0 0-5H18"/><path d="M4 22h16"/><path d="M10 14.66V17c0 .55-.47.98-.97 1.21C7.85 18.75 7 20.24 7 22"/><path d="M14 14.66V17c0 .55.47.98.97 1.21C16.15 18.75 17 20.24 17 22"/><path d="M18 2H6v7a6 6 0 0 0 12 0V2Z"/></svg>
                        </div>
                        <div class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white mb-1 tracking-tight stat-counter-value" data-stat-value="{{ $statYears }}">{{ $statYears }}</div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Years Experience</div>
                    </div>

                    <div class="p-5 sm:p-7 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition text-center sm:text-left flex flex-col justify-between">
                        <div class="w-10 h-10 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center mb-4 mx-auto sm:mx-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m12 15-3-3m0 0 3-3m-3 3h12"/><path d="M4.5 16.5c-1.5 1.26-2 3.5-2 3.5s2.24-.5 3.5-2"/></svg>
                        </div>
                        <div class="text-2xl sm:text-4xl font-black text-slate-900 dark:text-white mb-1 tracking-tight stat-counter-value" data-stat-value="{{ $statSat }}">{{ $statSat }}</div>
                        <div class="text-xs sm:text-sm font-medium text-slate-500 dark:text-slate-400">Client Satisfaction</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- 6. CLIENT TESTIMONIALS (Temporarily hidden) -->
    {{--
    <section class="py-16 sm:py-20 bg-slate-50/50 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="text-center max-w-xl mx-auto mb-12 sm:mb-14">
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-2">Client Testimonials</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    What our clients say
                </h2>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach($testimonials as $testimonial)
                    <div class="p-6 sm:p-7 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm hover:shadow-md transition flex flex-col justify-between">
                        <div>
                            <div class="flex items-center gap-1 text-amber-400 mb-4">
                                @for($i = 0; $i < $testimonial->rating; $i++)
                                    <svg class="w-4 h-4 fill-current" viewBox="0 0 24 24"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"/></svg>
                                @endfor
                            </div>
                            <p class="text-sm text-slate-600 dark:text-slate-300 leading-relaxed italic mb-6">
                                "{{ $testimonial->content }}"
                            </p>
                        </div>

                        <div class="flex items-center gap-3 pt-4 border-t border-slate-100 dark:border-slate-800">
                            <img src="{{ $testimonial->avatar ?? '/assets/images/testimonials/avatar-1.jpg' }}" 
                                 alt="{{ $testimonial->client_name }}" 
                                 loading="lazy"
                                 decoding="async"
                                 class="w-10 h-10 rounded-full object-cover flex-shrink-0">
                            <div class="min-w-0">
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white truncate">{{ $testimonial->client_name }}</h4>
                                <p class="text-xs text-slate-400 dark:text-slate-500 truncate">{{ $testimonial->designation }}, {{ $testimonial->company }}</p>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="flex items-center justify-center gap-1.5 mt-8">
                <span class="w-6 h-1.5 rounded-full bg-blue-600"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
                <span class="w-1.5 h-1.5 rounded-full bg-slate-300 dark:bg-slate-700"></span>
            </div>
        </div>
    </section>
    --}}

    <!-- 7. CALL TO ACTION BANNER -->
    <x-cta-banner />

    <!-- 8. LATEST INSIGHTS & BLOG UPDATES -->
    <section class="py-16 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row md:items-end justify-between mb-10 sm:mb-12 gap-4">
            <div>
                <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-2">From Our Blog</span>
                <h2 class="text-3xl sm:text-4xl font-extrabold text-slate-900 dark:text-white tracking-tight">
                    Latest insights &amp; updates
                </h2>
            </div>
            <a href="{{ route('blog.index') }}" 
               class="inline-flex items-center text-sm font-semibold text-slate-700 dark:text-slate-300 hover:text-blue-600 dark:hover:text-blue-400 group">
                <span>View All Posts</span>
                <svg class="w-4 h-4 ml-1.5 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
            </a>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            @foreach($recentBlogs as $blog)
                <div class="group rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-xl dark:hover:shadow-blue-950/40 transition-all duration-300 flex flex-col justify-between">
                    <div class="h-44 bg-slate-900 relative overflow-hidden">
                        <img src="{{ $blog->image }}" alt="{{ $blog->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                        <div class="absolute top-3 left-3">
                            <span class="px-2.5 py-1 rounded-full text-[11px] font-semibold bg-white/90 dark:bg-slate-900/90 backdrop-blur-md text-blue-600 dark:text-blue-400 border border-white/20 dark:border-slate-700">
                                {{ $blog->category }}
                            </span>
                        </div>
                    </div>

                    <div class="p-6 flex flex-col justify-between flex-grow">
                        <div>
                            <span class="text-[11px] text-slate-400 dark:text-slate-500 font-medium block mb-2">
                                {{ $blog->published_at ? $blog->published_at->format('M d, Y') : 'Recent' }} • {{ $blog->read_time }}
                            </span>
                            <h3 class="text-base font-bold text-slate-900 dark:text-white mb-2 group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">
                                {{ $blog->title }}
                            </h3>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed">
                                {{ $blog->excerpt }}
                            </p>
                        </div>

                        <div class="mt-5 pt-3 border-t border-slate-100 dark:border-slate-800 flex items-center justify-between">
                            <a href="{{ route('blog.show', $blog->slug) }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:text-blue-700 dark:hover:text-blue-300 flex items-center gap-1 group/btn">
                                <span>Read More</span>
                                <svg class="w-3.5 h-3.5 transition-transform group-hover/btn:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </section>
@endsection

@push('scripts')
<script @if(!empty($cspNonce)) nonce="{{ $cspNonce }}" @endif>
(function () {
    function initStatCounters() {
        var counters = document.querySelectorAll('.stat-counter-value');
        if (!counters.length) return;

        function parse(val) {
            var raw = (val || '').trim();
            var match = raw.match(/^([^0-9\.]*)(\d+(?:\.\d+)?)(.*)$/);
            if (!match) return { prefix: '', target: 0, suffix: raw, decimals: 0 };
            return {
                prefix: match[1] || '',
                target: parseFloat(match[2]),
                suffix: match[3] || '',
                decimals: match[2].indexOf('.') !== -1 ? match[2].split('.')[1].length : 0
            };
        }

        function run(el) {
            var raw = el.getAttribute('data-stat-value') || el.innerText;
            var parsed = parse(raw);
            if (isNaN(parsed.target) || parsed.target <= 0) return;

            var duration = 1800; // 1.8s smooth rolling duration
            var start = null;

            function step(timestamp) {
                if (!start) start = timestamp;
                var progress = Math.min((timestamp - start) / duration, 1);
                // Ease out cubic curve: 1 - (1 - progress)^3
                var ease = 1 - Math.pow(1 - progress, 3);
                var current = parsed.target * ease;

                el.innerText = parsed.prefix + (parsed.decimals > 0 ? current.toFixed(parsed.decimals) : Math.floor(current)) + parsed.suffix;

                if (progress < 1) {
                    window.requestAnimationFrame(step);
                } else {
                    el.innerText = raw;
                }
            }

            window.requestAnimationFrame(step);
        }

        if ('IntersectionObserver' in window) {
            var observer = new IntersectionObserver(function (entries, obs) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) {
                        run(entry.target);
                        obs.unobserve(entry.target);
                    }
                });
            }, { threshold: 0.25 });

            counters.forEach(function (el) {
                var raw = el.getAttribute('data-stat-value') || el.innerText;
                var parsed = parse(raw);
                if (!isNaN(parsed.target) && parsed.target > 0) {
                    el.innerText = parsed.prefix + '0' + parsed.suffix;
                }
                observer.observe(el);
            });
        } else {
            counters.forEach(function (el) {
                run(el);
            });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', initStatCounters);
    } else {
        initStatCounters();
    }
})();
</script>
@endpush
