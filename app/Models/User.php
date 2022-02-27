<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    // フィールドへの代入を許可
    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    // データ取得しないフィールド
    // protected $hidden = [
    //     'password',
    //     'remember_token',
    // ];

    // データ取得するフィールド
    protected $visible = [
        'name',
    ];
    // ↓hiddenで書く場合。
    // protected $hidden = [
    //     'id', 'email', 'email_verified_at', 'password', 'remember_token',
    //     self::CREATED_AT, self::UPDATED_AT,
    // ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    // DBから取得したデータを自動変換
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * リレーションシップ - photosテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function photos()
    {
        return $this->hasMany('App\Models\Photo');
    }
}
