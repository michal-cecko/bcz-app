<?php

namespace App\Http\Controllers;

use App\Models\Page;
use App\Models\SubscriptionPlan;
use Awcodes\Mason\Support\MasonRenderer;
use Illuminate\View\View;

class PricingController extends Controller
{
    public function index(): View
    {
        $plans = SubscriptionPlan::query()
            ->where('is_active', true)
            ->with('prices')
            ->orderBy('sort_order')
            ->get();

        $page = Page::query()
            ->published()
            ->where('system_key', 'pricing')
            ->first();

        $renderedContent = '';
        if ($page && $page->content) {
            $renderedContent = MasonRenderer::make($page->content)
                ->bricks(PageController::BRICKS)
                ->toUnsafeHtml();
        }

        $currency = app()->getLocale() === 'cs' ? 'CZK' : 'EUR';

        return view('pages.pricing', [
            'plans' => $plans,
            'page' => $page,
            'renderedContent' => $renderedContent,
            'currency' => $currency,
        ]);
    }
}
