<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;

class DashboardEpisodesController extends Controller
{
    public function create(Podcast $podcast)
    {
        return view('dashboard.episodes.create', [
            'podcast' => $podcast
        ]);
    }

    public function edit(Podcast $podcast, Episode $episode)
    {
        return view('dashboard.episodes.edit', [
            'podcast' => $podcast,
            'episode' => $episode
        ]);
    }
}
