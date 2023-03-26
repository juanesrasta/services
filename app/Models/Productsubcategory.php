<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Productsubcategory extends Model
{
    use HasFactory;
	
	protected $fillable = [
        'product_id',
        'subcategory_id',
    ];
	
}
