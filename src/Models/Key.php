<?php

declare(strict_types=1);

namespace Tmogdans\LaravelCryptoShredder\Models;

use Illuminate\Database\Eloquent\Model;

class Key extends Model
{
    protected $fillable = [
        'model',
        'model_id',
        'key'
    ];

    public $timestamps = false;
}
