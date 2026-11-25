@php
    $siteKey = config('services.captcha.site_key', env('CAPTCHA_SITE_KEY', ''));
    $driver = strtolower(config('services.captcha.driver', env('CAPTCHA_DRIVER', 'recaptcha')));
@endphp

@if(!empty(trim($siteKey)))
    <div class="my-4">
        @if($driver === 'turnstile')
            <!-- Cloudflare Turnstile -->
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="auto"></div>
        @else
            <!-- Google reCAPTCHA v2 -->
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" data-theme="dark"></div>
        @endif

        @if(isset($errors) && $errors->has('captcha'))
            <p class="text-xs text-rose-500 font-semibold mt-1.5 flex items-center gap-1">
                <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
                <span>{{ $errors->first('captcha') }}</span>
            </p>
        @endif
    </div>
@endif

