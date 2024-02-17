<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute as Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Employee extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'address',
        'image',
        'status'
    ];

    /**
     * image
     *
     * @return Attribute
     */

    //  Eloquent Accessor
    // Agar path image menggunakan http://127.0.0.1:8000/uploads/
    protected function image(): Attribute
    {
        return Attribute::make(
            get: fn ($image) => asset('/uploads/' . $image),
        );
    }

}
