<?php

namespace App\Http\Controllers;
use App\Models\Comment;

use Illuminate\Http\Request;

class CommentController extends Controller
{
    public function index()
    {
        return view('comment.index', [
            'Comments' => Comment::where("active", 1)->where("parent_id", 0)->orderBy("id", "DESC")->paginate(),
        ]);
    }
}
