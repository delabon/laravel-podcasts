<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Your Podcasts') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <section>
                    <div class="mb-4">
                        @foreach($podcasts as $podcast)
                            <div>
                                <h3 class="font-bold underline"><a href="/dashboard/podcasts/{{ $podcast->id }}">{{ $podcast->name }}</a></h3>
                                <p>{{ $podcast->description }}</p>
                            </div>
                            <hr class="mt-4 mb-4">
                        @endforeach
                    </div>

                    <a href="{{ route('dashboard.podcast.create') }}" class="underline">Create a podcast</a>
                </section>
            </div>
        </div>
    </div>
</x-app-layout>
