<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Comment;
use App\Models\CommentShopItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{

    public static $items_on_page = 15;

    /**
     * Display a listing of the resource.
     */
    public function index(Request $request)
    {

        $parent = $request->parent_id ?? 0;

        return view('admin.comment.index', [
            'breadcrumbs' => self::breadcrumbs($parent > 0 ? Comment::find($parent) : false),
            'comments' => Comment::where("parent_id", $request->parent_id ?? 0)->orderBy("id", "Desc")->paginate(self::$items_on_page),
            'parent_id' => $request->parent_id ?? 0,
        ]);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

        $parent = $request->parent_id ?? 0;

        return view('admin.comment.create', [
            'breadcrumbs' => self::breadcrumbs($parent > 0 ? Comment::find($parent) : false, [], true),
            'parent_id' => $request->parent_id ?? 0,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        return $this->saveComment($request);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(Comment $comment)
    {

        return view('admin.comment.edit', [
            'comment' => $comment,
            'breadcrumbs' => self::breadcrumbs($comment, [], true),
        ]);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Comment $comment)
    {
        return $this->saveComment($request, $comment);
    }

    public function saveComment(Request $request, $comment = false)
    {
        if (!$comment) {
            $comment = new Comment();
        }

        $user = Auth::user();

        $comment->subject = $request->subject;
        $comment->text = $request->text;
        $comment->author = $request->author;
        $comment->email = $request->email;
        $comment->phone = $request->phone;
        $comment->grade = $request->grade;
        $comment->client_id = 0;
        $comment->user_id = $user->id;
        $comment->active = $request->active;
        $comment->parent_id = $request->parent_id ?? 0;

        $comment->save();

        if (!is_null($CommentShopItem = CommentShopItem::where("comment_id", $comment->id)->first())) {
            $CommentShopItem->shop_item_id = $request->shop_item_id;
            $CommentShopItem->save();
        } else if (!empty($request->shop_item_id)) {
            $CommentShopItem = new CommentShopItem();
            $CommentShopItem->comment_id = $comment->id;
            $CommentShopItem->shop_item_id = $request->shop_item_id;
            $CommentShopItem->save();
        }

        $message = "Комментарий был успешно сохранен!";
        
        if ($request->apply) {
            return redirect()->to(route("comment.index") . ($comment->parent_id > 0 ? "?parent_id=" . $comment->parent_id : ""))->withSuccess($message);
        } else {
            return redirect()->back()->withSuccess($message);
        }
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Comment $comment)
    {

        $this->remove($comment);

        return redirect()->back()->withSuccess("Комментарий был успешно удален!");
    }

    public function remove(Comment $comment)
    {

        $id = $comment->id;

        if (!is_null($comment->CommentShopItem)) {
            $comment->CommentShopItem->delete();
        }

        $comment->delete();

        if ($Comments = Comment::where("parent_id", $id)->get()) {
            foreach ($Comments as $comment) {
                $this->remove($comment);
            }
        }
    }

    public static function breadcrumbs($comment, $aResult = array(), $lastItemIsLink = false)
    {
        if ($comment) {

            $Result["name"] = $comment->subject . " [#id " . $comment->id . "]";
            $Result["url"] = '';
            if ($lastItemIsLink && count($aResult) == 0) {
                $Result["url"] = route("comment.index") . '?parent_id=' . $comment->id;
            } else if ($lastItemIsLink === false && count($aResult) > 0 && $comment->parent_id == 0) {
                $Result["url"] = route("comment.index") . '?parent_id=' . $comment->id;
            }
            array_unshift($aResult, $Result);

            if ($comment->parent_id > 0) {
                return self::breadcrumbs(Comment::find($comment->parent_id), $aResult, false);
            } else {

                $Result["url"] = route("comment.index");
                $Result["name"] = 'Комментарии';

                array_unshift($aResult, $Result);

                return $aResult;
            }

        } else {
            //
            $Result["url"] = route("comment.index");
            $Result["name"] = 'Комментарии';

            array_unshift($aResult, $Result);

            return $aResult;
        }

        return $aResult;
    }
}
