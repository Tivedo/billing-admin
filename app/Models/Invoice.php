<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $table = 'invoice';
    protected $guarded = [];
    public $timestamps = false;

    // public function order()
    // {
    //     return $this->belongsTo(Order::class, 'order_id');
    // }

    // public function invoiceItem()
    // {
    //     return $this->hasMany(InvoiceItem::class, 'invoice_id');
    // }
}
