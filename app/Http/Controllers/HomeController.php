<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index()
    {
        return view('welcome', [
            'episodes' => Episode::query()->orderBy('created_at', 'desc')->paginate(10),
        ]);
    }
}
