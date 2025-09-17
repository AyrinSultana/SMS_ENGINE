<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Excel extends Model
{
    // Table name (optional if following convention)
    protected $table = 'excel';

    // Mass assignable fields
    protected $fillable = ['name'];
}
