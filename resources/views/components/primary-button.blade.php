<button {{ $attributes->merge(['type' => 'submit', 'class' => 'inline-flex items-center px-4 py-2 bg-gradient-to-r from-orange-500 to-orange-600 border border-transparent rounded-xl font-semibold text-sm text-white uppercase tracking-wider hover:from-orange-600 hover:to-orange-700 focus:outline-none focus:ring-2 focus:ring-orange-300 focus:ring-offset-2 transition ease-in-out duration-150 shadow-md hover:shadow-lg active:scale-95']) }}>
    {{ $slot }}
</button>
