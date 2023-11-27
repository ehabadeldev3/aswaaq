<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Treasury extends Model
{
    use HasFactory;
    protected $guarded = ['id'];

    protected $appends = [
        'amount',
        'income',
        'income_transfer',
        'name',
        'expense',
        'expense_transfer',
    ];

    //---------------------------- total amount in treasury

    public function getNameAttribute()
    {
        return app()->getLocale() == 'ar' ? $this->name_ar : $this->name_en;
    }

    public function getAmountAttribute()
    {
        return ( $this->income + $this->income_transfer ) - ( $this->expense + $this->expense_transfer );
    }

    //--------------------------- income in treasury

    public function getIncomeAttribute()
    {
        $totalIncome = 0 ;
        $totalIncome += $this->incomeAndExpense->whereNotNull('income_id')->sum('amount');
        $totalIncome += $this->treasuryBalance ? $this->treasuryBalance->amount:0;
        return $totalIncome;
    }

    public function getIncomeTransferAttribute()
    {
        return $this->toTransferringTreasury->sum('amount');
    }

    // ------------------- expense in treasury
    public function getExpenseAttribute()
    {
        $totalExpense = 0;
        $totalExpense += $this->incomeAndExpense->whereNotNull('expense_id')->sum('amount');
        return $totalExpense;
    }

    public function getExpenseTransferAttribute()
    {
        return $this->fromTransferringTreasury->sum('amount');
    }

    //--------------------- relations

    public function treasuryParent(): \Illuminate\Database\Eloquent\Relations\BelongsTo
    {
        return $this->belongsTo(Treasury::class, 'treasury_id');
    }

    public function children(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Treasury::class, 'treasury_id');
    }

    public function incomeAndExpense(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(IncomeAndExpense::class);
    }

    public function fromTransferringTreasury(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransferringTreasury::class, 'from_treasury_id');
    }

    public function toTransferringTreasury(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(TransferringTreasury::class, 'to_treasury_id');
    }


    public function treasuryBalance()
    {
        return $this->hasOne(TreasuryBalance::class);
    }
}
