<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Spatie\Permission\Traits\HasRoles;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use  HasFactory, Notifiable,HasRoles,SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $guarded = ['id'];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'role_name' => 'array',
        'status' => 'integer',
        'wallet_points' => 'integer',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }


    public function getJWTCustomClaims()
    {
        return [];
    }

    // set hash password

    public function setPasswordAttribute($value){
        $this->attributes['password'] = bcrypt($value);
    }

    //============== appends paths ===========

    protected $appends = [
        'image_path',
        'sum_account'
    ];

    //append img path

    public function getImagePathAttribute(): string
    {
        $file_name = '';
        if($this->media){
            $file_name = $this->media->file_name;
        }
        return $file_name  ?asset('upload/'.$file_name)  : '';
    }

    public function getSumAccountAttribute(): float
    {
        // if ($this->whereAuthId(2)->whereJsonContains('role_name','client')){
        //     return $this->clientAccounts->sum('amount');
        // }
        return 0;
    }

    public function completedOrdersbetweenTargetDates($target_start_date,$target_end_date){
        return $this->orders()
            ->whereDate('created_at','>=',$target_start_date)
            ->whereDate('created_at','<=',$target_end_date)
            ->where('confirmed_received_amount',1)
            ->whereNull('wallet_target_id')
            ->whereIn('order_status',['Completed','Partial Return']);
    }

    // start Relation
    public function media()
    {
        return $this->morphOne(Media::class,'mediable');
    }
    public function employee()
    {
        return $this->hasOne(Employee::class);
    }

    public function loading_man()
    {
        return $this->hasOne(LoadingMan::class);
    }
    public function representative()
    {
        return $this->hasOne(Representative::class);
    }
    public function dispatcher()
    {
        return $this->hasOne(Dispatcher::class);
    }

    public function targets(){
        return $this->belongsToMany(WalletTarget::class,'wallet_targets_clients','user_id','wallet_target_id')->withPivot(['points','achieved']);
    }
    public function active_targets(){
        return $this->targets()->whereDate('start_date','<=',now())->whereDate('end_date','>=',now())->where('achieved',0)->get();
    }

    public function banks()
    {
        return $this->hasOne(Bank::class,'user_id');
    }


    public function pricingHistories(){
        return $this->hasMany(PricingHistory::class);
    }

    public function client(){
        return $this->hasOne(Client::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class,'user_id');
    }

    public function suggestionUser(){
        return $this->hasMany(SuggestionUser::class);
    }

    public function clientAccounts(){
        return $this->hasMany(ClientAccount::class,'user_id','id');
    }


    public function receivesBroadcastNotificationsOn()
    {
        return 'App.Models.User.'.$this->id;
    }




    public function clientOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class,'user_id');
    }

    public function representativeOrders(): \Illuminate\Database\Eloquent\Relations\HasMany
    {
        return $this->hasMany(Order::class,'representative_id');
    }

}
