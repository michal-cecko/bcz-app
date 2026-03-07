<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Symfony\Component\Uid\Uuid;

trait HasUuidV7
{
    use HasUuids;

    public function newUniqueId(): string
    {
        return (string) Uuid::v7();
    }
}
