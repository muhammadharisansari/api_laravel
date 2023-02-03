<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use App\Http\Resources\PostDetailResource;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    public function index()
    {
        $posts = Post::with('writer:id,username')->orderBy('id','DESC')->get();
        return PostResource::collection($posts->loadMissing(['comments:id,post_id,user_id,comments_content,created_at']));
    }

    public function show($id)
    {
        $post = Post::with('writer:id,username')->findOrFail($id);
        // return response()->json(['data'=>$post]);
        return new PostDetailResource($post->loadMissing(['comments:id,post_id,user_id,comments_content,created_at']));
    }

    public function store(Request $request)
    {
        $request->validate([
            'title'          => 'required|max:100',
            'news_content'   => 'required',
        ]);

        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id)
    {
        // dd('lolos middleware');
        $request->validate([
            'title'          => 'required|max:100',
            'news_content'   => 'required',
        ]);

        $post = Post::findOrFail($id);
        $post->update($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->delete();
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }
}

