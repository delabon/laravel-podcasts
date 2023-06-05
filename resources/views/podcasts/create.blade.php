<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Create a Podcast') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            <div class="p-4 sm:p-8 bg-white shadow sm:rounded-lg">
                <div class="max-w-xl">
                    <section>
                        <form method="post" action="{{ route('podcast.store') }}" class="space-y-6">
                            @csrf

                            <div>
                                <label>
                                    <span>{{ __('Name') }}</span>
                                </label>

                                <input id="name" name="name" type="text" class="mt-1 block w-full" />

                                @if ($errors->has('name'))
                                    <span class="text-danger">{{ $errors->first('name') }}</span>
                                @endif
                            </div>

                            <div>
                                <label>
                                    <span>{{ __('Description') }}</span>
                                </label>

                                <textarea id="description" name="description" class="mt-1 block w-full"></textarea>

                                @if ($errors->has('description'))
                                    <span class="text-danger">{{ $errors->first('description') }}</span>
                                @endif
                            </div>

                            <div class="flex items-center gap-4">
                                <button type="submit" class="bg-gray-800 text-white py-2 px-4 text-sm">Create</button>
                                <a href="{{ route('podcast.index') }}" class="underline-offset-auto">Cancel</a>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
