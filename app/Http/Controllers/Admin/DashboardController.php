<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Agreement;
use App\Models\BlogPost;
use App\Models\ContactInquiry;
use App\Models\Project;
use App\Models\Service;
use App\Models\Testimonial;
use App\Models\WebsiteSetting;
use App\Models\WebsiteVisitor;

class DashboardController extends Controller
{
    public function index()
    {
        $totalVisitors = 0;
        $todayVisitors = 0;
        $recentVisitors = collect();

        try {
            $totalVisitors = WebsiteVisitor::count();
            $todayVisitors = WebsiteVisitor::whereDate('first_visited_at', today())->count();
            $recentVisitors = WebsiteVisitor::latest('last_visited_at')->take(5)->get();
        } catch (\Throwable $e) {
            // Graceful fallback if table is still migrating
        }

        $stats = [
            'total_visitors' => $totalVisitors,
            'today_visitors' => $todayVisitors,
            'total_inquiries' => ContactInquiry::count(),
            'unread_inquiries' => ContactInquiry::where('status', 'new')->count(),
            'total_services' => Service::count(),
            'active_services' => Service::where('is_active', true)->count(),
            'total_projects' => Project::count(),
            'featured_projects' => Project::where('is_featured', true)->count(),
            'total_testimonials' => Testimonial::count(),
            'total_blogs' => BlogPost::count(),
            'total_agreements' => Agreement::count(),
            'pending_agreements' => Agreement::whereIn('status', ['pending_signature', 'estamp_pending'])->count(),
        ];

        $recentInquiries = ContactInquiry::latest()->take(5)->get();
        $recentProjects = Project::latest()->take(4)->get();
        $recentAgreements = Agreement::latest()->take(4)->get();

        return view('admin.dashboard', compact('stats', 'recentInquiries', 'recentProjects', 'recentAgreements', 'recentVisitors'));
    }
}

