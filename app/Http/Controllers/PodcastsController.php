<?php

namespace App\Http\Controllers;

use App\Models\Podcast;
use App\Rules\PodcastName;
use Illuminate\Support\Facades\Auth;

class PodcastsController extends Controller
{
    public function store()
    {
        Auth::user()->podcasts()->create($this->validateRequest());

        return redirect()->route('dasbboard.podcast.index');
    }

    public function update(Podcast $podcast)
    {
        $podcast = Auth::user()->podcasts()->findOrFail($podcast->id);

        $podcast->update($this->validateRequest());
    }

    public function delete(Podcast $podcast)
    {
        $podcast = Auth::user()->podcasts()->findOrFail($podcast->id);
        $podcast->delete();
    }

    /**
     * @return array
     */
    protected function validateRequest(): array
    {
        return request()->validate([
            'name' => ['required', new PodcastName()],
            'description' => ['required'],
        ]);
    }
}
