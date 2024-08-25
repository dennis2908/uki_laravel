<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TipsterTransactionCancel extends Model
{
    use HasFactory;

    protected $table = 'tipster_transaction_cancel';

    protected $fillable = ['user_id', 'tipster_transaction_id', 'cancel_time'];
}
