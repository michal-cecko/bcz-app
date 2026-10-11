<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

class MemberTeam extends Pivot
{
    protected $table = 'member_team';

    protected function casts(): array
    {
        return ['is_active' => 'boolean', 'joined_at' => 'date', 'continuous_membership' => 'boolean'];
    }
}
