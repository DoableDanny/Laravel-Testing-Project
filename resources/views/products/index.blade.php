<x-guest-layout>
    <x-slot name="header">
        <h2 class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200">
            {{ __('Products') }}
        </h2>
    </x-slot>

    @if (auth()->user()->is_admin)
        <a href={{ route('products.create') }}>Add new product</a>
    @endif

    <div class="py-12">
        <div class="mx-auto max-w-7xl sm:px-6 lg:px-8">
            <div class="overflow-hidden bg-white shadow-sm dark:bg-gray-800 sm:rounded-lg">
                <div class="p-6 text-gray-900 dark:text-gray-100">
                    @forelse ($products as $p)
                        <div class="mb-4 flex justify-between gap-4">
                            <p class="mr-10">{{ __($p->title) }}</p>
                            <p>${{ __($p->price) }}</p>
                            <p>Eur {{ __($p->price_eur) }}</p>

                            @if (auth()->user()->is_admin)
                                <a class='rounded bg-green-500 p-2 text-white'
                                    href="{{ route('products.edit', $p) }}">Edit</a>

                                <form action="{{ route('products.destroy', $p) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <x-danger-button onclick="return confirm('Are you sure?')">
                                        Delete</x-danger-button>
                                </form>
                            @endif

                        </div>
                    @empty
                        <p>No products found</p>
                    @endforelse
                </div>
            </div>
        </div>
        <br>
        <br>
        {{ $products->links() }}
    </div>
</x-guest-layout>
