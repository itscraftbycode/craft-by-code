@extends('layouts.app')

@php
    $brandName = \App\Models\WebsiteSetting::get('brand_name', 'craft by code');
@endphp

@section('title', 'Contact Us | ' . $brandName . ' - Start Your Startup Project')
@section('meta_description', 'Get in touch with ' . $brandName . '. Request an MVP or SaaS proposal, discuss DevOps & cloud architecture, or schedule a technical discovery call for your startup.')
@section('canonical', route('contact'))

@php
    $email = \App\Models\WebsiteSetting::get('contact_email', 'contact@craftbycode.in');
    $phone = \App\Models\WebsiteSetting::get('contact_phone', '+1 (555) 019-2834');
    $address = \App\Models\WebsiteSetting::get('contact_address', '');
    if (str_contains((string) $address, 'Innovation Way')) {
        $address = '';
    }
    $hours = \App\Models\WebsiteSetting::get('contact_hours', 'Monday - Friday: 9:00 AM - 6:00 PM EST');
    $mapsUrl = \App\Models\WebsiteSetting::get('google_maps_url', 'https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3153.0863816568285!2d-122.39997232348573!3d37.78802271168436!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x80858062828b8c73%3A0x6b63d91cf3241bc3!2sMarket%20St%2C%20San%20Francisco%2C%20CA!5e0!3m2!1sen!2sus!4v1710000000000!5m2!1sen!2sus');
@endphp

