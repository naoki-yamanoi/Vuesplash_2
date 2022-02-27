<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Auth;

class Photo extends Model
{
    use HasFactory;

    /** プライマリキーの型を上書き */
    protected $keyType = 'string';

    // ページネーションの１ページあたりのデフォルト値
    protected $perPage = 6;

    /** JSONに含めるアクセサ */
    protected $appends = [
        'url', 'likes_count', 'liked_by_user',
    ];

    /** JSONに含める属性 */   //登録項目だけを JSON に含める。それ以外は含めない。
    protected $visible = [
        'id', 'owner', 'url', 'comments', 'likes_count', 'liked_by_user',
    ];

    /** JSONに含めない属性 */   //登録項目は JSON に含めない。それ以外は基本ルールに従う。
    // protected $hidden = [
    //     'user_id', 'filename',
    //     self::CREATED_AT, self::UPDATED_AT,
    // ];

    /** IDの桁数 */
    const ID_LENGTH = 12;

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (!Arr::get($this->attributes, 'id'))
        {
            $this->setId();
        }
    }

    /**
     * ランダムなID値をid属性に代入する
     */
    private function setId()
    {
        $this->attributes['id'] = $this->getRandomId();
    }

    /**
     * ランダムなID値を生成する
     * @return string
     */
    private function getRandomId()
    {
        $characters = array_merge(
            range(0, 9),
            range('a', 'z'),
            range('A', 'Z'),
            ['-', '_']
        );

        $length = count($characters);

        $id = "";

        for ($i = 0; $i < self::ID_LENGTH; $i++)
        {
            $id .= $characters[random_int(0, $length - 1)];
        }

        return $id;
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    // モデルクラスがコントローラーからレスポンスされて JSON に変換されるとき、このリレーション名 "owner" が反映される。
    public function owner()
    {
        // モデル名と関係のない名前を付ける場合は belongsTo などのメソッドの引数は省略できない。
        return $this->belongsTo('App\Models\User', 'user_id', 'id', 'users');
    }

    /**
     * アクセサ - url・・・外部からアクセスできるようにするもの。
     * アクセサは定義しただけではモデルの JSON 表現には現れないので、明示的に $appends プロパティに登録する必要がある。
     * @return string
     */
    public function getUrlAttribute()
    {
        // ファイルの公開 URL を返却
        return Storage::url('images/' . $this->attributes['filename']);
    }

    /**
     * リレーションシップ - commentsテーブル
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function comments()
    {
        return $this->hasMany('App\Models\Comment')->orderBy('id', 'desc');
    }

    /**
     * リレーションシップ - usersテーブル
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function likes()
    {
        // likes テーブルを中間テーブルとした、photos テーブルと users テーブルの多対多の関係。
        // likes テーブルに当たるモデルクラスは作成しない。
        // 外部キーしか中身のない中間テーブルの場合はモデルクラスは作成する必要のない場合が多い。
        // withTimestamps()・・・ likesテーブルにデータを挿入したとき、created_atとupdated_at カラムを更新させる。
        return $this->belongsToMany('App\Models\User', 'likes')->withTimestamps();
    }

    /**
     * アクセサ - likes_count
     * @return int
     */
    public function getLikesCountAttribute()
    {
        return $this->likes->count();
    }

    /**
     * アクセサ - liked_by_user
     * @return boolean
     */
    public function getLikedByUserAttribute()
    {
        if (Auth::guest())
        {
            return false;
        }

        // Laravelのコレクションメソッドcontainsを使い、ログインユーザーのIDと合致するいいねが含まれるか調べる。
        // likes リレーションから取得できるのはユーザーモデル（のコレクション）
        return $this->likes->contains(function ($user)
        {
            return $user->id === Auth::user()->id;
        });
    }
}
