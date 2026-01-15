<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;


/**
 * App\Models\ChannelMember
 * @property string $id
 * @property string $channel_id
 * @property string $user_id
 * @property \Illuminate\Support\Carbon $joined_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @mixin \Eloquent
 */
class ChannelMember extends Model
{
    use \Illuminate\Database\Eloquent\Concerns\HasUlids;
}
