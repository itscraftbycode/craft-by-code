@extends('layouts.admin')

@section('title', $agreement->agreement_number . ' - ' . $agreement->title)
@section('header_title', 'Agreement Details')

@section('content')
<div class="space-y-6">

    <!-- Header Navigation & Action Bar -->
    <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <a href="{{ route('admin.agreements.index') }}" class="text-xs font-semibold text-slate-500 hover:text-slate-800 dark:hover:text-white transition flex items-center gap-1 mb-2">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><path d="m15 18-6-6 6-6"/></svg>
                <span>Back to Agreements</span>
            </a>
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold text-slate-900 dark:text-white">{{ $agreement->title }}</h1>
                <span class="font-mono text-xs px-2.5 py-1 rounded-lg bg-slate-100 dark:bg-slate-800 text-blue-600 dark:text-blue-400 font-bold border border-slate-200 dark:border-slate-700">
                    {{ $agreement->agreement_number }}
                </span>
            </div>
        </div>

        <div class="flex flex-wrap items-center gap-2">
            <!-- Download Official PDF -->
            <a href="{{ route('admin.agreements.download', $agreement->id) }}" 
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-blue-600 hover:bg-blue-500 text-xs font-bold text-white transition shadow-sm hover:shadow">
                <x-icon name="download" class="w-3.5 h-3.5" />
                <span>Download PDF</span>
            </a>

            <!-- Stream PDF in Browser -->
            <a href="{{ route('admin.agreements.stream', $agreement->id) }}" target="_blank" rel="noopener noreferrer"
               class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                <x-icon name="eye" class="w-3.5 h-3.5" />
                <span>Preview PDF</span>
            </a>

            <!-- Edit (If allowed) -->
            @if(!in_array($agreement->status, ['completed', 'cancelled']))
                <a href="{{ route('admin.agreements.edit', $agreement->id) }}" 
                   class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-bold text-slate-700 dark:text-slate-200 transition">
                    <x-icon name="edit" class="w-3.5 h-3.5" />
                    <span>Edit</span>
                </a>
            @endif

            <!-- Delete Agreement -->
            @if(Auth::user()->hasPermission('agreements.manage'))
                <form action="{{ route('admin.agreements.destroy', $agreement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete agreement {{ $agreement->agreement_number }}? This action cannot be undone.');" class="inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" 
                            class="inline-flex items-center gap-1.5 px-3.5 py-2 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white transition shadow-sm hover:shadow" 
                            title="Delete Agreement">
                        <x-icon name="trash" class="w-3.5 h-3.5" />
                        <span>Delete</span>
                    </button>
                </form>
            @endif
        </div>
    </div>

    <!-- Main Content Layout (8 Cols Details / 4 Cols Legal Action Center) -->
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- Left 8 Cols: Legal Terms, Parties, Scope, Signatures -->
        <div class="lg:col-span-8 space-y-6">

            <!-- Status Banner & Summary Strip -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-5">
                <div class="flex flex-wrap items-center justify-between gap-3 border-b border-slate-100 dark:border-slate-800 pb-4">
                    <div>
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Lifecycle Status</span>
                        <div class="mt-1">
                            @if($agreement->status === 'active')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                                    ● Active Enforceable Contract
                                </span>
                            @elseif($agreement->status === 'fully_signed')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-indigo-50 dark:bg-indigo-950/60 text-indigo-600 dark:text-indigo-400 border border-indigo-200 dark:border-indigo-800">
                                    ● Fully Signed &amp; Sealed
                                </span>
                            @elseif($agreement->status === 'pending_signature')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                                    ● Pending Digital Signature
                                </span>
                            @elseif($agreement->status === 'estamp_pending')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-rose-50 dark:bg-rose-950/60 text-rose-600 dark:text-rose-400 border border-rose-200 dark:border-rose-800">
                                    ● e-Stamp Certificate Required
                                </span>
                            @elseif($agreement->status === 'cancelled')
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-500 border border-slate-200 dark:border-slate-700">
                                    ✕ Cancelled
                                </span>
                            @else
                                <span class="px-3 py-1 rounded-full text-xs font-bold bg-slate-100 dark:bg-slate-800 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-700">
                                    Draft Document
                                </span>
                            @endif
                        </div>
                    </div>

                    <div class="text-right">
                        <span class="text-[10px] font-bold text-slate-400 uppercase tracking-wider block">Contract Value</span>
                        <span class="text-2xl font-black text-slate-900 dark:text-white">
                            {{ $agreement->getFormattedValue() }}
                        </span>
                        <span class="text-xs text-slate-400 font-semibold ml-1">{{ $agreement->currency }}</span>
                    </div>
                </div>

                <!-- Parties Box -->
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 p-4 rounded-2xl bg-slate-50 dark:bg-slate-950/50 border border-slate-100 dark:border-slate-800 text-xs">
                    <div>
                        <span class="text-[10px] font-bold text-blue-600 dark:text-blue-400 uppercase tracking-wider block mb-1">Service Provider</span>
                        <div class="font-bold text-slate-900 dark:text-white">CraftByCode Technologies</div>
                        <div class="text-slate-400 text-[11px] mt-0.5">legal@craftbycode.tech</div>
                        <div class="text-slate-400 text-[11px]">100 Innovation Way, Suite 400, San Francisco, CA</div>
                    </div>
                    <div>
                        <span class="text-[10px] font-bold text-indigo-600 dark:text-indigo-400 uppercase tracking-wider block mb-1">Client Entity</span>
                        <div class="font-bold text-slate-900 dark:text-white">{{ $agreement->client_company ?: $agreement->client_name }}</div>
                        <div class="text-slate-400 text-[11px] mt-0.5">Rep: {{ $agreement->client_name }} ({{ $agreement->client_email }})</div>
                        @if($agreement->client_phone)<div class="text-slate-400 text-[11px]">Phone: {{ $agreement->client_phone }}</div>@endif
                        @if($agreement->client_address)<div class="text-slate-400 text-[11px]">{{ $agreement->client_address }}</div>@endif
                    </div>
                </div>

                <!-- Commercial Key Metrics Strip -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs pt-1">
                    <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-semibold">Start Date</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">
                            {{ $agreement->start_date ? \Carbon\Carbon::parse($agreement->start_date)->format('M d, Y') : 'Immediate' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-semibold">End Date</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">
                            {{ $agreement->end_date ? \Carbon\Carbon::parse($agreement->end_date)->format('M d, Y') : 'Per Milestones' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-semibold">Created By</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block truncate">
                            {{ $agreement->creator?->name ?: 'Admin' }}
                        </span>
                    </div>
                    <div class="p-3 rounded-xl bg-slate-50/70 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800">
                        <span class="text-[10px] text-slate-400 block font-semibold">PDF Compiled</span>
                        <span class="font-bold text-slate-800 dark:text-slate-200 mt-0.5 block">
                            {{ $agreement->pdf_generated_at ? \Carbon\Carbon::parse($agreement->pdf_generated_at)->diffForHumans() : 'Pending' }}
                        </span>
                    </div>
                </div>
            </div>

            <!-- Scope of Work Schedule -->
            @if($agreement->scope_of_work)
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Schedule A — Scope of Work &amp; Specifications</h3>
                    <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                        {{ $agreement->scope_of_work }}
                    </div>
                </div>
            @endif

            <!-- Payment Terms & Milestones -->
            @if($agreement->payment_terms)
                <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Schedule B — Consideration &amp; Payment Milestones</h3>
                    <div class="p-4 rounded-2xl bg-slate-50/70 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 text-xs text-slate-700 dark:text-slate-300 whitespace-pre-line leading-relaxed">
                        {{ $agreement->payment_terms }}
                    </div>
                </div>
            @endif

            <!-- Agreement Terms & Legal Clauses -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-3">Schedule C — Legal Covenants &amp; Terms</h3>
                <div class="p-5 rounded-2xl bg-slate-50/60 dark:bg-slate-950/60 border border-slate-100 dark:border-slate-800 font-mono text-xs text-slate-800 dark:text-slate-200 whitespace-pre-line leading-relaxed max-h-96 overflow-y-auto">
                    {{ $agreement->content }}
                </div>
            </div>

            <!-- Digital Signature Verification Block -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400">Cryptographic Digital Signature Verification</h3>
                    @if($agreement->hasDigitalSignature())
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-emerald-50 dark:bg-emerald-950/60 text-emerald-600 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-800">
                            ✓ Verified &amp; Sealed
                        </span>
                    @else
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-50 dark:bg-amber-950/60 text-amber-600 dark:text-amber-400 border border-amber-200 dark:border-amber-800">
                            Unsigned
                        </span>
                    @endif
                </div>

                @if($agreement->hasDigitalSignature())
                    <div class="p-4 rounded-2xl bg-emerald-50/40 dark:bg-emerald-950/20 border border-emerald-200/60 dark:border-emerald-900/40 text-xs space-y-2">
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                            <div>
                                <span class="text-[10px] text-slate-400 block">Signatory:</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ is_array($agreement->digital_signature_metadata) ? ($agreement->digital_signature_metadata['signed_by'] ?? 'Admin Craft By Code') : 'Admin Craft By Code' }}</span>
                                <span class="text-[11px] text-slate-500 block">({{ is_array($agreement->digital_signature_metadata) ? ($agreement->digital_signature_metadata['signatory_role'] ?? 'Authorized Officer') : 'Authorized Officer' }})</span>
                            </div>
                            <div>
                                <span class="text-[10px] text-slate-400 block">Timestamp:</span>
                                <span class="font-bold text-slate-900 dark:text-white">{{ $agreement->signed_at ? \Carbon\Carbon::parse($agreement->signed_at)->format('Y-m-d H:i:s T') : 'Verified' }}</span>
                                <span class="text-[11px] text-slate-500 block">IP: {{ is_array($agreement->digital_signature_metadata) ? ($agreement->digital_signature_metadata['signer_ip'] ?? '127.0.0.1') : '127.0.0.1' }}</span>
                            </div>
                        </div>
                        <div class="pt-2 border-t border-emerald-100 dark:border-emerald-900/50">
                            <span class="text-[10px] text-slate-400 block">HMAC-SHA256 Digital Fingerprint:</span>
                            <span class="font-mono text-[11px] text-emerald-700 dark:text-emerald-400 break-all select-all font-bold">
                                {{ $agreement->digital_signature_hash }}
                            </span>
                        </div>
                    </div>
                @else
                    <p class="text-xs text-slate-500 dark:text-slate-400">
                        This agreement has not yet been sealed with a cryptographic digital signature. Authorized Admins can apply a digital seal using the Action Center on the right.
                    </p>
                @endif
            </div>

            <!-- e-Stamp Certificate Endorsement Block -->
            @if($agreement->hasEstamp())
                <div class="p-6 rounded-3xl bg-amber-50/50 dark:bg-amber-950/20 border border-amber-200/80 dark:border-amber-900/50 shadow-sm">
                    <div class="flex items-center justify-between mb-3">
                        <div class="flex items-center gap-2">
                            <x-icon name="stamp" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                            <h3 class="text-xs font-bold uppercase tracking-wider text-amber-900 dark:text-amber-300">Official e-Stamp Certificate Endorsement</h3>
                        </div>
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold bg-amber-100 dark:bg-amber-900/50 text-amber-800 dark:text-amber-300">
                            Legally Endorsed
                        </span>
                    </div>

                    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 text-xs">
                        <div>
                            <span class="text-[10px] text-amber-700 dark:text-amber-400 block">e-Stamp Number:</span>
                            <span class="font-mono font-bold text-slate-900 dark:text-white">{{ $agreement->estamp_number }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-700 dark:text-amber-400 block">Jurisdiction:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $agreement->estamp_state ?? 'Delaware' }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-700 dark:text-amber-400 block">Duty Paid:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $agreement->currency }} {{ number_format((float) $agreement->estamp_amount, 2) }}</span>
                        </div>
                        <div>
                            <span class="text-[10px] text-amber-700 dark:text-amber-400 block">Issued On:</span>
                            <span class="font-bold text-slate-900 dark:text-white">{{ $agreement->estamp_issued_at ? \Carbon\Carbon::parse($agreement->estamp_issued_at)->format('M d, Y') : 'Verified' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Chronological Agreement Audit Trail -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm">
                <h3 class="text-xs font-bold uppercase tracking-wider text-slate-400 mb-4">Security &amp; Legal Audit Trail</h3>

                <div class="space-y-3">
                    @forelse($agreement->auditLogs as $log)
                        <div class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950/40 border border-slate-100 dark:border-slate-800 text-xs flex items-start justify-between gap-4">
                            <div class="space-y-1">
                                <div class="flex items-center gap-2">
                                    <span class="px-2 py-0.5 rounded text-[10px] font-bold uppercase tracking-wider bg-slate-200 dark:bg-slate-800 text-slate-700 dark:text-slate-300 font-mono">
                                        {{ str_replace('_', ' ', $log->action) }}
                                    </span>
                                    <span class="font-bold text-slate-800 dark:text-slate-200">{{ $log->user?->name ?: 'System' }}</span>
                                    <span class="text-[10px] text-slate-400">({{ $log->ip_address ?? '127.0.0.1' }})</span>
                                </div>
                                <p class="text-slate-600 dark:text-slate-400 text-[11px]">{{ $log->details }}</p>
                            </div>
                            <span class="text-[10px] text-slate-400 whitespace-nowrap">
                                {{ $log->created_at ? \Carbon\Carbon::parse($log->created_at)->format('M d, Y H:i') : '' }}
                            </span>
                        </div>
                    @empty
                        <p class="text-xs text-slate-400">No audit events recorded yet.</p>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Right 4 Cols: Legal Action Center (Strict Admin-Only) -->
        <div class="lg:col-span-4 space-y-6">

            <!-- 1. Digital Signature Action Box -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <x-icon name="check-circle" class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">Admin Digital Signature</h3>
                </div>

                @if($agreement->hasDigitalSignature())
                    <div class="p-3 rounded-xl bg-emerald-50 dark:bg-emerald-950/40 text-emerald-800 dark:text-emerald-200 text-xs font-semibold">
                        ✓ Digital signature successfully applied by {{ $agreement->digital_signature_metadata['signed_by'] ?? 'Admin' }}.
                    </div>
                @endif

                <form action="{{ route('admin.agreements.sign', $agreement->id) }}" method="POST" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Signatory Title / Designation *</label>
                        <input type="text" name="signatory_role" required value="{{ old('signatory_role', 'Managing Director, CraftByCode') }}"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Internal Signature Note (Optional)</label>
                        <input type="text" name="notes" placeholder="e.g. Approved following legal review"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm hover:shadow">
                        {{ $agreement->hasDigitalSignature() ? 'Re-apply Digital Signature' : 'Apply Cryptographic Signature' }}
                    </button>
                </form>
            </div>

            <!-- 2. e-Stamp Certificate Endorsement Box -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <div class="flex items-center gap-2 border-b border-slate-100 dark:border-slate-800 pb-3">
                    <x-icon name="stamp" class="w-4 h-4 text-amber-600 dark:text-amber-400" />
                    <h3 class="text-sm font-bold text-slate-900 dark:text-white">e-Stamp Duty Certificate</h3>
                </div>

                <form action="{{ route('admin.agreements.estamp', $agreement->id) }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                    @csrf
                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">e-Stamp Number / GRN *</label>
                        <input type="text" name="estamp_number" required value="{{ old('estamp_number', $agreement->estamp_number) }}" placeholder="e.g. IN-DL982348712903H"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Jurisdiction *</label>
                            <input type="text" name="estamp_state" required value="{{ old('estamp_state', $agreement->estamp_state ?? 'Delaware') }}" placeholder="Delaware"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>
                        <div>
                            <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Duty Amount *</label>
                            <input type="number" step="0.01" min="0" name="estamp_amount" required value="{{ old('estamp_amount', $agreement->estamp_amount ?? '500.00') }}"
                                   class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                        </div>
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Date of Issue *</label>
                        <input type="date" name="estamp_issued_at" required value="{{ old('estamp_issued_at', $agreement->estamp_issued_at ? $agreement->estamp_issued_at->format('Y-m-d') : date('Y-m-d')) }}"
                               class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                    </div>

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Attach Certificate Document (Optional)</label>
                        <input type="file" name="certificate_file" accept=".pdf,.png,.jpg,.jpeg"
                               class="w-full px-3 py-1.5 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-500 focus:outline-none">
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl bg-amber-600 hover:bg-amber-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm hover:shadow">
                        {{ $agreement->hasEstamp() ? 'Update e-Stamp Endorsement' : 'Endorse e-Stamp Certificate' }}
                    </button>
                </form>
            </div>

            <!-- 3. Lifecycle Status Workflow Box -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">Lifecycle State Transitions</h3>

                <form action="{{ route('admin.agreements.status', $agreement->id) }}" method="POST" class="space-y-3">
                    @csrf
                    @method('PUT')

                    <div>
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Transition Status To</label>
                        <select name="status" id="status-select" class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs font-semibold text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">
                            <option value="draft" {{ $agreement->status === 'draft' ? 'selected' : '' }}>Draft (In Preparation)</option>
                            <option value="pending_signature" {{ $agreement->status === 'pending_signature' ? 'selected' : '' }}>Pending Signature</option>
                            <option value="fully_signed" {{ $agreement->status === 'fully_signed' ? 'selected' : '' }}>Fully Signed &amp; Sealed</option>
                            <option value="estamp_pending" {{ $agreement->status === 'estamp_pending' ? 'selected' : '' }}>e-Stamp Pending</option>
                            <option value="active" {{ $agreement->status === 'active' ? 'selected' : '' }}>Active (Enforceable)</option>
                            <option value="completed" {{ $agreement->status === 'completed' ? 'selected' : '' }}>Completed (Fulfilled)</option>
                            <option value="cancelled" {{ $agreement->status === 'cancelled' ? 'selected' : '' }}>Cancelled / Revoked</option>
                        </select>
                    </div>

                    <div id="cancellation-reason-box" class="{{ $agreement->status === 'cancelled' ? '' : 'hidden' }}">
                        <label class="block text-[11px] font-semibold text-slate-700 dark:text-slate-300 mb-1">Reason for Cancellation</label>
                        <textarea name="cancellation_reason" rows="2" placeholder="Explain revocation reason..."
                                  class="w-full px-3 py-2 bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-700 rounded-xl text-xs text-slate-900 dark:text-white focus:outline-none focus:ring-2 focus:ring-blue-600">{{ old('cancellation_reason', $agreement->cancellation_reason) }}</textarea>
                    </div>

                    <button type="submit" 
                            class="w-full py-2.5 px-4 rounded-xl bg-slate-900 dark:bg-blue-600 hover:bg-blue-600 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm">
                        Apply Status Transition
                    </button>
                </form>
            </div>

            <!-- 4. PDF Compilation & Integrity Fingerprint Box -->
            <div class="p-6 rounded-3xl bg-white dark:bg-slate-900 border border-slate-200/80 dark:border-slate-800 shadow-sm space-y-4">
                <h3 class="text-sm font-bold text-slate-900 dark:text-white border-b border-slate-100 dark:border-slate-800 pb-3">PDF Engine &amp; Security</h3>

                <div class="text-xs space-y-2 text-slate-600 dark:text-slate-400">
                    <div>
                        <span class="text-[10px] text-slate-400 block font-semibold">Storage Location:</span>
                        <span class="font-mono text-[10px] text-slate-500 dark:text-slate-400">storage/app/private/agreements/</span>
                    </div>
                    @if($agreement->pdf_hash)
                        <div>
                            <span class="text-[10px] text-slate-400 block font-semibold">SHA-256 Checksum:</span>
                            <span class="font-mono text-[10px] text-blue-600 dark:text-blue-400 break-all select-all font-bold">
                                {{ $agreement->pdf_hash }}
                            </span>
                        </div>
                    @endif
                </div>

                <form action="{{ route('admin.agreements.pdf', $agreement->id) }}" method="POST">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2 px-3 rounded-xl border border-slate-200 dark:border-slate-700 hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-700 dark:text-slate-300 font-bold text-xs transition">
                        Recompile PDF Document
                    </button>
                </form>
            </div>

            <!-- 5. Danger Zone / Delete Agreement -->
            @if(Auth::user()->hasPermission('agreements.manage'))
                <div class="p-6 rounded-3xl bg-rose-50/40 dark:bg-rose-950/20 border border-rose-200/80 dark:border-rose-900/50 shadow-sm space-y-3">
                    <h3 class="text-xs font-bold uppercase tracking-wider text-rose-600 dark:text-rose-400">Danger Zone</h3>
                    <p class="text-[11px] text-slate-500 dark:text-slate-400">
                        Permanently delete this contract, its audit trail, and stored PDF files from the database.
                    </p>
                    <form action="{{ route('admin.agreements.destroy', $agreement->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to permanently delete agreement {{ $agreement->agreement_number }}? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full py-2.5 px-4 rounded-xl bg-rose-600 hover:bg-rose-500 text-white font-bold text-xs uppercase tracking-wider transition shadow-sm hover:shadow flex items-center justify-center gap-1.5">
                            <x-icon name="trash" class="w-3.5 h-3.5" />
                            <span>Delete Agreement Permanently</span>
                        </button>
                    </form>
                </div>
            @endif

        </div>
    </div>
</div>

@push('scripts')
<script nonce="{{ $cspNonce ?? '' }}">
    document.addEventListener('DOMContentLoaded', function () {
        const statusSelect = document.getElementById('status-select');
        const reasonBox = document.getElementById('cancellation-reason-box');

        if (statusSelect && reasonBox) {
            statusSelect.addEventListener('change', function () {
                if (this.value === 'cancelled') {
                    reasonBox.classList.remove('hidden');
                } else {
                    reasonBox.classList.add('hidden');
                }
            });
        }
    });
</script>
@endpush
@endsection

