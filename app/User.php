<?php

namespace App;

use App\Reply;
use App\Activity;
use Illuminate\Notifications\Notifiable;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use Notifiable;
    public function getRouteKeyName()
    {
        return 'name';
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'avatar_path'
    ];

    protected $casts = [
        'confirmed' =>  'boolean'
    ];
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'email',
    ];

    public function threads()
    {
        return $this->hasMany(Thread::class);
    }

    public function lastReply()
    {
        return $this->hasOne(Reply::class)->latest();
    }

    public function activity()
    {
        return $this->hasMany(Activity::class);
    }

    public function getAvatarPathAttribute($avatar)
    {
        return "/storage/" . ($avatar ?: "avatars/default.png");
    }

    public function confirm()
    {
        $this->confirmed = true;
        $this->confirmation_token = null;
        
        $this->save();
    }

    public function isAdmin()
    {
        return in_array($this->name, ['Mosaab Muhammed', 'JohnDoe']);
    }
}
