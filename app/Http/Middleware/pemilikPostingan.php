<?php

namespace App\Http\Middleware;

use App\Models\Post;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class pemilikPostingan
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // dd('pemilik postingan');
        
        $currentUser = Auth::user()->id;
        $post = Post::findOrFail($request->id);

        if ($post->author != $currentUser) {
            return response()->json(['message' =>'data forbidden. The data is not yours.'],403);
        } else {
            return $next($request);
        }
        

    }
}
