<?php

namespace App\Models\Concerns;

use Illuminate\Database\Eloquent\Model;

/**
 * Deletes a payable model's polymorphic payments when the model is deleted.
 *
 * Payments attach via a morph (payable_type/payable_id) with no database foreign
 * key, so there is no DB-level cascade; this event provides the cleanup.
 */
trait PurgesPaymentsOnDelete
{
    public static function bootPurgesPaymentsOnDelete(): void
    {
        static::deleting(function (Model $model): void {
            $model->payments()->delete();
        });
    }
}
