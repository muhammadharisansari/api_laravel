<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\PostResource;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\PostDetailResource;
use Illuminate\Validation\Rules\Exists;

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
        // return $request->file;
        $request->validate([
            'title'          => 'required|max:100',
            'news_content'   => 'required',
        ]);

        if ($request->file) {
            $extension = $request->file->extension();

            $name = uniqid().'.'.$extension;
            $path = Storage::putFileAs('images', $request->file, $name);
            $request['image'] = url('storage/'.$path);
        }

        $request['author'] = Auth::user()->id;
        $post = Post::create($request->all());
        return new PostDetailResource($post->loadMissing('writer:id,username'));
    }

    public function update(Request $request, $id)
    {
        // dd($request->all());
        $request->validate([
            'title'          => 'required|max:100',
            'news_content'   => 'required',
        ]);
        
        $post = Post::findOrFail($id);

        if ($request->file) {

            if ($post->image != null) {
                // Path image Lama
                $imageLama = public_path('storage/images/' . substr($post->image,37));
                // Cek Apakah ada filenya
                if(File::exists($imageLama)){
                    // Jika File tersebut ada hapus File tersebut
                    File::delete($imageLama);
                }
            }

            $extension = $request->file->extension();

            $name = uniqid().'.'.$extension;
            $path = Storage::putFileAs('images', $request->file, $name);
            $request['image'] = url('storage/'.$path);
        }

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

