<?php

use App\Http\Controllers\Admin\AboutContentController;
use App\Http\Controllers\Admin\AgreementController;
use App\Http\Controllers\Admin\AgreementTemplateController;
use App\Http\Controllers\Admin\AuthController as AdminAuthController;
use App\Http\Controllers\Admin\BlogPostController;
use App\Http\Controllers\Admin\DashboardController;
use App\Http\Controllers\Admin\InquiryController;
use App\Http\Controllers\Admin\ProfileController;
use App\Http\Controllers\Admin\ProjectController;
use App\Http\Controllers\Admin\SecurityCenterController;
use App\Http\Controllers\Admin\SeoController;
use App\Http\Controllers\Admin\ServiceController;
use App\Http\Controllers\Admin\TestimonialController;
use App\Http\Controllers\Admin\TwoFactorController;
use App\Http\Controllers\Admin\WebsiteSettingController;
use App\Http\Controllers\ChatbotController;
use App\Http\Controllers\FrontendController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\MediaController;
use App\Http\Controllers\SeoPublicController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Public Frontend & Secure Media Routes
|--------------------------------------------------------------------------
*/
Route::get('/', [FrontendController::class, 'index'])->name('home');
Route::get('/about', [FrontendController::class, 'about'])->name('about');
Route::get('/services', [FrontendController::class, 'services'])->name('services.index');
Route::get('/services/{slug}', [FrontendController::class, 'serviceDetail'])->name('services.show');
Route::get('/portfolio', [FrontendController::class, 'portfolio'])->name('portfolio.index');
Route::get('/portfolio/{slug}', [FrontendController::class, 'projectDetail'])->name('portfolio.show');
Route::get('/blog', [FrontendController::class, 'blog'])->name('blog.index');
Route::get('/blog/{slug}', [FrontendController::class, 'blogDetail'])->name('blog.show');
Route::get('/contact', [FrontendController::class, 'contact'])->name('contact');

