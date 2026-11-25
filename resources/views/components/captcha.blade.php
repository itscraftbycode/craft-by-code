@php
    $siteKey = config('services.captcha.site_key', env('CAPTCHA_SITE_KEY', ''));
    $driver = strtolower(config('services.captcha.driver', env('CAPTCHA_DRIVER', 'recaptcha_v3')));
    $nonce = $cspNonce ?? null;
@endphp

@if(!empty(trim($siteKey)))
    @if($driver === 'turnstile')
        <!-- Cloudflare Turnstile -->
        <div class="my-4">
            <script src="https://challenges.cloudflare.com/turnstile/v0/api.js" async defer></script>
            <div class="cf-turnstile" data-sitekey="{{ $siteKey }}" data-theme="auto"></div>
        </div>
    @elseif($driver === 'recaptcha_v2' || $driver === 'v2')
        <!-- Google reCAPTCHA v2 Checkbox -->
        <div class="my-4">
            <script src="https://www.google.com/recaptcha/api.js" async defer></script>
            <div class="g-recaptcha" data-sitekey="{{ $siteKey }}" data-theme="dark"></div>
        </div>
    @else
        <!-- Google reCAPTCHA v3 (Invisible) -->
        <script src="https://www.google.com/recaptcha/api.js?render={{ $siteKey }}"></script>
        <input type="hidden" name="g-recaptcha-response" class="g-recaptcha-response-field">
        <script @if(!empty($nonce)) nonce="{{ $nonce }}" @endif>
            (function() {
                function fetchV3Token() {
                    if (typeof grecaptcha !== 'undefined') {
                        grecaptcha.ready(function() {
                            try {
                                grecaptcha.execute('{{ $siteKey }}', {action: 'contact_submit'}).then(function(token) {
                                    document.querySelectorAll('.g-recaptcha-response-field').forEach(function(el) {
                                        el.value = token;
                                    });
                                });
                            } catch(err) {}
                        });
                    }
                }

                if (document.readyState === 'loading') {
                    document.addEventListener('DOMContentLoaded', fetchV3Token);
                } else {
                    fetchV3Token();
                }

                document.addEventListener('submit', function(e) {
                    var form = e.target;
                    var tokenInput = form.querySelector('.g-recaptcha-response-field');
                    if (tokenInput && (!tokenInput.value || form.dataset.recaptchaSubmitting !== 'true')) {
                        if (typeof grecaptcha !== 'undefined') {
                            e.preventDefault();
                            form.dataset.recaptchaSubmitting = 'true';
                            grecaptcha.ready(function() {
                                grecaptcha.execute('{{ $siteKey }}', {action: 'contact_submit'}).then(function(token) {
                                    tokenInput.value = token;
                                    form.submit();
                                }).catch(function() {
                                    form.submit();
                                });
                            });
                        }
                    }
                }, true);
            })();
        </script>
        <div class="text-[11px] text-slate-500 dark:text-slate-400 my-2">
            Protected by reCAPTCHA &bull; <a href="https://policies.google.com/privacy" target="_blank" rel="noopener" class="underline hover:text-blue-500">Privacy</a> &bull; <a href="https://policies.google.com/terms" target="_blank" rel="noopener" class="underline hover:text-blue-500">Terms</a>
        </div>
    @endif

    @if(isset($errors) && $errors->has('captcha'))
        <p class="text-xs text-rose-500 font-semibold my-2 flex items-center gap-1">
            <svg class="w-3.5 h-3.5 flex-shrink-0" fill="none" stroke="currentColor" stroke-width="2" viewBox="0 0 24 24"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
            <span>{{ $errors->first('captcha') }}</span>
        </p>
    @endif
@endif
