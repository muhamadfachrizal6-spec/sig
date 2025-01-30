<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Transactions extends Model
{
    protected $table = 'transactions';
    protected $fillable = [
        'pack_id',
        'company_id',
        'total_price',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function pack()
    {
        return $this->belongsTo(Pack::class);
    }
}
