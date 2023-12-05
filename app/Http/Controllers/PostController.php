<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Post;
use App\Models\User;
use Flasher\Prime\FlasherInterface;
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

        // $fileName = time() . '.' . $request->picture->extension();
        // // $request->picture->storeAs('images/', $fileName);
        // $request->picture->store('images/', $fileName);

        // $file->move(public_path('uploads'), $fileName);

        $file = $request->file('picture');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads'), $fileName);

        $posts = Post::create([
            'content' => $request->content,
            'picture' => $fileName,
            'user_id' => Auth::user()->id,
        ]);

        // dd($path);

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
    public function update($id, Request $request, FlasherInterface $flasherInterface)
    {
        $oldPicture = Post::find($id);
        $fileName = $oldPicture->picture;

        if ($request->picture) {
            // unlink($request->picture);
            unlink(public_path('uploads/' . $fileName));
            $file = $request->file('picture');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $file->move(public_path('uploads'), $fileName);
        }

        $post = Post::find($id);
        $post->content = $request->content;
        $post->picture = $fileName;
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
    public function destroy($id)
    {
        $post = Post::findOrFail($id);
        $post->deleteOrFail();
        // sweetalert()->addSuccess('Your post has been deleted.');
        return redirect('/');
    }

    public function search(Request $request)
    {
        $searchItem = $request->search;
        $searchResults = User::where('first_name', 'like', '%' . $searchItem . '%')
            ->orWhere('last_name', 'like', '%' . $searchItem . '%')
            ->orWhere('email', 'like', '%' . $searchItem . '%')
            ->get();
        // $posts = Post::with('user')->orderBy('id', 'desc')->get();

        // return $searchResults;

        return view('users', compact('searchResults'));

        // $searchItem = $request->search;
    }
}
