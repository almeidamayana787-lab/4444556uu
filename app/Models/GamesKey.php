<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Casts\Attribute;

class GamesKey extends Model
{
    use HasFactory;

    /**
     * The database table used by the model.
     *
     * @var string
     */
    protected $table = 'games_keys';

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // Play Fiver
        'playfiver_secret',
        'saldo_agente',
        'playfiver_code',
        'playfiver_token',
        // MAX API GAMES
        'max_api_code',
        'max_api_token',
        'max_api_secret',
        'active_api'
    ];

    protected $hidden = ['updated_at'];

    /**
     * Accessor for playfiver_secret.
     */
    protected function playfiverSecret(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Accessor for playfiver_code.
     */
    protected function playfiverCode(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Accessor for playfiver_token.
     */
    protected function playfiverToken(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Accessor for max_api_secret.
     */
    protected function maxApiSecret(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Accessor for max_api_code.
     */
    protected function maxApiCode(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }

    /**
     * Accessor for max_api_token.
     */
    protected function maxApiToken(): Attribute
    {
        return Attribute::make(
            get: fn(?string $value) => env('APP_DEMO') ? '*********************' : $value,
        );
    }
}
