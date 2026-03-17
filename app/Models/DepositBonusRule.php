<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DepositBonusRule extends Model
{
    protected $fillable = [
        'name',
        'min_amount',
        'max_amount',
        'bonus_amount',
    ];
}
