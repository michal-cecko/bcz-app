<?php

namespace App\Livewire;

use App\Enums\BannerTypeEnum;
use App\Models\Banner;
use Illuminate\Contracts\View\View;
use Livewire\Component;

class BannerDisplay extends Component
{
    public ?string $pageId = null;

    public ?string $topbarHtml = null;

    public ?string $topbarId = null;

    public ?string $floatingHtml = null;

    public ?string $floatingId = null;

    public bool $floatingIsLight = false;

    public ?string $popupHtml = null;

    public ?string $popupId = null;

    public function mount(?string $pageId = null): void
    {
        $this->pageId = $pageId;

        $this->resolveTopbar();
        $this->resolveFloating();
        $this->resolvePopup();
    }

    public function dismissTopbar(): void
    {
        if ($this->topbarId) {
            session()->put("banner_dismissed_{$this->topbarId}", true);
            $this->topbarHtml = null;
        }
    }

    public function dismissFloating(): void
    {
        if ($this->floatingId) {
            session()->put("banner_dismissed_{$this->floatingId}", true);
            $this->floatingHtml = null;
        }
    }

    public function dismissPopup(): void
    {
        if ($this->popupId) {
            session()->put("banner_dismissed_{$this->popupId}", true);
            $this->popupHtml = null;
        }
    }

    public function render(): View
    {
        return view('livewire.banner-display');
    }

    private function resolveTopbar(): void
    {
        $banner = Banner::resolve(BannerTypeEnum::Topbar, $this->pageId);

        if ($banner && ! session()->has("banner_dismissed_{$banner->id}")) {
            $this->topbarId = $banner->id;
            $this->topbarHtml = $this->renderBanner($banner);
        }
    }

    private function resolveFloating(): void
    {
        $banner = Banner::resolve(BannerTypeEnum::Floating, $this->pageId);

        if ($banner && ! session()->has("banner_dismissed_{$banner->id}")) {
            $this->floatingId = $banner->id;
            $this->floatingHtml = $this->renderBanner($banner);
            $bgColor = strtolower($banner->content['bg_color'] ?? '#111111');
            $this->floatingIsLight = in_array($bgColor, ['#ffffff', '#fff', '#f5f5f5']);
        }
    }

    private function resolvePopup(): void
    {
        $banner = Banner::resolve(BannerTypeEnum::Popup, $this->pageId);

        if ($banner && ! session()->has("banner_dismissed_{$banner->id}")) {
            $this->popupId = $banner->id;
            $this->popupHtml = $this->renderBanner($banner);
        }
    }

    private function renderBanner(Banner $banner): ?string
    {
        $content = $banner->content ?: [];

        if (empty($content)) {
            return null;
        }

        $view = match ($banner->type) {
            BannerTypeEnum::Topbar => 'components.banners.topbar',
            BannerTypeEnum::Floating => 'components.banners.floating',
            BannerTypeEnum::Popup => 'components.banners.popup',
        };

        return view($view, $content)->render();
    }
}
