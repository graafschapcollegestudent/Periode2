<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

use Illuminate\Database\Eloquent\Factories\HasFactory;

class Task extends Model

{

    use HasFactory;

    protected $fillable = ['title', 'is_done'];
    protected $casts = [
        'is_done' => 'boolean',
    ];
    protected $primaryKey = 'id';
    


}