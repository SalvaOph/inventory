<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Warehouse extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'address',
        'telephone',
        'email',
        'creator_user_id',
        'last_update_user_id',
    ];

    public function inventories()
    {
        return $this->hasMany(Inventory::class, 'warehouse_id');
    }

}