<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class GlobalSetting extends Model
{
    protected $fillable = [
        'key',
        'value',
        'rollover_deposit_multiplier',
        'rollover_bonus_multiplier',
        'system_arrecadacao',
        'system_distribuicao'
    ];
}