@section('content')
    <!-- Header -->
    <section class="py-14 sm:py-20 gradient-hero border-b border-slate-100 dark:border-slate-800/80">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
            <span class="text-xs font-bold uppercase tracking-wider text-blue-600 dark:text-blue-400 block mb-3">Get In Touch</span>
            <h1 class="text-3xl sm:text-5xl font-black text-slate-900 dark:text-white tracking-tight leading-tight mb-5">
                Let's Build Something <span class="bg-gradient-to-r from-blue-600 via-indigo-600 to-purple-600 dark:from-blue-400 dark:via-indigo-400 dark:to-purple-400 bg-clip-text text-transparent">Amazing Together</span>
            </h1>
            <p class="text-base sm:text-lg text-slate-600 dark:text-slate-300 max-w-2xl mx-auto leading-relaxed">
                Have a project in mind, need technical advisory, or want to explore an enterprise partnership? Send us a message and our lead engineers will reply promptly.
            </p>
        </div>
    </section>

    <!-- Main Contact Section -->
    <section class="py-14 sm:py-20 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-10 lg:gap-12">
            <!-- Left 5 cols: Contact Cards & Info -->
            <div class="lg:col-span-5 space-y-6">
                <div>
                    <h2 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Direct Contact Information</h2>
                    <p class="text-sm text-slate-500 dark:text-slate-400 leading-relaxed">
                        We pride ourselves on transparent communication and rapid turnaround. Reach out directly or complete the form.
                    </p>
                </div>

                <!-- Info Cards -->
                <div class="space-y-4">
                    <!-- Email -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-blue-50 dark:bg-blue-950/60 text-blue-600 dark:text-blue-400 flex items-center justify-center flex-shrink-0">
                            <x-icon name="mail" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Email Us</span>
                            <a href="mailto:{{ $email }}" class="text-sm font-bold text-slate-900 dark:text-white hover:text-blue-600 dark:hover:text-blue-400 transition break-all">{{ $email }}</a>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Average response: &lt; 2 hours</p>
                        </div>
                    </div>

                    <!-- Phone -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-purple-50 dark:bg-purple-950/60 text-purple-600 dark:text-purple-400 flex items-center justify-center flex-shrink-0">
                            <x-icon name="phone" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Call Our Office</span>
                            <span class="text-sm font-bold text-slate-900 dark:text-white block">{{ $phone }}</span>
                            <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">{{ $hours }}</p>
                        </div>
                    </div>

                    <!-- Location / Operations -->
                    <div class="p-5 sm:p-6 rounded-2xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-sm flex items-start gap-4">
                        <div class="w-12 h-12 rounded-xl bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 flex items-center justify-center flex-shrink-0">
                            <x-icon name="map-pin" class="w-6 h-6" />
                        </div>
                        <div class="min-w-0">
                            <span class="text-xs font-bold text-slate-400 dark:text-slate-500 uppercase tracking-wider block mb-1">Operations</span>
                            @if(!empty($address))
                                <p class="text-sm font-bold text-slate-900 dark:text-white leading-snug">{{ $address }}</p>
                            @else
                                <p class="text-sm font-bold text-slate-900 dark:text-white leading-snug">Operating Globally • Remote-First Digital Studio</p>
                                <p class="text-xs text-slate-400 dark:text-slate-500 mt-0.5">Serving clients across India, North America, UK &amp; Europe</p>
                            @endif
                        </div>
                    </div>
                </div>

                @if(!empty($mapsUrl) && !str_contains($mapsUrl, 'Market%20St'))
                    <!-- Google Maps Embed -->
                    <div class="rounded-3xl overflow-hidden border border-slate-200 dark:border-slate-800 shadow-sm h-56 sm:h-64 bg-slate-100 dark:bg-slate-900">
                        <iframe src="{{ $mapsUrl }}" 
                                width="100%" 
                                height="100%" 
                                style="border:0;" 
                                allowfullscreen="" 
                                loading="lazy" 
                                referrerpolicy="no-referrer-when-downgrade"
                                title="Office Map Location"></iframe>
                    </div>
                @endif
            </div>

            <!-- Right 7 cols: Contact Inquiry Form -->
            <div class="lg:col-span-7">
                <div class="p-6 sm:p-10 lg:p-12 rounded-3xl bg-white dark:bg-slate-900/90 border border-slate-100 dark:border-slate-800 shadow-xl">
                    <h3 class="text-2xl font-bold text-slate-900 dark:text-white mb-2">Send Us a Message</h3>
                    <p class="text-sm text-slate-500 dark:text-slate-400 mb-6">All fields marked with an asterisk (*) are required. Inquiries are logged directly into our engineering dashboard.</p>

                    @if(session('success'))
                        <div class="p-4 mb-6 rounded-2xl bg-emerald-50 dark:bg-emerald-950/60 border border-emerald-200 dark:border-emerald-800 text-emerald-800 dark:text-emerald-200 text-sm font-semibold flex items-center gap-3">
                            <svg class="w-5 h-5 flex-shrink-0 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            <span>{{ session('success') }}</span>
                        </div>
                    @endif

                    @if($errors->any())
                        <div class="p-4 mb-6 rounded-2xl bg-rose-50 dark:bg-rose-950/60 border border-rose-200 dark:border-rose-800 text-rose-800 dark:text-rose-200 text-xs font-medium space-y-1">
                            @foreach($errors->all() as $err)
                                <div>&bull; {{ $err }}</div>
                            @endforeach
                        </div>
                    @endif

                    <form action="{{ route('contact.submit') }}" method="POST" class="space-y-6">
                        @csrf

                        <!-- Honeypot Bot Trap (Invisible to humans) -->
                        <div style="display:none !important;" aria-hidden="true">
                            <label for="contact_hp_website">Leave this field blank</label>
                            <input type="text" id="contact_hp_website" name="contact_hp_website" value="" tabindex="-1" autocomplete="off">
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Name -->
                            <div>
                                <label for="name" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Your Name *</label>
                                <input type="text" id="name" name="name" required value="{{ old('name') }}" placeholder="Alex Morgan"
                                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:focus:bg-slate-950 transition">
                            </div>

                            <!-- Email -->
                            <div>
                                <label for="email" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Work Email *</label>
                                <input type="email" id="email" name="email" required value="{{ old('email') }}" placeholder="alex@company.com"
                                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:focus:bg-slate-950 transition">
                            </div>
                        </div>

                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                            <!-- Phone -->
                            <div>
                                <label for="phone" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Phone Number</label>
                                <input type="text" id="phone" name="phone" value="{{ old('phone') }}" placeholder="+1 (555) 000-0000"
                                       class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:focus:bg-slate-950 transition">
                            </div>

                            <!-- Service Interested -->
                            <div>
                                <label for="service_interested" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Service of Interest</label>
                                <select id="service_interested" name="service_interested" 
                                        class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:focus:bg-slate-950 transition">
                                    <option value="">Select a service (optional)</option>
                                    @foreach($services as $sTitle)
                                        <option value="{{ $sTitle }}" {{ (old('service_interested') == $sTitle || request('service_interested') == $sTitle) ? 'selected' : '' }}>
                                            {{ $sTitle }}
                                        </option>
                                    @endforeach
                                    <option value="General Enterprise Software">General Enterprise Software</option>
                                </select>
                            </div>
                        </div>

                        <!-- Subject -->
                        <div>
                            <label for="subject" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Subject</label>
                            <input type="text" id="subject" name="subject" value="{{ old('subject', request('subject', 'New Project Inquiry')) }}" placeholder="e.g. Mobile App Development for Startup"
                                   class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:focus:bg-slate-950 transition">
                        </div>

                        <!-- Message -->
                        <div>
                            <label for="message" class="block text-xs font-bold text-slate-700 dark:text-slate-300 uppercase tracking-wider mb-2">Project Brief & Details *</label>
                            <textarea id="message" name="message" required rows="5" placeholder="Tell us about your project, current stage, expected deliverables, and approximate timeline..."
                                      class="w-full px-4 py-3 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-sm text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:bg-white dark:focus:bg-slate-950 transition">{{ old('message') }}</textarea>
                        </div>

                        <!-- CAPTCHA Verification (Google reCAPTCHA / Cloudflare Turnstile) -->
                        <x-captcha />

                        <!-- Submit Button -->
                        <div>
                            <button type="submit" 
                                    class="w-full sm:w-auto inline-flex items-center justify-center px-10 py-4 rounded-full bg-slate-950 dark:bg-blue-600 text-white font-bold text-sm hover:bg-blue-600 dark:hover:bg-blue-500 transition shadow-lg hover:shadow-xl hover:scale-[1.01] group">
                                <span>Send Inquiry</span>
                                <svg class="w-4 h-4 ml-2 transition-transform duration-200 group-hover:translate-x-1" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24">
                                    <path d="M5 12h14M12 5l7 7-7 7"/>
                                </svg>
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
@endsection

