<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Documento extends Model
{
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }
}
