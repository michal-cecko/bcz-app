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

        return $model?->getLinkUrl();
    }
}
