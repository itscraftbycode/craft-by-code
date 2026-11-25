@extends('layouts.app')

@php
    $brandName = \App\Models\WebsiteSetting::get('brand_name', 'craft by code');
@endphp

@section('title', ($service->meta_title ?? $service->title . ' for Startups') . ' | ' . $brandName)
@section('meta_description', $service->meta_description ?? $service->short_description)
@section('canonical', route('services.show', $service->slug))

@push('schema')
@php
    $serviceSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'Service',
        'name' => $service->title,
        'description' => $service->meta_description ?? $service->short_description,
        'provider' => [
            '@type' => 'Organization',
            'name' => $brandName,
            'url' => route('home'),
        ],
        'areaServed' => 'Worldwide',
        'url' => route('services.show', $service->slug),
    ];
    $breadcrumbSchema = [
        '@context' => 'https://schema.org',
        '@type' => 'BreadcrumbList',
        'itemListElement' => [
            ['@type' => 'ListItem', 'position' => 1, 'name' => 'Home', 'item' => route('home')],
            ['@type' => 'ListItem', 'position' => 2, 'name' => 'Services', 'item' => route('services.index')],
            ['@type' => 'ListItem', 'position' => 3, 'name' => $service->title, 'item' => route('services.show', $service->slug)],
        ],
    ];
