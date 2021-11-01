<?php

namespace App\Models;

use App\Traits\Pipeable;
use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    use Pipeable;
    protected $guarded = [];
}
