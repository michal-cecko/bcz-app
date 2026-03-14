<?php

namespace App\Models;

use App\Contracts\Linkable;
use App\Models\Concerns\HasUuidV7;
use Illuminate\Support\Collection;
use RalphJSmit\Filament\MediaLibrary\Models\MediaLibraryItem as BaseMediaLibraryItem;

class MediaLibraryItem extends BaseMediaLibraryItem implements Linkable
{
    use HasUuidV7;

    public function getLinkUrl(): string
    {
        return $this->getFirstMediaUrl('library');
    }

    public function getLinkLabel(): string
    {
        return $this->getItem()?->name ?? $this->getKey();
    }

    /**
     * @return Collection<string, string>
     */
    public static function linkableOptions(): Collection
    {
        return static::query()
            ->with('media')
            ->get()
            ->mapWithKeys(fn (self $item) => [$item->getKey() => $item->getLinkLabel()]);
    }
}