// Public Legal & Compliance Pages
Route::get('/privacy-policy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/terms-and-conditions', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/data-security-policy', [LegalController::class, 'dataSecurity'])->name('legal.security');
Route::get('/data-retention-policy', [LegalController::class, 'dataRetention'])->name('legal.retention');
Route::get('/grievance-officer', [LegalController::class, 'grievance'])->name('legal.grievance');

// Aliases for common URL formats
Route::permanentRedirect('/privacy', '/privacy-policy');
Route::permanentRedirect('/terms', '/terms-and-conditions');
Route::permanentRedirect('/security', '/data-security-policy');
Route::permanentRedirect('/data-security', '/data-security-policy');
Route::permanentRedirect('/data-deletion-policy', '/data-retention-policy');
Route::post('/contact', [FrontendController::class, 'submitInquiry'])
    ->middleware('throttle:contact')
    ->name('contact.submit');
Route::post('/newsletter', [FrontendController::class, 'newsletter'])
    ->middleware('throttle:newsletter')
    ->name('newsletter.subscribe');
Route::post('/chatbot/message', [ChatbotController::class, 'message'])
    ->middleware('throttle:chatbot')
    ->name('chatbot.message');
Route::post('/chatbot/lead', [ChatbotController::class, 'lead'])
    ->middleware('throttle:contact')
    ->name('chatbot.lead');
Route::get('/sitemap.xml', [SeoPublicController::class, 'sitemap'])->name('sitemap');

// Non-executable uploaded media proxy stored outside public/
Route::get('/media/{category}/{filename}', [MediaController::class, 'show'])
    ->where([
        'category' => 'branding|projects|testimonials|blog|seo|avatars',
        'filename' => '[a-f0-9\-]{36}\.(jpg|jpeg|png|webp)',
    ])
    ->name('media.show');

/*
|--------------------------------------------------------------------------
| Admin Authentication & Two-Factor Challenge Routes
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->group(function () {
    Route::get('/login', [AdminAuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AdminAuthController::class, 'login'])
        ->middleware('throttle:login')
        ->name('login.submit');
    Route::post('/logout', [AdminAuthController::class, 'logout'])->name('logout');

    Route::get('/two-factor-challenge', [TwoFactorController::class, 'challengeForm'])->name('two-factor.challenge');
    Route::post('/two-factor-challenge', [TwoFactorController::class, 'verifyChallenge'])
        ->middleware('throttle:two-factor')
        ->name('two-factor.verify');

    Route::get('/forgot-password', [AdminAuthController::class, 'showForgotPassword'])->name('password.request');
    Route::post('/forgot-password', [AdminAuthController::class, 'sendResetLink'])
        ->middleware('throttle:password-reset')
        ->name('password.email');
    Route::get('/reset-password/{token}', [AdminAuthController::class, 'showResetPasswordForm'])->name('password.reset');
    Route::post('/reset-password', [AdminAuthController::class, 'resetPassword'])
        ->middleware('throttle:password-reset')
        ->name('password.update');
});

/*
|--------------------------------------------------------------------------
| Admin Protected Back-Office Routes (RBAC + Security Hardened)
|--------------------------------------------------------------------------
*/
Route::prefix('admin')->name('admin.')->middleware(['admin'])->group(function () {
    Route::get('/', function () {
        return redirect()->route('admin.dashboard');
    });
    Route::get('/dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:dashboard.view')
        ->name('dashboard');

    // Modules CRUD (requires content.manage)
    Route::middleware('permission:content.manage')->group(function () {
        Route::resource('services', ServiceController::class);
        Route::resource('projects', ProjectController::class);
        Route::resource('testimonials', TestimonialController::class);
        Route::resource('blog', BlogPostController::class)->parameters(['blog' => 'post']);

        Route::get('/about-content', [AboutContentController::class, 'index'])->name('about.index');
        Route::get('/about-content/{about}/edit', [AboutContentController::class, 'edit'])->name('about.edit');
        Route::put('/about-content/{about}', [AboutContentController::class, 'update'])->name('about.update');
    });

    // Inquiries Management (RBAC split view vs manage)
    Route::get('/inquiries', [InquiryController::class, 'index'])
        ->middleware('permission:inquiries.view')
        ->name('inquiries.index');
    Route::get('/inquiries/{inquiry}', [InquiryController::class, 'show'])
        ->middleware('permission:inquiries.view')
        ->name('inquiries.show');
    Route::middleware('permission:inquiries.manage')->group(function () {
        Route::put('/inquiries/{inquiry}/status', [InquiryController::class, 'updateStatus'])->name('inquiries.status');
        Route::delete('/inquiries/{inquiry}', [InquiryController::class, 'destroy'])->name('inquiries.destroy');
    });

    // Strict Admin-Only Digital Agreement & Contract Management System
    Route::middleware('permission:agreements.view')->group(function () {
        Route::get('/agreements', [AgreementController::class, 'index'])->name('agreements.index');
        Route::get('/agreements/archive', [AgreementController::class, 'archive'])->name('agreements.archive');
        Route::get('/agreements/audit-logs', [AgreementController::class, 'auditLogs'])->name('agreements.audit-logs');
        Route::get('/agreements/settings', [AgreementController::class, 'settings'])->name('agreements.settings');
        Route::get('/agreements/templates/list', [AgreementTemplateController::class, 'index'])->name('agreements.templates.index');

        Route::get('/agreements/{agreement}', [AgreementController::class, 'show'])->whereNumber('agreement')->name('agreements.show');
        Route::get('/agreements/{agreement}/stream', [AgreementController::class, 'streamPdf'])->whereNumber('agreement')->name('agreements.stream');
        Route::get('/agreements/{agreement}/download', [AgreementController::class, 'downloadPdf'])->whereNumber('agreement')->name('agreements.download');
    });

    Route::middleware('permission:agreements.manage')->group(function () {
        Route::get('/agreements/create', [AgreementController::class, 'create']);
        Route::get('/agreements/create/new', [AgreementController::class, 'create'])->name('agreements.create');
        Route::post('/agreements', [AgreementController::class, 'store'])->name('agreements.store');
        Route::get('/agreements/{agreement}/edit', [AgreementController::class, 'edit'])->whereNumber('agreement')->name('agreements.edit');
        Route::put('/agreements/{agreement}', [AgreementController::class, 'update'])->whereNumber('agreement')->name('agreements.update');
        Route::delete('/agreements/{agreement}', [AgreementController::class, 'destroy'])->whereNumber('agreement')->name('agreements.destroy');
        Route::post('/agreements/clear-dummy', [AgreementController::class, 'clearDummyAgreements'])->name('agreements.clear-dummy');

        Route::post('/agreements/{agreement}/pdf', [AgreementController::class, 'generatePdf'])->name('agreements.pdf');
        Route::post('/agreements/{agreement}/sign', [AgreementController::class, 'applyDigitalSignature'])->name('agreements.sign');
        Route::post('/agreements/{agreement}/estamp', [AgreementController::class, 'attachEstamp'])->name('agreements.estamp');
        Route::put('/agreements/{agreement}/status', [AgreementController::class, 'updateStatus'])->name('agreements.status');
        Route::post('/agreements/settings', [AgreementController::class, 'updateSettings'])->name('agreements.settings.update');

        // Agreement Templates (Manage)
        Route::get('/agreements/templates/create', [AgreementTemplateController::class, 'create'])->name('agreements.templates.create');
        Route::post('/agreements/templates', [AgreementTemplateController::class, 'store'])->name('agreements.templates.store');
        Route::get('/agreements/templates/{template}/edit', [AgreementTemplateController::class, 'edit'])->name('agreements.templates.edit');
        Route::put('/agreements/templates/{template}', [AgreementTemplateController::class, 'update'])->name('agreements.templates.update');
        Route::delete('/agreements/templates/{template}', [AgreementTemplateController::class, 'destroy'])->name('agreements.templates.destroy');
    });

    // Website Content, Settings & SEO (requires settings.manage)
    Route::middleware('permission:settings.manage')->group(function () {
        Route::get('/settings', [WebsiteSettingController::class, 'index'])->name('settings.index');
        Route::post('/settings', [WebsiteSettingController::class, 'update'])->name('settings.update');
        Route::post('/settings/reset-logo', [WebsiteSettingController::class, 'resetLogo'])->name('settings.reset-logo');

        Route::get('/seo', [SeoController::class, 'index'])->name('seo.index');
        Route::post('/seo', [SeoController::class, 'update'])->name('seo.update');
    });

    // Profile, Password Change & Two-Factor Authentication
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile.index');
    Route::put('/profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
    Route::put('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password');
    Route::post('/profile/two-factor/setup', [TwoFactorController::class, 'initiateSetup'])->name('profile.two-factor.setup');
    Route::post('/profile/two-factor/confirm', [TwoFactorController::class, 'confirmSetup'])->name('profile.two-factor.confirm');
    Route::post('/profile/two-factor/recovery', [TwoFactorController::class, 'regenerateRecoveryCodes'])->name('profile.two-factor.recovery');
    Route::delete('/profile/two-factor', [TwoFactorController::class, 'disable'])->name('profile.two-factor.disable');

    // Security Center (Audit Logs, Blocked IPs, API Tokens, Encrypted Backups)
    Route::get('/security', [SecurityCenterController::class, 'index'])
        ->middleware('permission:security.view')
        ->name('security.index');

    Route::middleware('permission:security.manage')->group(function () {
        Route::post('/security/unblock-ip', [SecurityCenterController::class, 'unblockIp'])->name('security.unblock-ip');
        Route::post('/security/tokens', [SecurityCenterController::class, 'createToken'])->name('security.tokens.create');
        Route::delete('/security/tokens/{apiToken}', [SecurityCenterController::class, 'revokeToken'])->name('security.tokens.revoke');
        Route::post('/security/backup', [SecurityCenterController::class, 'runBackup'])
            ->middleware('permission:backups.manage')
            ->name('security.backup');
    });
});
