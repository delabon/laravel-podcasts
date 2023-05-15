<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EpisodesController extends Controller
{
    public function store()
    {
        Episode::create([
            'title' => request()->get('title'),
            'description' => request()->get('description'),
            'user_id' => Auth::user()->id,
        ]);
    }
}
