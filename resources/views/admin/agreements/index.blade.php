@extends('layouts.admin')

@section('title', 'Agreement & Contract Management')
@section('header_title', 'Agreement & Contract Management')

@section('content')
<div class="space-y-6">

    <!-- Top Header & Quick Links -->
    <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
        <div>
            <div class="flex items-center gap-2">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-black uppercase tracking-wider bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                    Strict Admin-Only
                </span>
                <span class="text-xs text-slate-400 dark:text-slate-500">Legal &amp; Contract Office</span>
            </div>
            <h1 class="text-2xl font-bold text-slate-900 dark:text-white mt-1">Digital Agreements &amp; Contracts</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400">Manage client contracts, digital signatures, e-Stamp certificates, and legal archives.</p>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <a href="{{ route('admin.agreements.archive') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                <x-icon name="shield-check" class="w-3.5 h-3.5 text-blue-600 dark:text-blue-400" />
                <span>Legal Archive</span>
            </a>
            <a href="{{ route('admin.agreements.templates.index') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                <x-icon name="layers" class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
                <span>Templates</span>
            </a>
            <a href="{{ route('admin.agreements.audit-logs') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                <x-icon name="clock" class="w-3.5 h-3.5 text-amber-600 dark:text-amber-400" />
                <span>Audit Trail</span>
            </a>
            <a href="{{ route('admin.agreements.settings') }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                <x-icon name="building" class="w-3.5 h-3.5 text-purple-600 dark:text-purple-400" />
                <span>Settings</span>
            </a>
            @if(Auth::user()->hasPermission('agreements.manage'))
                <form action="{{ route('admin.agreements.clear-dummy') }}" method="POST" onsubmit="return confirm('Are you sure you want to delete all dummy/sample agreements?');" class="inline">
                    @csrf
                    <button type="submit" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl border border-rose-200 dark:border-rose-900/50 bg-rose-50/60 dark:bg-rose-950/30 hover:bg-rose-100 text-xs font-bold text-rose-600 dark:text-rose-400 transition" title="Delete all sample dummy contracts">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                        <span>Purge Dummy Data</span>
                    </button>
                </form>
                <a href="{{ route('admin.agreements.create') }}" 
                   class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-md hover:shadow-lg">
                    <x-icon name="plus" class="w-3.5 h-3.5" />
                    <span>Generate Agreement</span>
                </a>
            @endif
        </div>
    </div>

    <!-- Metrics Dashboard Cards -->
    <div class="grid grid-cols-2 sm:grid-cols-4 lg:grid-cols-7 gap-3 sm:gap-4">
        <!-- Total -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Total</span>
            <div class="text-xl sm:text-2xl font-black text-slate-900 dark:text-white mt-1">{{ $metrics['total'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">All contracts</div>
        </div>

        <!-- Draft -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-slate-500 dark:text-slate-400">Drafts</span>
            <div class="text-xl sm:text-2xl font-black text-slate-700 dark:text-slate-300 mt-1">{{ $metrics['draft'] }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">In preparation</div>
        </div>

        <!-- Pending Signature -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-amber-600 dark:text-amber-400">Pending Sign</span>
            <div class="text-xl sm:text-2xl font-black text-amber-600 dark:text-amber-400 mt-1">{{ $metrics['pending_signature'] }}</div>
            <div class="text-[10px] text-amber-600/70 mt-0.5">Awaiting sign</div>
        </div>

        <!-- Fully Signed -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-indigo-600 dark:text-indigo-400">Fully Signed</span>
            <div class="text-xl sm:text-2xl font-black text-indigo-600 dark:text-indigo-400 mt-1">{{ $metrics['fully_signed'] }}</div>
            <div class="text-[10px] text-indigo-600/70 mt-0.5">Signed &amp; sealed</div>
        </div>

        <!-- E-Stamp Pending -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-rose-600 dark:text-rose-400">e-Stamp Pending</span>
            <div class="text-xl sm:text-2xl font-black text-rose-600 dark:text-rose-400 mt-1">{{ $metrics['estamp_pending'] }}</div>
            <div class="text-[10px] text-rose-600/70 mt-0.5">Needs certificate</div>
        </div>

        <!-- Active -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-emerald-600 dark:text-emerald-400">Active</span>
            <div class="text-xl sm:text-2xl font-black text-emerald-600 dark:text-emerald-400 mt-1">{{ $metrics['active'] }}</div>
            <div class="text-[10px] text-emerald-600/70 mt-0.5">Live contracts</div>
        </div>

        <!-- Total Value -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm flex flex-col justify-between">
            <span class="text-[11px] font-bold text-blue-600 dark:text-blue-400">Value (USD)</span>
            <div class="text-base sm:text-lg font-black text-blue-600 dark:text-blue-400 mt-1">${{ number_format($metrics['total_value']) }}</div>
            <div class="text-[10px] text-slate-400 mt-0.5">Active portfolio</div>
        </div>
    </div>

    <!-- Status Filter Tabs & Search Bar -->
    <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
        <!-- Status Tabs -->
        <div class="flex items-center gap-1 bg-white dark:bg-slate-900 p-1.5 rounded-2xl border border-slate-200/80 dark:border-slate-800 text-xs font-semibold shadow-sm overflow-x-auto">
            <a href="{{ route('admin.agreements.index') }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ !request('status') || request('status') === 'all' ? 'bg-slate-900 text-white dark:bg-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                All ({{ $metrics['total'] }})
            </a>
            <a href="{{ route('admin.agreements.index', ['status' => 'draft']) }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ request('status') === 'draft' ? 'bg-slate-900 text-white dark:bg-blue-600' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Draft ({{ $metrics['draft'] }})
            </a>
            <a href="{{ route('admin.agreements.index', ['status' => 'pending_signature']) }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ request('status') === 'pending_signature' ? 'bg-amber-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Pending Sign ({{ $metrics['pending_signature'] }})
            </a>
            <a href="{{ route('admin.agreements.index', ['status' => 'fully_signed']) }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ request('status') === 'fully_signed' ? 'bg-indigo-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Signed ({{ $metrics['fully_signed'] }})
            </a>
            <a href="{{ route('admin.agreements.index', ['status' => 'estamp_pending']) }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ request('status') === 'estamp_pending' ? 'bg-rose-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                e-Stamp Pending ({{ $metrics['estamp_pending'] }})
            </a>
            <a href="{{ route('admin.agreements.index', ['status' => 'active']) }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ request('status') === 'active' ? 'bg-emerald-600 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Active ({{ $metrics['active'] }})
            </a>
            <a href="{{ route('admin.agreements.index', ['status' => 'cancelled']) }}" 
               class="px-3 py-1.5 rounded-xl transition whitespace-nowrap {{ request('status') === 'cancelled' ? 'bg-rose-700 text-white' : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-800' }}">
                Cancelled ({{ $metrics['cancelled'] }})
            </a>
        </div>

        <!-- Search Form -->
        <form action="{{ route('admin.agreements.index') }}" method="GET" class="relative max-w-xs w-full">
            @if(request('status'))
                <input type="hidden" name="status" value="{{ request('status') }}">
            @endif
            <input type="text" name="search" value="{{ request('search') }}" placeholder="Search agreement #, title, client..."
                   class="w-full pl-9 pr-4 py-2 bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-xl text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:ring-2 focus:ring-blue-600 shadow-sm">
            <div class="absolute left-3 top-2.5 text-slate-400">
                <x-icon name="search" class="w-3.5 h-3.5" />
            </div>
        </form>
    </div>

    <!-- Agreements Table (Executive Clean Design) -->
    <div class="bg-white dark:bg-slate-900 rounded-3xl border border-slate-200/80 dark:border-slate-800 shadow-sm overflow-hidden">
        <div class="overflow-x-auto">
            <table class="w-full text-left text-xs text-slate-600 dark:text-slate-300 min-w-[1040px]">
                <thead class="bg-slate-50/80 dark:bg-slate-800/60 text-slate-400 dark:text-slate-400 font-bold uppercase tracking-wider text-[10px] border-b border-slate-100 dark:border-slate-800">
                    <tr>
                        <th class="py-3.5 px-5 w-44">Agreement Number</th>
                        <th class="py-3.5 px-4 min-w-[220px]">Agreement Title &amp; Category</th>
                        <th class="py-3.5 px-4 min-w-[200px]">Client &amp; Company</th>
                        <th class="py-3.5 px-4 w-32">Contract Value</th>
                        <th class="py-3.5 px-4 w-40">Status</th>
                        <th class="py-3.5 px-4 w-36">Digital Seal / e-Stamp</th>
                        <th class="py-3.5 px-5 w-32 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-100 dark:divide-slate-800/80 font-medium">
                    @forelse($agreements as $agr)
                        <tr class="hover:bg-slate-50/60 dark:hover:bg-slate-800/40 transition">
                            <!-- Agreement Number -->
                            <td class="py-4 px-5">
                                <a href="{{ route('admin.agreements.show', $agr->id) }}" 
                                   class="font-mono font-bold text-xs text-blue-600 dark:text-blue-400 hover:text-blue-500 transition inline-flex items-center gap-1 group whitespace-nowrap">
                                    <span>{{ $agr->agreement_number }}</span>
                                    <svg class="w-3 h-3 opacity-0 group-hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m7 17 10-10M7 7h10v10"/></svg>
                                </a>
                                <div class="text-[11px] text-slate-400 mt-0.5 whitespace-nowrap">
                                    {{ $agr->created_at ? $agr->created_at->format('M d, Y') : '—' }}
                                </div>
                            </td>

                            <!-- Agreement Title & Category -->
                            <td class="py-4 px-4">
                                <div class="font-bold text-slate-900 dark:text-white text-xs truncate max-w-[220px]" title="{{ $agr->title }}">
                                    {{ $agr->title }}
                                </div>
                                <div class="mt-1">
                                    <span class="inline-flex items-center px-2 py-0.5 rounded-md text-[10px] font-medium bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200/50 dark:border-slate-700/60 whitespace-nowrap">
                                        {{ $agr->template?->category ?: 'Bespoke Software' }}
                                    </span>
                                </div>
                            </td>

                            <!-- Client & Company -->
                            <td class="py-4 px-4">
                                <div class="flex items-center gap-2.5">
                                    <div class="w-8 h-8 rounded-xl bg-gradient-to-br from-blue-500/10 to-indigo-500/20 text-blue-600 dark:text-blue-400 font-black text-xs flex items-center justify-center shrink-0 border border-blue-500/20">
                                        {{ strtoupper(substr($agr->client_company ?: ($agr->client_name ?: 'C'), 0, 1)) }}
                                    </div>
                                    <div class="min-w-0">
                                        <div class="font-bold text-slate-900 dark:text-white text-xs truncate max-w-[170px]" title="{{ $agr->client_company ?: $agr->client_name }}">
                                            {{ $agr->client_company ?: $agr->client_name }}
                                        </div>
                                        <div class="text-[11px] text-slate-400 dark:text-slate-500 truncate max-w-[170px]" title="{{ $agr->client_email }}">
                                            {{ $agr->client_email }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            <!-- Contract Value -->
                            <td class="py-4 px-4">
                                <div class="font-mono font-bold text-slate-900 dark:text-white text-sm tracking-tight whitespace-nowrap">
                                    {{ $agr->getFormattedValue() }}
                                </div>
                                <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider">
                                    {{ $agr->currency }}
                                </div>
                            </td>

                            <!-- Status Badge (No Wrapping, Clean Indicator Dot) -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                @if($agr->status === 'active')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-emerald-500 animate-pulse"></span>
                                        <span>Active</span>
                                    </span>
                                @elseif($agr->status === 'fully_signed')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-indigo-500/10 text-indigo-600 dark:text-indigo-400 border border-indigo-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-indigo-500"></span>
                                        <span>Fully Signed</span>
                                    </span>
                                @elseif($agr->status === 'pending_signature')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-amber-500"></span>
                                        <span>Pending Sign</span>
                                    </span>
                                @elseif($agr->status === 'estamp_pending')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-purple-500/10 text-purple-600 dark:text-purple-400 border border-purple-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-purple-500"></span>
                                        <span>e-Stamp Pending</span>
                                    </span>
                                @elseif($agr->status === 'cancelled')
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-rose-500/10 text-rose-600 dark:text-rose-400 border border-rose-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-rose-500"></span>
                                        <span>Cancelled</span>
                                    </span>
                                @else
                                    <span class="inline-flex items-center gap-1.5 px-3 py-1 rounded-full text-xs font-semibold bg-slate-500/10 text-slate-600 dark:text-slate-400 border border-slate-500/20">
                                        <span class="w-1.5 h-1.5 rounded-full bg-slate-400"></span>
                                        <span>Draft</span>
                                    </span>
                                @endif
                            </td>

                            <!-- Digital Seal / e-Stamp -->
                            <td class="py-4 px-4 whitespace-nowrap">
                                <div class="flex flex-col gap-1 items-start">
                                    @if($agr->hasDigitalSignature())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-semibold bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border border-emerald-500/20">
                                            <svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" stroke-width="2.5" viewBox="0 0 24 24"><path d="m5 13 4 4L19 7"/></svg>
                                            <span>Signed</span>
                                        </span>
                                    @else
                                        <span class="text-[11px] text-slate-400 dark:text-slate-500">Unsigned</span>
                                    @endif

                                    @if($agr->hasEstamp())
                                        <span class="inline-flex items-center gap-1 px-2 py-0.5 rounded-md text-[10px] font-mono font-semibold bg-amber-500/10 text-amber-600 dark:text-amber-400 border border-amber-500/20" title="{{ $agr->estamp_number }}">
                                            <x-icon name="stamp" class="w-2.5 h-2.5" />
                                            <span>{{ substr($agr->estamp_number, 0, 11) }}...</span>
                                        </span>
                                    @endif
                                </div>
                            </td>

                            <!-- Actions -->
                            <td class="py-4 px-5 text-right whitespace-nowrap">
                                <div class="flex items-center justify-end gap-1.5">
                                    <a href="{{ route('admin.agreements.show', $agr->id) }}" 
                                       class="inline-flex items-center gap-1 px-3 py-1.5 rounded-xl bg-blue-600 hover:bg-blue-500 text-white font-bold text-xs transition shadow-sm hover:shadow"
                                       title="Manage Agreement">
                                        <span>Manage</span>
                                    </a>
                                    <a href="{{ route('admin.agreements.download', $agr->id) }}" 
                                       class="p-1.5 rounded-xl text-slate-400 hover:text-blue-600 dark:hover:text-blue-400 hover:bg-blue-50 dark:hover:bg-blue-950/50 border border-slate-200/60 dark:border-slate-800 transition" 
                                       title="Download PDF">
                                        <x-icon name="download" class="w-4 h-4" />
                                    </a>
                                    @if(Auth::user()->hasPermission('agreements.manage'))
                                        <form action="{{ route('admin.agreements.destroy', $agr->id) }}" method="POST" onsubmit="return confirm('Permanently delete agreement {{ $agr->agreement_number }}? This cannot be undone.');" class="inline">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="p-1.5 rounded-xl text-slate-400 hover:text-rose-600 hover:bg-rose-50 dark:hover:bg-rose-950/50 border border-slate-200/60 dark:border-slate-800 transition" title="Delete Agreement">
                                                <x-icon name="trash" class="w-4 h-4" />
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="py-12 text-center text-slate-400 dark:text-slate-500">
                                No agreements found matching this criteria. Click "+ Generate Agreement" to start a new client contract.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="p-4 border-t border-slate-100 dark:border-slate-800">
            {{ $agreements->links() }}
        </div>
    </div>
</div>
@endsection
