<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarketShare extends Model
{
    use HasFactory;

    protected $table = 'market_share';
    protected $fillable = [
        'company_id',
        'year',
        'quarter',
        'growth_net_profit',
        'price_popup',
        'price',
        'market_cap',
    ];

    public $timestamps = false;

    public function company()
    {
        return $this->belongsTo(Company::class);
    }
}