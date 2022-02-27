<?php

namespace App\Http\Controllers;


use App\Models\Photo;
use App\Models\Comment;
use Illuminate\Http\Request;
use App\Http\Requests\StorePhoto;
use App\Http\Requests\StoreComment;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        // 認証が必要
        $this->middleware('auth')->except(['index', 'download', 'show']);
    }

    /**
     * 写真一覧
     */
    public function index()
    {
        // with メソッドは、リレーションを事前にロードしておくメソッド
        // ownerリレーションとlikesリレーションをロード
        $photos = Photo::with(['owner', 'likes'])->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return $photos;
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Response
     */
    public function create(StorePhoto $request)
    {
        $requestPhoto = $request->photo;
        // 拡張子取得
        $extension = $request->photo->extension();
        $photo = new Photo();
        $photoId = $photo->id;
        // インスタンス生成時に割り振られたランダムなID値と本来の拡張子を合わせてファイル名とする
        $photoName = $photo->id . '.' . $extension;
        // ファイルを保存
        // 2番目の引数は、ファイルオブジェクトではなくファイルの内容を指定する。
        Storage::put('public/images/' . $photoName, file_get_contents($requestPhoto));
        // DBにも写真名を挿入
        $photo->filename = $photoName;


        DB::beginTransaction();

        try
        {
            Auth::user()->photos()->save($photo);
            DB::commit();
        }
        catch (\Exception $exception)
        {
            DB::rollBack();
            // DBとの不整合を避けるためアップロードしたファイルを削除
            Storage::delete('public/images/' . $photo->filename);
            throw $exception;
        }

        // なぜかAuth::user()->photos()->save($photo);のところで$photo->idが0になってしまうので、一時保管し、再代入。
        $photo->id = $photoId;
        // リソースの新規作成なので
        // レスポンスコードは201(CREATED)を返却する
        return response($photo, 201);
    }

    /**
     * 写真詳細
     * @param string $id
     * @return Photo
     */
    public function show(string $id)
    {
        $photo = Photo::where('id', $id)->with(['owner', 'comments.author', 'likes'])->first();
        // 写真データが見つからなかった場合は 404 を返却
        return $photo ?? abort(404);
    }

    /**
     * 写真ダウンロード
     * @param Photo $photo
     * @return \Illuminate\Http\Response
     */
    public function download(Photo $photo)
    {
        // 写真の存在チェック
        if (!Storage::exists($photo->filename))
        {
            abort(404);
        }

        $disposition = 'attachment; filename="' . $photo->filename . '"';
        $headers = [
            'Content-Type' => 'application/octet-stream',
            // ダウンロードさせるために保存ダイアログを開くようにブラウザに指示
            'Content-Disposition' => $disposition,
        ];

        return response(Storage::get($photo->filename), 200, $headers);
    }

    /**
     * コメント投稿
     * @param Photo $photo
     * @param StoreComment $request
     * @return \Illuminate\Http\Response
     */
    public function addComment(Photo $photo, StoreComment $request)
    {
        $comment = new Comment();
        $comment->content = $request->get('content');
        $comment->user_id = Auth::user()->id;
        $photo->comments()->save($comment);

        // authorリレーションをロードするためにコメントを取得しなおす
        $new_comment = Comment::where('id', $comment->id)->with('author')->first();

        return response($new_comment, 201);
    }

    /**
     * いいね
     * @param string $id
     * @return array
     */
    public function like(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo)
        {
            abort(404);
        }

        // 何回実行しても1個しかいいねが付かないように、まず特定の写真およびログインユーザーに紐づくいいねを削除してから追加。
        $photo->likes()->detach(Auth::user()->id); // detach()・・・削除
        $photo->likes()->attach(Auth::user()->id); // attach()・・・追加

        return ["photo_id" => $id];
    }

    /**
     * いいね解除
     * @param string $id
     * @return array
     */
    public function unlike(string $id)
    {
        $photo = Photo::where('id', $id)->with('likes')->first();

        if (!$photo)
        {
            abort(404);
        }

        $photo->likes()->detach(Auth::user()->id);

        return ["photo_id" => $id];
    }
}
