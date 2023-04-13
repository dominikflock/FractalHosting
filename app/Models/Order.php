<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'instance_id',
        'instance_module_id',
        'module_id',
        'mollie_transaction_id',
        'payment_status',
        'duration_booked',
        'finished_at',
    ];
}
