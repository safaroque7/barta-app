<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        // $users = User::with('post')->get();
        // dd($users);

        $posts = Post::with('user')->orderBy('id', 'desc')->get();
        // dd($posts);

        // $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
        //     ->orderBy('id', 'desc')
        //     ->select('posts.*', 'users.first_name', 'users.last_name', 'users.email')
        //     ->get();

        // dd($posts);

        // $posts = Post::with('user')->select('posts.*', 'users.first_name')->get();

        return view('welcome', compact('posts'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $file = $request->file('picture');
        $fileName = time() . '_' . $file->getClientOriginalname();
        $file->move(public_path('uploads'), $fileName);

        if ($file) {
            //your upload code here
            $path = Storage::putFile('public', $file);
        }
        dd($path);

        $posts = Post::create([
            'content' => $request->content,
            'picture' => $path,
            'user_id' => Auth::user()->id,

        ]);

        return back();
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        // it is called eager loading
        // return $posts = Post::with('user', 'comments')->where('id', $id)->orderBy('id', 'desc')->first();
        // Abvoe return means show data json base

        $posts = Post::with('user', 'comments.user')->where('id', $id)->orderBy('id', 'desc')->first();

        // $post = Post::findOrFail($id)->with('user')->first();
        return view('single-post', compact('posts'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $post = Post::find($id);
        return view('edit-post', compact('post'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function update($id, Request $request)
    {
        $post = Post::find($id);
        $post->content = $request->content;
        // sweetalert()->addSuccess('Your post has been updated.');
        $post->save();
        return redirect()->route('post.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->deleteOrFail();
        // sweetalert()->addSuccess('Your post has been deleted.');
        return redirect('/');
    }
}