@endphp
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
    {!! json_encode($serviceSchema, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
<script type="application/ld+json" nonce="{{ $cspNonce ?? '' }}">
    {!! json_encode($breadcrumbSchema, JSON_HEX_TAG | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE) !!}
</script>
@endpush

@section('content')
    <!-- Service Header -->
    <section class="py-12 sm:py-16 lg:py-20 gradient-hero border-b border-slate-100 dark:border-slate-800/80">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-wrap items-center gap-2 text-xs font-medium text-slate-500 dark:text-slate-400 mb-5 sm:mb-6">
                <a href="{{ route('home') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Home</a>
                <span>/</span>
                <a href="{{ route('services.index') }}" class="hover:text-blue-600 dark:hover:text-blue-400 transition">Services</a>
                <span>/</span>
                <span class="text-slate-900 dark:text-white font-semibold">{{ $service->title }}</span>
            </div>

            <div class="flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div class="max-w-3xl">
                    <div class="flex items-start sm:items-center gap-4 mb-4">
                        <div class="w-12 h-12 sm:w-14 sm:h-14 rounded-2xl bg-blue-600 text-white flex items-center justify-center shadow-md flex-shrink-0">
                            <x-icon :name="$service->icon" class="w-6 h-6 sm:w-7 sm:h-7" />
                        </div>
                        <div class="min-w-0">
                            @if($service->badge)
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-blue-50 dark:bg-blue-950/70 text-blue-700 dark:text-blue-300 border border-blue-100 dark:border-blue-800/80 inline-block mb-1">
                                    {{ $service->badge }}
                                </span>
                            @endif
                            <h1 class="text-2xl sm:text-4xl lg:text-5xl font-black text-slate-900 dark:text-white tracking-tight mt-1">
                                {{ $service->title }}
                            </h1>
                        </div>
                    </div>
                    <p class="text-sm sm:text-lg text-slate-600 dark:text-slate-300 leading-relaxed">
                        {{ $service->short_description }}
                    </p>
                </div>

                <div class="w-full md:w-auto flex-shrink-0">
                    <a href="#inquire-form" 
                       class="w-full md:w-auto inline-flex items-center justify-center px-8 py-3.5 rounded-full bg-slate-900 dark:bg-blue-600 text-white font-semibold text-sm hover:bg-blue-600 dark:hover:bg-blue-500 transition shadow hover:shadow-md">
                        <span>Inquire About This Service</span>
                        <svg class="w-4 h-4 ml-2" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Main Service Content Grid -->
    <section class="py-12 sm:py-16 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
            <!-- Left 8 cols: Detailed Overview, Features, Benefits, Tech Stack -->
            <div class="lg:col-span-8 space-y-10 sm:space-y-12">
                <!-- Overview -->
                <div>
                    <h2 class="text-xl sm:text-2xl font-bold text-slate-900 dark:text-white mb-4">Service Overview</h2>
                    <div class="prose prose-slate dark:prose-invert max-w-none text-slate-600 dark:text-slate-300 leading-relaxed space-y-4">
                        <p class="text-sm sm:text-lg">
                            {{ $service->full_description }}
                        </p>
                    </div>
                </div>

                <!-- Key Features -->
                @if(!empty($service->features))
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-5 sm:mb-6">Key Capabilities & Features</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                            @foreach($service->features as $feat)
                                <div class="p-4 rounded-xl bg-slate-50 dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-full bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <x-icon name="check" class="w-3.5 h-3.5" />
                                    </div>
                                    <span class="text-sm font-medium text-slate-700 dark:text-slate-200">{{ $feat }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Business Benefits -->
                @if(!empty($service->benefits))
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-5 sm:mb-6">Business Benefits</h3>
                        <div class="space-y-3">
                            @foreach($service->benefits as $ben)
                                <div class="p-4 rounded-xl bg-blue-50/50 dark:bg-blue-950/30 border border-blue-100/60 dark:border-blue-900/60 flex items-start gap-3">
                                    <div class="w-5 h-5 rounded-full bg-blue-600 text-white flex items-center justify-center flex-shrink-0 mt-0.5">
                                        <x-icon name="sparkles" class="w-3 h-3" />
                                    </div>
                                    <span class="text-sm font-medium text-slate-800 dark:text-slate-200">{{ $ben }}</span>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Technologies Used -->
                @if(!empty($service->tech_stack))
                    <div>
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-4">Technologies & Frameworks We Use</h3>
                        <div class="flex flex-wrap gap-2.5">
                            @foreach($service->tech_stack as $tech)
                                <span class="px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 shadow-sm">
                                    {{ $tech }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                <!-- Related Case Studies / Projects -->
                @if($relatedProjects->isNotEmpty())
                    <div class="pt-8 border-t border-slate-100 dark:border-slate-800">
                        <h3 class="text-lg sm:text-xl font-bold text-slate-900 dark:text-white mb-6">Related Projects</h3>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            @foreach($relatedProjects as $proj)
                                <a href="{{ route('portfolio.show', $proj->slug) }}" class="group block rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 overflow-hidden shadow-sm hover:shadow-lg transition">
                                    <div class="h-40 bg-slate-900 overflow-hidden">
                                        <img src="{{ $proj->image }}" alt="{{ $proj->title }}" loading="lazy" decoding="async" class="w-full h-full object-cover group-hover:scale-105 transition duration-500">
                                    </div>
                                    <div class="p-4">
                                        <span class="text-[11px] font-semibold text-blue-600 dark:text-blue-400 block mb-1">{{ $proj->category }}</span>
                                        <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition">{{ $proj->title }}</h4>
                                    </div>
                                </a>
                            @endforeach
                        </div>
                    </div>
                @endif
            </div>

            <!-- Right 4 cols: Sticky Quick Inquiry Box -->
            <div class="lg:col-span-4">
                <div id="inquire-form" class="sticky top-28 p-5 sm:p-7 rounded-3xl bg-slate-50 dark:bg-slate-900/90 border border-slate-200/80 dark:border-slate-800 shadow-md">
                    <div class="flex items-center justify-between mb-2">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-blue-50 dark:bg-blue-950/60 text-blue-700 dark:text-blue-300 border border-blue-200 dark:border-blue-800">
                            Direct Inquiry
                        </span>
                        <span class="text-[11px] text-slate-400 dark:text-slate-500">Fast 24h Response</span>
                    </div>
                    <h3 class="text-lg font-bold text-slate-900 dark:text-white mb-1">Inquire About {{ $service->title }}</h3>
                    <p class="text-xs text-slate-500 dark:text-slate-400 mb-6">Fill in your requirements and our solutions architect will reach out within 24 hours.</p>

                    @if(session('success'))
                        <div class="p-3 mb-4 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-700 dark:text-emerald-300 text-xs font-semibold flex items-center gap-2">
                            <svg class="w-4 h-4 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-3 mb-4 rounded-xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-700 dark:text-rose-300 text-xs font-medium space-y-1">
                            @foreach($errors->all() as $err)
                                <div>&bull; {{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="service_interested" value="{{ $service->title }}">
                        <input type="hidden" name="subject" value="Inquiry for {{ $service->title }}">

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Your Name *</label>
                            <input type="text" name="name" required value="{{ old('name') }}" placeholder="Alex Morgan"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Work Email *</label>
                            <input type="email" name="email" required value="{{ old('email') }}" placeholder="alex@company.com"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Phone Number</label>
                            <input type="text" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"
                                   class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>

                        <div>
                            <label class="block text-xs font-semibold text-slate-700 dark:text-slate-300 mb-1">Project Details *</label>
                            <textarea name="message" required rows="4" placeholder="Briefly describe your objectives, timeline, or current challenges..."
                                      class="w-full px-3.5 py-2.5 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('message') }}</textarea>
                        </div>

                        <!-- CAPTCHA Verification (Google reCAPTCHA / Cloudflare Turnstile) -->
                        <x-captcha />

                        <button type="submit" 
                                class="w-full py-3 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-xs tracking-wide uppercase transition shadow-md hover:shadow-lg hover:scale-[1.01]">
                            Submit Inquiry
                        </button>
                    </form>

                    @php($directEmail = \App\Models\WebsiteSetting::get('contact_email', 'hello@craftbycode.tech'))
                    <div class="mt-6 pt-4 border-t border-slate-200 dark:border-slate-800 text-center">
                        <p class="text-[11px] text-slate-400 dark:text-slate-500">Prefer direct email?</p>
                        <a href="mailto:{{ $directEmail }}" class="text-xs font-semibold text-blue-600 dark:text-blue-400 hover:underline break-all">{{ $directEmail }}</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Other Services Multi-Page Navigation Grid -->
    @if($otherServices->isNotEmpty())
        <section class="py-14 sm:py-18 bg-slate-50/70 dark:bg-slate-900/40 border-t border-slate-100 dark:border-slate-800">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between mb-8 gap-4">
                    <div>
                        <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-1">Explore More Services</span>
                        <h3 class="text-2xl font-black text-slate-900 dark:text-white">Other Solutions We Engineer</h3>
                    </div>
                    <a href="{{ route('services.index') }}" class="text-xs font-bold text-blue-600 dark:text-blue-400 hover:underline flex items-center gap-1">
                        <span>View All 6 Services</span>
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($otherServices as $other)
                        <a href="{{ route('services.show', $other->slug) }}" 
                           class="group p-6 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-200/70 dark:border-slate-800 hover:border-blue-300 dark:hover:border-blue-600 shadow-sm hover:shadow-lg transition-all duration-200 block hover:-translate-y-0.5">
                            <div class="flex items-center gap-3.5 mb-3">
                                <div class="w-10 h-10 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0 group-hover:bg-blue-600 group-hover:text-white transition">
                                    <x-icon :name="$other->icon" class="w-5 h-5" />
                                </div>
                                <h4 class="text-sm font-bold text-slate-900 dark:text-white group-hover:text-blue-600 dark:group-hover:text-blue-400 transition truncate">
                                    {{ $other->title }}
                                </h4>
                            </div>
                            <p class="text-xs text-slate-500 dark:text-slate-400 line-clamp-2 leading-relaxed mb-4">
                                {{ $other->short_description }}
                            </p>
                            <span class="text-xs font-bold text-blue-600 dark:text-blue-400 group-hover:translate-x-1 inline-flex items-center gap-1 transition">
                                <span>Open Page</span>
                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                            </span>
                        </a>
                    @endforeach
                </div>
            </div>
        </section>
    @endif

    <!-- CTA Banner -->
    <x-cta-banner />
@endsection

