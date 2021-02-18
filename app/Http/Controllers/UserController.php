<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;

class UserController extends Controller
{
    public function show(string $name)
    {
        $user = User::where('name',$name)->first();

        return view('users.show', [
            'user' => $user,
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
