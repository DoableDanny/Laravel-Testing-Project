<x-guest-layout>
    <h1 class="mb-7 text-center text-2xl font-bold">Edit Product</h1>

    <form method="POST" action="{{ route('products.update', $product) }}">
        @csrf
        @method('PUT')

        <div class='mb-3'>
            <x-input-label for='title' :value="__('Title')" />
            <x-text-input id="title" class="mt-1 block w-full" type="text" name="title" :value="$product->title" required
                autofocus />
        </div>
        <div>
            <x-input-label for='price' :value="__('Price')" />
            <x-text-input id='price' class="mt-1 block w-full" type="number" name='price' :value="$product->price"
                required />
        </div>

        <x-primary-button class="mt-5">
            {{ __('Save') }}
        </x-primary-button>

        @foreach ($errors->all() as $err)
            <p class='mt-3 text-red-500'>{{ $err }}</p>
        @endforeach
    </form>
</x-guest-layout>
