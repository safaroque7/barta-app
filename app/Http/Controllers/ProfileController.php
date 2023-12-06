<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Post;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Illuminate\View\View;

class ProfileController extends Controller
{

    public function show($id)
    {
        //to get user id
        $user = User::find($id);

        // to join post with users
        $posts = Post::join('users', 'posts.user_id', '=', 'users.id')
            ->orderBy('id', 'desc')
            ->select('posts.*', 'users.first_name', 'users.last_name', 'users.email')
            ->where('user_id', '=', $id)
            ->get();

        // to get current users post count
        $postsCount = Post::where('user_id', '=', $id)->count();

        // for comments count
        $commentCount = Comment::where('user_id', '=', $id)->count();

        // dd($user);
        return view('profile', compact('user', 'posts', 'postsCount', 'commentCount'));
    }

    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('edit-profile');
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {

        $file = $request->file('avatar');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $file->move(public_path('uploads/profile', $fileName));

        $user = Auth::user();
        // dd($user);
        
        
        $user->first_name = $request->first_name;
        $user->last_name = $request->last_name;
        $user->email = $request->email;
        $user->avatar = $fileName;
        $user->bio = $request->bio;
        $user->password = $request->password;

        $user->save();
        return back();

        // return $data;

        // $request = $this->validate(request(), [
        //     'first_name' =>'required|string|max:255',
        //     'last_name' =>'required|string|max:255',
        //     'email' =>'required|string|email|max:255|unique:users,email',
        //     'password' => '[passwords]|password',
        // ]);
        // if ($request['password']!= null) {
        //     $request['password'] = Hash::make($request['password']);
        // } else {
        //     unset($request['password']);
        // }
        // $user->update($request);
        // return redirect()->route('profile.show', $user->id);

        // $request->first_name;

        // $request->user()->fill($request->validated());

        // if ($request->user()->isDirty('email')) {
        //     $request->user()->email_verified_at = null;
        // }

        // $request->user()->save();

        // return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current-password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    public function store(Request $request)
    {
        $posts = Post::created([
            'content' => $request->content,
            'user_id' => Auth::user()->id,
        ]);

        // dd($posts);

        return back();
    }

    public function profileUpdate()
    {
        return 'hi';
    }
}
