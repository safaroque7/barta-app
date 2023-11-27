<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Flasher\Prime\FlasherInterface;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PostController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
            ->orderBy('id', 'desc')
            ->select('posts.*', 'users.first_name', 'users.last_name', 'users.email')
            ->get();

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
        $posts = Post::create([
            'content' => $request->content,
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

        // dd($id);
        // $user = Auth::user();
        // Post::findOrFail($id)->with('user')->get();

        // $post = Post::find($id);
        $post = Post::findOrFail($id)->with('user')->first();
        // dd($post);
        return view('single-post', compact('post'));

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
    public function update($id, Request $request, FlasherInterface $flasherInterface)
    {
        $post = Post::find($id);
        $post->content = $request->content;
        sweetalert()->addSuccess('Your post has been updated.');
        $post->save();
        return redirect()->route('post.show', $post->id);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Post  $post
     * @return \Illuminate\Http\Response
     */
    public function destroy($id, FlasherInterface $flasher)
    {
        $post = Post::findOrFail($id);
        $post->deleteOrFail();
        sweetalert()->addSuccess('Your post has been deleted.');
        return redirect('/');
    }
}
