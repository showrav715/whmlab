<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function index()
    {
        $pageTitle = 'Withdraw History';
        // Add your withdraw logic here
        return view('user.withdraw.index', compact('pageTitle'));
    }

    public function create()
    {
        $pageTitle = 'New Withdraw';
        // Add your withdraw creation logic here
        return view('user.withdraw.create', compact('pageTitle'));
    }

    public function store(Request $request)
    {
        // Add your withdraw store logic here
        $notify[] = ['success', 'Withdraw request submitted successfully'];
        return back()->withNotify($notify);
    }

    public function show($id)
    {
        $pageTitle = 'Withdraw Details';
        // Add your withdraw show logic here
        return view('user.withdraw.show', compact('pageTitle'));
    }
}