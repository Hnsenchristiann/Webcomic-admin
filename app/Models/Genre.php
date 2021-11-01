<?php

namespace App\Models;

use App\Traits\Pipeable;
use Illuminate\Database\Eloquent\Model;

class Genre extends Model
{
    use Pipeable;
    protected $guarded = [];
}
