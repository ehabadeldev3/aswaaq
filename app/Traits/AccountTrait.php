<?php

namespace App\Traits;

use App\Models\Restriction;

trait AccountTrait
{
    public function debitAmount($subAccount)
    {

        return $this->getSumOfChildernTransactions($subAccount,'debit');
        // $num = $this->debitTransaction($subAccount ? $subAccount->id : null);
        // foreach ($subAccount->children as $first){
        //     if (count($first->children)>0)
        //     {
        //         foreach ($first->children as $two)
        //         {
        //             if (count($two->children)>0)
        //             {
        //                 foreach ($two->children as $three)
        //                 {
        //                     if (count($three->children)>0)
        //                     {
        //                         foreach ($three->children as $four)
        //                         {
        //                             if (count($four->children)>0)
        //                             {
        //                                 foreach ($four->children as $five)
        //                                 {
        //                                     $num += $this->debitTransaction($five->id);
        //                                 }
        //                             }
        //                             $num += $this->debitTransaction($four->id);
        //                         }
        //                     }
        //                     $num += $this->debitTransaction($three->id);
        //                 }
        //             }
        //             $num += $this->debitTransaction($two->id);
        //         }
        //     }
        //     $num += $this->debitTransaction($first->id);

        // }
        // return $num;
    }


    protected function getSumOfChildernTransactions($parent,$type,$sum = 0){
        if(isset($parent) && isset($parent->children))
        foreach ($parent->children as $child){
            $sum += $type == 'debit' ? $this->debitTransaction($child->id) :  $this->creditTransaction($child->id);
            if (count($child->children) > 0){
                $this->getSumOfChildernTransactions($child,$type);
            }
        }
        return $sum;
    }

    public function creditAmount($subAccount)
    {
        return $this->getSumOfChildernTransactions($subAccount , 'credit');
        // $num = $this->creditTransaction( $subAccount ? $subAccount->id : null);
        // foreach ($subAccount->children as $first){
        //     if (count($first->children)>0)
        //     {
        //         foreach ($first->children as $two)
        //         {
        //             if (count($two->children)>0)
        //             {
        //                 foreach ($two->children as $three)
        //                 {
        //                     if (count($three->children)>0)
        //                     {
        //                         foreach ($three->children as $four)
        //                         {
        //                             if (count($four->children)>0)
        //                             {
        //                                 foreach ($four->children as $five)
        //                                 {
        //                                     $num += $this->creditTransaction($five->id);
        //                                 }
        //                             }
        //                             $num += $this->creditTransaction($four->id);
        //                         }
        //                     }
        //                     $num += $this->creditTransaction($three->id);
        //                 }
        //             }
        //             $num += $this->creditTransaction($two->id);
        //         }
        //     }
        //     $num += $this->creditTransaction($first->id);

        // }
        // return $num;
    }

    public function debitTransaction($id)
    {
        return  Restriction::where([['debit',1],['sub_account_id',$id]])->get()->sum('amount') ;
    }

    public function creditTransaction($id)
    {
        return  Restriction::where([['debit',0],['sub_account_id',$id]])->get()->sum('amount') ;
    }




    public function get_sub_accounts($children , $sub_accounts = [])
    {

        if(count($children) > 0)
        foreach ($children as $child){
            $sub_accounts [] = $child;
            if (count($child->children) > 0){
                $this->get_sub_accounts($child->children);
            }
        }
        return $sub_accounts;        
    }
}
