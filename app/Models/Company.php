<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Company extends Model
{
    use HasFactory;

    protected $table = 'company';
    protected $fillable = [
        'ticker',
        'name',
        'address',
        'category',
        'description',
        'logo'
    ];

    public $timestamps = false;

    protected static function boot()
    {
        parent::boot();

        static::deleting(function ($company) {
            $company->dividends()->delete();
            $company->financialPositions()->delete();
            $company->marketShares()->delete();
            $company->revenues()->delete();
            $company->profitabilityRatios()->delete();
            $company->relativeRatios()->delete();
            $company->liquidityRatios()->delete();
        });
    }

    public function revenues()
    {
        return $this->hasMany(RevenueData::class);
    }

    public function financialPositions()
    {
        return $this->hasMany(FinancialPositionData::class);
    }

    public function dividends()
    {
        return $this->hasMany(DividendData::class);
    }

    public function profitabilityRatios()
    {
        return $this->hasMany(ProfitabilityRatioData::class);
    }

    public function relativeRatios()
    {
        return $this->hasMany(RelativeRatioData::class);
    }

    public function liquidityRatios()
    {
        return $this->hasMany(LiquidityRatioData::class);
    }

    public function marketShares()
    {
        return $this->hasMany(MarketShare::class);
    }

    public function packs()
    {
        return $this->hasMany(Pack::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transactions::class);
    }
}