<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Provider extends Model
{
    protected $fillable = ['code', 'name', 'cover_image', 'logo', 'is_slot', 'status'];

    public function games()
    {
        return $this->hasMany(Game::class, 'provider_code', 'code');
    }
}
