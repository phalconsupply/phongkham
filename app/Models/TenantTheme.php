<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TenantTheme extends Model
{
    protected $fillable = [
        'id',
        'clinic_name',
        'logo_path',
        'primary_color',
        'secondary_color',
    ];

    public $incrementing = false;
    protected $keyType = 'string';
}
