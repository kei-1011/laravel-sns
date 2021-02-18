<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function show(string $name)
    {
        $user = User::where('name',$name)->first();

        $articles = $user->articles->sortByDesc('created_at');

        return view('users.show', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    public function likes(string $name)
    {
        $user = User::where('name', $name)->first();

        $articles = $user->likes->sortByDesc('created_at');

        return view('users.likes', [
            'user' => $user,
            'articles' => $articles,
        ]);
    }

    /**
     *  フォロー
     */
    public function follow(Request $request, string $name)
    {
        $user = User::where('name',$name)->first();

        if($user->id === $request->user()->id)
        {
            return abort('404','Cannot follow yourself');
        }

        $request->user()->followings()->detach($user);
        $request->user()->followings()->attach($user);

        return ['name' =>$user];
    }

    /**
     * フォロー解除　
     */
    public function unfollow(Request $request, string $name)
    {
        $user = User::where('name',$name)->first();

        if($user->id === $request->user()->id)
        {
            return abort('404','Cannot follow yourself');
        }

        $request->user()->followings()->detach($user);

        return ['name' =>$user];
    }
}
