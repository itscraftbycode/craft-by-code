@extends('layouts.admin')

@section('title', 'Admin Dashboard')
@section('header_title', 'Dashboard Overview')

@section('content')
    <div class="space-y-8">
        <!-- Welcome Banner -->
        <div class="relative overflow-hidden rounded-3xl bg-gradient-to-r from-slate-900 via-indigo-950 to-blue-900 p-8 text-white shadow-xl">
            <div class="relative z-10 flex flex-col md:flex-row md:items-center justify-between gap-6">
                <div>
                    <span class="px-3 py-1 rounded-full text-[11px] font-bold bg-blue-500/20 text-blue-300 border border-blue-400/30 inline-block mb-3">
                        {{ \App\Models\WebsiteSetting::get('brand_name', 'craft by code') }} Enterprise Engine
                    </span>
                    <h1 class="text-2xl sm:text-3xl font-black tracking-tight mb-2">
                        Welcome back, {{ Auth::user()->name }}!
                    </h1>
                    <p class="text-xs sm:text-sm text-slate-300 max-w-xl leading-relaxed">
                        Manage your live services, client inquiries, case studies, and website content directly without touching source code.
                    </p>
                </div>

                <div class="flex flex-wrap items-center gap-3">
                    <a href="{{ route('admin.inquiries.index') }}" 
                       class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow flex items-center gap-2">
                        <x-icon name="mail" class="w-4 h-4" />
                        <span>View Inquiries</span>
                    </a>
                    @if(Auth::user()?->hasPermission('agreements.view'))
                        <a href="{{ route('admin.agreements.index') }}" 
                           class="px-5 py-2.5 rounded-xl bg-indigo-500/20 hover:bg-indigo-500/30 text-indigo-200 font-bold text-xs transition border border-indigo-400/30 flex items-center gap-2">
                            <x-icon name="file-text" class="w-4 h-4" />
                            <span>Agreements ({{ $stats['total_agreements'] ?? 0 }})</span>
                        </a>
                    @endif
                    @if(Auth::user()?->hasPermission('security.view'))
                        <a href="{{ route('admin.security.index') }}" 
                           class="px-5 py-2.5 rounded-xl bg-emerald-500/20 hover:bg-emerald-500/30 text-emerald-200 font-bold text-xs transition border border-emerald-400/30 flex items-center gap-2">
                            <x-icon name="shield-check" class="w-4 h-4" />
                            <span>Security Center</span>
                        </a>
                    @endif
                    <a href="{{ route('admin.settings.index') }}" 
                       class="px-5 py-2.5 rounded-xl bg-white/10 hover:bg-white/20 text-white font-bold text-xs transition border border-white/10">
                        <span>Edit Website</span>
                    </a>
                </div>
            </div>
        </div>

        <!-- 7 Stat Cards Grid -->
        <div class="grid grid-cols-2 md:grid-cols-4 lg:grid-cols-7 gap-4">
            <!-- Unique Visitors -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Unique Visitors</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-icon name="users" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ number_format($stats['total_visitors'] ?? 0) }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1 flex items-center gap-1">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                    <span>+{{ $stats['today_visitors'] ?? 0 }} new today</span>
                </div>
            </div>

            <!-- Inquiries -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Inquiries</span>
                    <div class="w-8 h-8 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center">
                        <x-icon name="mail" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_inquiries'] }}</div>
                <div class="text-[11px] font-semibold text-rose-500 mt-1">
                    {{ $stats['unread_inquiries'] }} new unread
                </div>
            </div>

            <!-- Agreements & Contracts -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Agreements</span>
                    <div class="w-8 h-8 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center">
                        <x-icon name="file-text" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_agreements'] ?? 0 }}</div>
                <div class="text-[11px] font-semibold text-amber-600 mt-1">
                    {{ $stats['pending_agreements'] ?? 0 }} pending action
                </div>
            </div>

            <!-- Services -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Services</span>
                    <div class="w-8 h-8 rounded-lg bg-indigo-50 text-indigo-600 flex items-center justify-center">
                        <x-icon name="code" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_services'] }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1">
                    {{ $stats['active_services'] }} active online
                </div>
            </div>

            <!-- Projects -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Projects</span>
                    <div class="w-8 h-8 rounded-lg bg-amber-50 text-amber-600 flex items-center justify-center">
                        <x-icon name="briefcase" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_projects'] }}</div>
                <div class="text-[11px] font-semibold text-amber-600 mt-1">
                    {{ $stats['featured_projects'] }} featured
                </div>
            </div>

            <!-- Testimonials -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Testimonials</span>
                    <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center">
                        <x-icon name="star" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_testimonials'] }}</div>
                <div class="text-[11px] font-semibold text-emerald-600 mt-1">5-star verified</div>
            </div>

            <!-- Blog Articles -->
            <div class="p-5 rounded-2xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div class="flex items-center justify-between mb-2">
                    <span class="text-xs font-bold text-slate-500">Articles</span>
                    <div class="w-8 h-8 rounded-lg bg-pink-50 text-pink-600 flex items-center justify-center">
                        <x-icon name="sparkles" class="w-4 h-4" />
                    </div>
                </div>
                <div class="text-2xl font-black text-slate-900">{{ $stats['total_blogs'] }}</div>
                <div class="text-[11px] font-semibold text-slate-400 mt-1">Published posts</div>
            </div>
        </div>

        <!-- Recent Inquiries Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex items-center justify-between">
                <div>
                    <h3 class="text-base font-bold text-slate-900">Recent Customer Inquiries</h3>
                    <p class="text-xs text-slate-500 mt-0.5">Direct submissions from the website contact forms</p>
                </div>
                <a href="{{ route('admin.inquiries.index') }}" class="text-xs font-bold text-blue-600 hover:text-blue-700">
                    View All Inquiries &rarr;
                </a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50/70 text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100">
                        <tr>
                            <th class="py-3 px-6">Client Name</th>
                            <th class="py-3 px-6">Email / Phone</th>
                            <th class="py-3 px-6">Service of Interest</th>
                            <th class="py-3 px-6">Status</th>
                            <th class="py-3 px-6">Date</th>
                            <th class="py-3 px-6 text-right">Action</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentInquiries as $inq)
                            <tr class="hover:bg-slate-50/50 transition">
                                <td class="py-4 px-6 font-bold text-slate-900">
                                    {{ $inq->name }}
                                </td>
                                <td class="py-4 px-6">
                                    <div>{{ $inq->email }}</div>
                                    @if($inq->phone)
                                        <div class="text-[11px] text-slate-400">{{ $inq->phone }}</div>
                                    @endif
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-md bg-blue-50 text-blue-700 text-[11px] font-semibold">
                                        {{ $inq->service_interested ?? 'General Inquiry' }}
                                    </span>
                                </td>
                                <td class="py-4 px-6">
                                    @if($inq->status === 'new')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-rose-50 text-rose-600 border border-rose-200">New</span>
                                    @elseif($inq->status === 'read')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-blue-50 text-blue-600 border border-blue-200">Read</span>
                                    @elseif($inq->status === 'in_progress')
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-amber-50 text-amber-600 border border-amber-200">In Progress</span>
                                    @else
                                        <span class="px-2.5 py-1 rounded-full text-[10px] font-bold bg-emerald-50 text-emerald-600 border border-emerald-200">Resolved</span>
                                    @endif
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-[11px]">
                                    {{ $inq->created_at->diffForHumans() }}
                                </td>
                                <td class="py-4 px-6 text-right">
                                    <a href="{{ route('admin.inquiries.show', $inq->id) }}" class="inline-flex items-center px-3 py-1.5 rounded-lg bg-slate-100 hover:bg-blue-600 hover:text-white text-slate-700 font-bold transition">
                                        Review
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="py-8 text-center text-slate-400">
                                    No inquiries received yet.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Recent Unique Visitors Table -->
        <div class="bg-white rounded-3xl border border-slate-200/80 shadow-sm overflow-hidden">
            <div class="p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-2">
                        <span class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></span>
                        <h3 class="text-base font-bold text-slate-900">Live Website Traffic (Unique Visitors)</h3>
                    </div>
                    <p class="text-xs text-slate-500 mt-0.5">Real-time unique human visitors to the public website with geographical origin &amp; device signature</p>
                </div>
                <div class="flex items-center gap-2 flex-wrap">
                    @if(isset($topLocations) && $topLocations->isNotEmpty())
                        <div class="hidden md:flex items-center gap-1.5 mr-2">
                            @foreach($topLocations as $tl)
                                <span class="inline-flex items-center gap-1 text-[11px] font-semibold bg-slate-100 text-slate-700 px-2.5 py-1 rounded-xl">
                                    <span>{{ \App\Services\GeoLocationService::getFlagEmoji($tl->country_code) }}</span>
                                    <span>{{ $tl->country }}</span>
                                    <span class="text-slate-400 font-bold text-[10px]">({{ $tl->count }})</span>
                                </span>
                            @endforeach
                        </div>
                    @endif
                    <span class="text-xs font-bold text-slate-700 bg-slate-100 px-3 py-1.5 rounded-xl">
                        Total Unique: {{ number_format($stats['total_visitors'] ?? 0) }}
                    </span>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left text-xs text-slate-600">
                    <thead class="bg-slate-50 text-[10px] uppercase font-bold text-slate-400 tracking-wider">
                        <tr>
                            <th class="py-3 px-6">Visitor Location (Place)</th>
                            <th class="py-3 px-6">Device / Platform</th>
                            <th class="py-3 px-6">Network (Masked IP)</th>
                            <th class="py-3 px-6">Entry Page</th>
                            <th class="py-3 px-6">Page Views (Hits)</th>
                            <th class="py-3 px-6">First Seen</th>
                            <th class="py-3 px-6 text-right">Last Active</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentVisitors as $v)
                            <tr class="hover:bg-slate-50/80 transition">
                                <td class="py-4 px-6 text-slate-900">
                                    <div class="flex items-center gap-2.5">
                                        <span class="text-xl flex-shrink-0" title="{{ $v->country ?? 'Unknown' }}">{{ $v->country_flag }}</span>
                                        <div>
                                            <div class="font-bold text-slate-900 text-[12px] flex items-center gap-1">
                                                <span>{{ $v->location_label }}</span>
                                            </div>
                                            @if($v->region && $v->region !== $v->city)
                                                <div class="text-[10px] text-slate-400 font-medium">{{ $v->region }}</div>
                                            @endif
                                        </div>
                                    </div>
                                </td>
                                <td class="py-4 px-6 text-slate-900 font-semibold flex items-center gap-2">
                                    <div class="w-7 h-7 rounded-lg bg-slate-100 text-slate-600 flex items-center justify-center flex-shrink-0">
                                        <x-icon name="monitor" class="w-3.5 h-3.5" />
                                    </div>
                                    <span>{{ $v->device_label }}</span>
                                </td>
                                <td class="py-4 px-6 text-slate-600 font-mono text-[11px]">
                                    {{ $v->masked_ip }}
                                </td>
                                <td class="py-4 px-6 text-blue-600 font-mono text-[11px]">
                                    /{{ ltrim($v->landing_page, '/') }}
                                </td>
                                <td class="py-4 px-6">
                                    <span class="px-2.5 py-1 rounded-full text-[11px] font-bold bg-slate-100 text-slate-700">
                                        {{ $v->hits }} {{ Str::plural('page', $v->hits) }}
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-slate-400 text-[11px]">
                                    {{ $v->first_visited_at?->diffForHumans() ?? 'Just now' }}
                                </td>
                                <td class="py-4 px-6 text-right text-slate-500 text-[11px]">
                                    {{ $v->last_visited_at?->diffForHumans() ?? 'Just now' }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="7" class="py-8 text-center text-slate-400">
                                    No visitors recorded yet. Visitors to your public website will appear here in real time.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Quick Access Module Links -->
        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
            <!-- Services Shortcut -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center mb-4">
                        <x-icon name="code" class="w-5 h-5" />
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-1">Services Management</h4>
                    <p class="text-xs text-slate-500 mb-4">Add, edit, or adjust ordering of all 9 core software development offerings.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.services.create') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-blue-600 transition">
                        + Add Service
                    </a>
                    <a href="{{ route('admin.services.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                        View List
                    </a>
                </div>
            </div>

            <!-- Portfolio Shortcut -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center mb-4">
                        <x-icon name="briefcase" class="w-5 h-5" />
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-1">Portfolio & Case Studies</h4>
                    <p class="text-xs text-slate-500 mb-4">Upload screenshots, client testimonials, and tech stack details for case studies.</p>
                </div>
                <div class="flex items-center gap-2">
                    <a href="{{ route('admin.projects.create') }}" class="px-3 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-blue-600 transition">
                        + New Project
                    </a>
                    <a href="{{ route('admin.projects.index') }}" class="px-3 py-1.5 rounded-lg bg-slate-100 text-slate-700 text-xs font-semibold hover:bg-slate-200 transition">
                        View All
                    </a>
                </div>
            </div>

            <!-- Settings Shortcut -->
            <div class="p-6 rounded-3xl bg-white border border-slate-200/80 shadow-sm flex flex-col justify-between">
                <div>
                    <div class="w-10 h-10 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center mb-4">
                        <x-icon name="edit" class="w-5 h-5" />
                    </div>
                    <h4 class="text-base font-bold text-slate-900 mb-1">Live Website Content</h4>
                    <p class="text-xs text-slate-500 mb-4">Modify hero headline, statistics, email, phone number, address, and CTA banners.</p>
                </div>
                <div>
                    <a href="{{ route('admin.settings.index') }}" class="inline-block px-4 py-1.5 rounded-lg bg-slate-900 text-white text-xs font-semibold hover:bg-blue-600 transition">
                        Manage Settings
                    </a>
                </div>
            </div>
        </div>
    </div>
@endsection

