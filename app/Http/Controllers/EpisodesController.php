<?php

namespace App\Http\Controllers;

use App\Models\Episode;
use App\Models\Podcast;
use App\Rules\PodcastName;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class EpisodesController extends Controller
{
    public function store(Podcast $podcast)
    {
        $data = request()->validate([
            'title' => ['required', new PodcastName()],
            'description' => ['required'],
            'file' => ['required', 'mimetypes:audio/mpeg']
        ]);

        $path = request()->file('file')->store('episodes');

        $podcast->episodes()->create(array_merge($data, ['file' => $path]));
    }

    public function update(Podcast $podcast, Episode $episode)
    {
        $data = request()->validate([
            'title' => ['required', new PodcastName()],
            'description' => ['required'],
            'file' => ['sometimes', 'mimetypes:audio/mpeg']
        ]);

        Auth::user()->podcasts()->findOrFail($podcast->id);
        $podcast->episodes()->findOrFail($episode->id);

        if (request()->has('file')) {
            if (!request()->file('file')) {
                throw ValidationException::withMessages([
                    'file' => 'Invalid audio file.'
                ]);
            }

            $path = request()->file('file')->store('episodes');
        } else {
            $path = $episode->file;
        }

        $episode->update(array_merge($data, ['file' => $path]));
    }

    public function delete(Podcast $podcast, Episode $episode)
    {
        Auth::user()->podcasts()->findOrFail($podcast->id);
        $podcast->episodes()->findOrFail($episode->id);

        $episode->delete();
    }
}
