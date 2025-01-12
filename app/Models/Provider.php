<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Provider extends Model
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
     * Relación 1:1 con el modelo Purchase.
     */
    public function purcahse()
    {
        return $this->hasOne(Purchase::class);
    } 
}
