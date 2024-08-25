<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipsterStaticContent extends Model
{
    use HasFactory;

    protected $table = 'tipster_static_contents';

    protected $fillable = ['name', 'content', 'slug', 'is_active', 'created_by', 'updated_by'];
}
