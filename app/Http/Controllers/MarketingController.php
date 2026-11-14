<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class MarketingController extends Controller
{
    public function home(): View
    {
        return view('marketing.home', [
            'content' => __('marketing.home'),
            'services' => __('marketing.services.cards'),
            'canonical' => 'https://leadabox.com',
        ]);
    }

    public function about(): View
    {
        return view('marketing.about', [
            'content' => __('marketing.about'),
            'canonical' => 'https://leadabox.com/about',
        ]);
    }

    public function services(): View
    {
        return view('marketing.services', [
            'content' => __('marketing.services'),
            'canonical' => 'https://leadabox.com/services',
        ]);
    }

    public function service(string $service): View
    {
        $pages = __('marketing.service_pages');
        abort_unless(is_array($pages) && isset($pages[$service]), 404);

        $mainSitePaths = [
            'web-development' => '/services/web-development',
            'web-design' => '/services/web-design',
            'mobile-app-development' => '/services/mobile-app-development',
            'ai-agents' => '/ai-agents',
            'ai-integration' => '/services/ai-integration',
        ];

        return view('marketing.service', [
            'content' => $pages[$service],
            'service' => $service,
            'canonical' => isset($mainSitePaths[$service])
                ? 'https://leadabox.com'.$mainSitePaths[$service]
                : route('marketing.service', $service),
        ]);
    }

    public function contact(): View
    {
        return view('marketing.contact', [
            'content' => __('marketing.contact'),
            'canonical' => 'https://leadabox.com/contact',
        ]);
    }
}
