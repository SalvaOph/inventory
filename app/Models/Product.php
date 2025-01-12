<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'price',
        'saleprice',
        'creator_user_id',
        'last_update_user_id',
    ];

    /**
     * Relación 1:1 con el modelo Inventory.
     */
    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }

    public function warehouse()
    {
        return $this->hasOne(Warehouse::class);
    }

    public function sales()
    {
        return $this->belongsToMany(Sale::class)->withPivot('quantity')->withTimestamps();
    }

    public function purchases()
    {
        return $this->belongsToMany(Purchase::class)->withPivot('quantity')->withTimestamps();
    }
}
