<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use Illuminate\Http\Request;

class DashboardPodcastsController extends Controller
{
    public function index()
    {
        return view('dashboard.podcasts.index', [
            'podcasts' => Podcast::all(),
        ]);
    }

    public function show(Podcast $podcast)
    {
        return view('dashboard.podcasts.show', [
            'podcast' => $podcast,
            'episodes' => Episode::query()->where('podcast_id', '=', $podcast->id)->get()
        ]);
    }

    public function create()
    {
        return view('dashboard.podcasts.create');
    }
}
