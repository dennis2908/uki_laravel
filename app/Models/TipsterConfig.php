<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipsterConfig extends Model
{
    use HasFactory;

    protected $table = 'tipster_config';

    protected $fillable = ['name', 'display_label', 'value'];
}
