<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id', 'customer_name', 'description', 'invoice_date',
        'due_date', 'total_amount', 'total_tax', 'grand_total', 'status'
    ];

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function users()
    {
        return $this->belongsTo(User::class);
    }
}
