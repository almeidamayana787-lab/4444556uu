<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invite extends Model
{
    protected $fillable = [
        'referrer_id',
        'referred_id',
        'status',
        'referred_deposit_total',
        'referred_bet_total',
    ];
}
