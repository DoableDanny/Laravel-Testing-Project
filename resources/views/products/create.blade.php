<x-guest-layout>
    <div>
        <h1 class="mb-7 text-center text-2xl font-bold">Create a product</h1>

        <form method="POST" action="{{ route('products.store') }}">
            @csrf
            <div class='mb-3'>
                <x-input-label for='title' :value="__('Title')" />
                <x-text-input id="title" class="mt-1 block w-full" type="text" name="title" :value="old('title')"
                    required autofocus />
            </div>
            <div>
                <x-input-label for='price' :value="__('Price')" />
                <x-text-input id='price' class="mt-1 block w-full" type="number" name='price' :value="old('price')"
                    required />
            </div>

            <input class='mt-5 w-full cursor-pointer rounded bg-blue-600 p-4 text-white' type="submit" value="Create">

            @foreach ($errors->all() as $err)
                <p class='mt-3 text-red-500'>{{ $err }}</p>
            @endforeach
        </form>
    </div>
</x-guest-layout>
