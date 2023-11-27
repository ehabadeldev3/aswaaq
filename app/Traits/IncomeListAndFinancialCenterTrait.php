<?php

namespace App\Traits;

use App\Models\Restriction;
use App\Models\SubAccount;

trait IncomeListAndFinancialCenterTrait
{
    public function debitRestriction($id ,$date,$from_date,$to_date)
    {
        return  Restriction::where([['debit',1],['sub_account_id',$id]])->whereHas('dailyRestriction', function ($q) use ($date,$from_date,$to_date) {
            if($from_date && $to_date)
                $q->whereDate('date', ">=", $from_date)->whereDate('date', "<=", $to_date);
            else
                $q->whereDate('date', "<=", $date);
        })->get()->sum('amount');
    }

    public function creditRestriction($id,$date,$from_date,$to_date)
    {
        return   Restriction::where([['debit',0],['sub_account_id',$id]])->whereHas('dailyRestriction', function ($q) use ($date,$from_date,$to_date) {
            if($from_date && $to_date)
                $q->whereDate('date', ">=", $from_date)->whereDate('date', "<=", $to_date);
            else
                $q->whereDate('date', "<=", $date);
        })->get()->sum('amount');
    }
    public function calculateRestrictions($subAccount, $date, $from_date, $to_date)
    {
        $num_debit = 0;
        $num_credit = 0;

        foreach ($subAccount->children as $child) {
            $child_debit = $this->calculateRestrictions($child, $date, $from_date, $to_date);
            $child_credit = $this->calculateRestrictions($child, $date, $from_date, $to_date);
            $num_debit += $child_debit;
            $num_credit += $child_credit;
        }

        $num_debit += $this->debitRestriction($subAccount->id, $date, $from_date, $to_date);
        $num_credit += $this->creditRestriction($subAccount->id, $date, $from_date, $to_date);

        $subAccount->sumDebit = $this->debitRestriction($subAccount->id, $date, $from_date, $to_date) + $num_debit;
        $subAccount->sumCredit = $this->creditRestriction($subAccount->id, $date, $from_date, $to_date) + $num_credit;

        return $num_debit + $num_credit;
    }

    public function calculateAssetsAndLiabilities($mainAccountId, $subAccountId, $date, $from_date, $to_date)
    {
        $accounts = [];
        $data_accounts = SubAccount::where([['main_account_id', $mainAccountId], ['sub_account_id', $subAccountId]])->get();

        foreach ($data_accounts as $indexOne => $account) {
            $num_debit_one = 0;
            $num_credit_one = 0;

            if (count($account->children) > 0) {
                foreach ($account->children as $indexTwo => $childOne) {
                    $num_debit_one += $this->calculateRestrictions($childOne, $date, $from_date, $to_date);
                    $num_credit_one += $this->calculateRestrictions($childOne, $date, $from_date, $to_date);

                    $accounts[$indexOne][$indexTwo] = $childOne;
                }

                $account->sumDebit = $this->debitRestriction($account->id, $date, $from_date, $to_date) + $num_debit_one;
                $account->sumCredit = $this->creditRestriction($account->id, $date, $from_date, $to_date) + $num_credit_one;
                $accounts[$indexOne] = $account;
            } else {
                $account->sumDebit = $this->debitRestriction($account->id, $date, $from_date, $to_date);
                $account->sumCredit = $this->creditRestriction($account->id, $date, $from_date, $to_date);
                $accounts[$indexOne] = $account;
            }
        }

        return $accounts;
    }
}
