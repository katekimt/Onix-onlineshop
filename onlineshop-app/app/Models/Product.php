<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ProductImage;
use App\Models\Cart;

class Product extends Model
{
    use HasFactory;

    protected $table = 'products';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'description',
        'in_stick',
        'category_id',
        'price',
        'user_id'
    ];

    public function images()
    {
        return $this->hasMany('App\Models\ProductImage');
    }

    /**
     * Get the reviews of the product.
     */
    public function reviews()
    {
        return $this->hasMany('App\Models\Review');
    }

    /**
     * Get the user that added the product.
     */
    public function user()
    {
        return $this->belongsTo('App\Models\User');
    }

    public function carts(){
        return $this->belongsToMany('App\Models\Cart')->withTimestamps();
    }
}

