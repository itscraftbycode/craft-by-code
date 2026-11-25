<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\Project;
use App\Models\Testimonial;
use App\Models\BlogPost;
use App\Models\ContactInquiry;
use App\Models\WebsiteSetting;
use App\Models\AboutContent;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class FrontendController extends Controller
{
    /**
     * Home Page
     */
    public function index()
    {
        $featuredServices = Service::active()->orderBy('order')->take(6)->get();
        if ($featuredServices->isEmpty()) {
            $featuredServices = Service::active()->take(6)->get();
        }

        $allServices = Service::active()->take(12)->get();
        $projects = Project::active()->featured()->take(4)->get();
        if ($projects->isEmpty()) {
            $projects = Project::active()->take(4)->get();
        }

        $testimonials = Testimonial::active()->featured()->take(3)->get();
        if ($testimonials->isEmpty()) {
            $testimonials = Testimonial::active()->take(3)->get();
        }

        $recentBlogs = BlogPost::published()->take(3)->get();

        return view('frontend.home', compact(
            'featuredServices',
            'allServices',
            'projects',
            'testimonials',
            'recentBlogs'
        ));
    }

    /**
     * About Us Page
     */
    public function about()
    {
        $intro = AboutContent::getSection('intro');
        $story = AboutContent::getSection('story');
        $missionVision = AboutContent::getSection('mission_vision');
        $values = AboutContent::getSection('values');
        $process = AboutContent::getSection('process');
        $whyTrust = AboutContent::getSection('why_trust');
        $team = AboutContent::getSection('team');
        $testimonials = Testimonial::active()->take(4)->get();

        return view('frontend.about', compact(
            'intro',
            'story',
            'missionVision',
            'values',
            'process',
            'whyTrust',
            'team',
            'testimonials'
        ));
    }

    /**
     * Services Listing Page
     */
    public function services(Request $request)
    {
        $query = Service::active();

        if ($request->filled('search')) {
            $search = \App\Security\UrlValidator::escapeLike((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('short_description', 'like', "%{$search}%")
                  ->orWhere('full_description', 'like', "%{$search}%");
            });
        }

        $services = $query->paginate(12)->withQueryString();

        return view('frontend.services.index', compact('services'));
    }

    /**
     * Service Detail Page
     */
    public function serviceDetail($slug)
    {
        $service = Service::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $otherServices = Service::active()->where('id', '!=', $service->id)->orderBy('order')->take(5)->get();
        $escapedTitle = \App\Security\UrlValidator::escapeLike($service->title);
        $relatedProjects = Project::active()->where('category', 'like', "%{$escapedTitle}%")->take(2)->get();
        if ($relatedProjects->isEmpty()) {
            $relatedProjects = Project::active()->take(2)->get();
        }

        return view('frontend.services.show', compact('service', 'otherServices', 'relatedProjects'));
    }

    /**
     * Portfolio / Projects Page
     */
    public function portfolio(Request $request)
    {
        $category = $request->input('category');
        $query = Project::active();

        if ($category && $category !== 'all') {
            $query->where('category', $category);
        }

        $projects = $query->paginate(9)->withQueryString();
        $categories = Project::active()->pluck('category')->unique();

        return view('frontend.portfolio.index', compact('projects', 'categories', 'category'));
    }

    /**
     * Project Detail Page
     */
    public function projectDetail($slug)
    {
        $project = Project::where('slug', $slug)->where('is_active', true)->firstOrFail();
        $relatedProjects = Project::active()->where('id', '!=', $project->id)->take(2)->get();

        return view('frontend.portfolio.show', compact('project', 'relatedProjects'));
    }

    /**
     * Blog Insights Listing Page
     */
    public function blog(Request $request)
    {
        $query = BlogPost::published();

        if ($request->filled('category')) {
            $query->where('category', $request->input('category'));
        }

        if ($request->filled('search')) {
            $search = \App\Security\UrlValidator::escapeLike((string) $request->input('search'));
            $query->where(function ($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                  ->orWhere('excerpt', 'like', "%{$search}%");
            });
        }

        $posts = $query->paginate(6)->withQueryString();
        $categories = BlogPost::published()->pluck('category')->unique();

        return view('frontend.blog.index', compact('posts', 'categories'));
    }

    /**
     * Single Blog Post
     */
    public function blogDetail($slug)
    {
        $post = BlogPost::where('slug', $slug)->where('is_published', true)->firstOrFail();
        $recentPosts = BlogPost::published()->where('id', '!=', $post->id)->take(3)->get();

        return view('frontend.blog.show', compact('post', 'recentPosts'));
    }

    /**
     * Contact Us Page
     */
    public function contact()
    {
        $services = Service::active()->pluck('title');
        return view('frontend.contact', compact('services'));
    }

    /**
     * Handle Contact Inquiry Form Submission
     */
    public function submitInquiry(Request $request)
    {
        // Honeypot bot trap check
        if ($request->filled('contact_hp_website')) {
            \App\Security\SecurityLogger::log('bot_honeypot_triggered', 'Automated bot caught by inquiry honeypot field', 'medium', null, $request, [
                'bot_payload' => $request->input('contact_hp_website'),
            ]);

            if ($request->ajax() || $request->wantsJson()) {
                return response()->json([
                    'success' => true,
                    'message' => 'Thank you! Your message has been received. Our team will contact you within 24 hours.'
                ]);
            }

            return redirect()->back()->with('success', 'Thank you! Your message has been received. Our team will contact you within 24 hours.');
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:100',
            'email' => 'required|email|max:150',
            'phone' => 'nullable|string|max:30',
            'subject' => 'nullable|string|max:150',
            'service_interested' => 'nullable|string|max:100',
            'message' => 'required|string|min:10|max:3000',
        ], [
            'name.required' => 'Please provide your full name.',
            'email.required' => 'A valid email address is required.',
            'email.email' => 'Please enter a valid email address.',
            'message.required' => 'Please tell us about your project or inquiry.',
            'message.min' => 'Your message should be at least 10 characters long.',
        ]);

        if ($validator->fails()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput()->with('error', 'Please correct the highlighted errors below.');
        }

        // CAPTCHA verification (Google reCAPTCHA / Cloudflare Turnstile)
        if (! \App\Security\CaptchaValidator::verify($request)) {
            if ($request->ajax() || $request->wantsJson()) {
                return response()->json(['success' => false, 'errors' => ['captcha' => ['Security CAPTCHA verification failed. Please try again.']]], 422);
            }

            return redirect()->back()->withErrors(['captcha' => 'Security CAPTCHA verification failed. Please try again.'])->withInput();
        }

        $inquiry = ContactInquiry::create([
            'name' => strip_tags(trim($request->input('name'))),
            'email' => trim($request->input('email')),
            'phone' => $request->filled('phone') ? strip_tags(trim($request->input('phone'))) : null,
            'subject' => $request->filled('subject') ? strip_tags(trim($request->input('subject'))) : 'General Inquiry',
            'service_interested' => $request->filled('service_interested') ? strip_tags(trim($request->input('service_interested'))) : null,
            'message' => strip_tags(trim($request->input('message'))),
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        if ($request->ajax() || $request->wantsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Thank you! Your message has been received. Our team will contact you within 24 hours.'
            ]);
        }

        return redirect()->back()->with('success', 'Thank you! Your inquiry has been sent successfully. Our team will get back to you shortly.');
    }

    /**
     * Handle Newsletter Subscription
     */
    public function newsletter(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email|max:150',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->with('error', 'Please enter a valid email address.');
        }

        // We can record or log the newsletter subscription
        ContactInquiry::create([
            'name' => 'Newsletter Subscriber',
            'email' => $request->input('email'),
            'subject' => 'Newsletter Subscription',
            'message' => 'Subscribed to newsletter updates from website footer.',
            'status' => 'new',
            'ip_address' => $request->ip(),
        ]);

        return redirect()->back()->with('success', 'Thank you for subscribing to our tech insights!');
    }
}

