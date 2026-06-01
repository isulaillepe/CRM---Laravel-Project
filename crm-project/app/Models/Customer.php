<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'phone',
        'status',
    ];

    /**
     * Get all of the proposals for the customer.
     */
    public function proposals()
    {
        return $this->hasMany(Proposal::class);
    }
}