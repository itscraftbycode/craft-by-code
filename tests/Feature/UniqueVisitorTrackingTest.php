<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\WebsiteVisitor;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UniqueVisitorTrackingTest extends TestCase
{
    use RefreshDatabase;

    public function test_first_frontend_visit_records_one_unique_visitor(): void
    {
        $this->assertEquals(0, WebsiteVisitor::count());

        $response = $this->withServerVariables([
            'REMOTE_ADDR' => '203.0.113.195',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_7)',
        ])->get('/');

        $response->assertOk();
        $this->assertEquals(1, WebsiteVisitor::count());

        $visitor = WebsiteVisitor::first();
        $this->assertEquals('203.0.113.195', $visitor->ip_address);
        $this->assertEquals(1, $visitor->hits);
    }

    public function test_multiple_page_visits_by_same_visitor_does_not_create_duplicates(): void
    {
        $serverVars = [
            'REMOTE_ADDR' => '198.51.100.42',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (iPhone; CPU iPhone OS 16_0 like Mac OS X)',
        ];

        // 1st visit
        $this->withServerVariables($serverVars)->get('/');
        // 2nd visit to services
        $this->withServerVariables($serverVars)->get('/services');
        // 3rd visit to contact
        $this->withServerVariables($serverVars)->get('/contact');

        // Must still be EXACTLY 1 unique visitor in database
        $this->assertEquals(1, WebsiteVisitor::count());
    }

    public function test_different_visitors_create_distinct_unique_records(): void
    {
        // Visitor 1
        $this->withServerVariables([
            'REMOTE_ADDR' => '192.0.2.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64)',
        ])->get('/');

        // Visitor 2
        $this->withServerVariables([
            'REMOTE_ADDR' => '192.0.2.2',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Android 13; Mobile)',
        ])->get('/');

        $this->assertEquals(2, WebsiteVisitor::count());
    }

    public function test_bots_and_crawlers_are_not_counted_as_unique_visitors(): void
    {
        $this->withServerVariables([
            'REMOTE_ADDR' => '66.249.66.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; Googlebot/2.1; +http://www.google.com/bot.html)',
        ])->get('/');

        $this->withServerVariables([
            'REMOTE_ADDR' => '157.55.39.1',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (compatible; bingbot/2.0; +http://www.bing.com/bingbot.htm)',
        ])->get('/');

        $this->assertEquals(0, WebsiteVisitor::count());
    }

    public function test_admin_dashboard_displays_unique_visitor_stats(): void
    {
        // Seed 3 unique visitors
        WebsiteVisitor::create([
            'visitor_hash' => hash('sha256', '1.1.1.1|Chrome'),
            'ip_address' => '1.1.1.1',
            'user_agent' => 'Chrome',
            'hits' => 5,
            'first_visited_at' => now(),
            'last_visited_at' => now(),
        ]);
        WebsiteVisitor::create([
            'visitor_hash' => hash('sha256', '2.2.2.2|Safari'),
            'ip_address' => '2.2.2.2',
            'user_agent' => 'Safari',
            'hits' => 2,
            'first_visited_at' => now(),
            'last_visited_at' => now(),
        ]);

        $admin = new User([
            'name' => 'Admin User',
            'email' => 'admin@test.com',
            'password' => Hash::make('Password!1234'),
        ]);
        $admin->role = 'admin';
        $admin->is_active = true;
        $admin->save();

        $response = $this->actingAs($admin)->get(route('admin.dashboard'));
        $response->assertOk();
        $response->assertSee('Unique Visitors');
        $response->assertSee('Live Website Traffic (Unique Visitors)');
    }
}
