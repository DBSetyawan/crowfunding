<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends \TCG\Voyager\Models\User
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        // 'id','name', 'email', 'password',''
        'id',
        'role_id',
        'name',
        'email',
        'avatar',
        'email_verified_at',
        'password',
        'password',
        'nama_cabang',
        'id_cabang',
        'additional_each_id',
        'groups_id',
        'remember_token',
        'settings',
        'created_at',
        'updated_at',
        'tempat_lahir',
        'tanggal_lahir',
        'hobi',
        'alamat',
        'urban_id',
        'pekerjaan',
        'posisi',
        'no_whatsapp',
        'url_facebook',
        'url_instagram',
        'url_twitter',
        'url_website',
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function setSettingsAttribute($value)
    {
            $this->attributes['settings'] = $value->toJson();
    }
}
