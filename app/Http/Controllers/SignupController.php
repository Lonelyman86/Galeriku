<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class SignupController extends Controller
{
    public function index()
    {
        return view('layouts.sign-up', [
            'title' => 'Sign-up',
            'active' => 'sign-up'
        ]);
    }

    public function store(Request $request)
    {
        
        $validatedData = $request->validate([
            'fullname' => 'required|max:45',
            'name' => 'required|min:3|max:10',
            'address' => 'required',
            'username' => ['required', 'min:7', 'max:15', 'unique:users'],
            'email' => 'required|email:dns|unique:users',
            'password' => 'required|min:7|max:15' 
        ]);


        $validatedData['password'] = Hash::make($validatedData['password']);

        User::create($validatedData);
        return redirect('/sign-in')->with('success', 'Registration successfull! Welcome to the club!');
    }
}