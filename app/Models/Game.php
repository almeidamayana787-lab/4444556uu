<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Game extends Model
{
    protected $fillable = ['game_code', 'game_name', 'provider_code', 'banner_url', 'banner_local', 'is_popular', 'status'];

    public function provider()
    {
        return $this->belongsTo(Provider::class, 'provider_code', 'code');
    }
}
