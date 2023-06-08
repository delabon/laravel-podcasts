<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Episode') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <form method="post" action="{{ route('episode.update', [$podcast->id, $episode->id]) }}" class="space-y-6" enctype="multipart/form-data">
                            @csrf
                            @method('patch')

                            <div>
                                <label>
                                    <span>{{ __('Title') }}</span>
                                </label>

                                <input id="title" name="title" type="text" value="{{ $episode->title }}" class="mt-1 block w-full" />

                                @if ($errors->has('title'))
                                    <span class="text-danger">{{ $errors->first('title') }}</span>
                                @endif
                            </div>

                            <div>
                                <label>
                                    <span>{{ __('Description') }}</span>
                                </label>

                                <textarea id="description" name="description" class="mt-1 block w-full">{!! $episode->description !!}</textarea>

                                @if ($errors->has('description'))
                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                @endif
                            </div>

                            <div>
                                <label>
                                    <span>{{ __('MP3 File') }}</span>
                                </label>

                                <input type="file" id="file" name="file" class="mt-1 block w-full">

                                <button data-file="{{ $episode->file }}" class="border-gray-200 py-1 px-2 bg-gray-800 text-white rounded mt-2 mr-2 text-sm">Play</button>

                                @if ($errors->has('file'))
                                    <span class="text-danger">{{ $errors->first('file') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="bg-gray-800 text-white py-2 px-4 text-sm">Update</button>
                                <a href="{{ route('dashboard.podcast.show', $podcast->id) }}" class="underline-offset-auto">Cancel</a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
