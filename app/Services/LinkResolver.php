<?php

namespace App\Services;

use App\Enums\LinkTypeEnum;

class LinkResolver
{
    public static function resolve(array $link): ?string
    {
        $type = LinkTypeEnum::tryFrom($link['link_type'] ?? '');

        if (! $type) {
            return null;
        }

        if ($type === LinkTypeEnum::Custom) {
            $url = $link['link_url'] ?? null;

            return is_array($url)
                ? ($url[app()->getLocale()] ?? $url['sk'] ?? null)
                : $url;
        }

        $modelClass = $type->getModelClass();
        $modelId = $link['link_model_id'] ?? null;

        if (! $modelClass || ! $modelId) {
            return null;
        }

        $model = $modelClass::find($modelId);

        if (! $model) {
            return null;
        }

        // Media links resolve directly to the file URL
        if ($type === LinkTypeEnum::Media) {
            return $model->getLinkUrl() ?: null;
        }

        // Role-based link types use specific route prefixes
        $routePrefix = $type->getRoutePrefix();
        $url = $routePrefix ? $routePrefix.$model->slug : $model->getLinkUrl();

        if (! $url) {
            return null;
        }

        return locale_url($url);
    }
}
