<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ $podcast->name }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <div class="mb-4">
                        @foreach($episodes as $episode)
                            <div>
                                <h3 class="font-bold underline"><a href="/dashboard/podcasts/{{ $podcast->id }}/episode/{{ $episode->id }}">{{ $episode->title }}</a></h3>
                                <p>{{ $episode->description }}</p>

                                <div class="flex mt-2 items-center">
                                    <button data-file="{{ $episode->file }}" class="border-gray-200 py-1 px-2 bg-gray-800 text-white rounded text-sm">Play</button>

                                    <a href="{{ route('dashboard.episode.edit', [$podcast->id, $episode->id]) }}" class="ml-2 underline text-sm">Edit</a>

                                    <form method="post" action="{{ route('episode.delete', [$podcast->id, $episode->id]) }}" class="inline-flex">
                                        @csrf
                                        @method('delete')
                                        <button type="submit" class="ml-2 underline text-sm">Delete</button>
                                    </form>
                                </div>
                            </div>
                            <hr class="mt-4 mb-4">
                        @endforeach

                        <div>
                            {!! $episodes->links() !!}
                        </div>
                    </div>

                    <a href="{{ route('dashboard.episode.create', $podcast->id) }}" class="underline">Create an episode</a> |
                    <a href="{{ route('dashboard.podcast.index', $podcast->id) }}" class="underline">Podcasts</a>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
