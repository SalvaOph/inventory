<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Client extends Model
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

    /**
     * Relación 1:1 con el modelo Inventory.
     */
    public function sale()
    {
        return $this->hasOne(Sale::class);
    } 
}