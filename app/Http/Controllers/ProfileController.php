<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\User;
use App\Models\Comment;
use Illuminate\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests\ProfileUpdateRequest;

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
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $request->user()->fill($request->validated());

        if ($request->user()->isDirty('email')) {
            $request->user()->email_verified_at = null;
        }

        $request->user()->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
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
}
