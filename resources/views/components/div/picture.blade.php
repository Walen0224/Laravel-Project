<div
    {{ $attributes->merge(['type' => '', 'class' => 'grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 gap-4']) }}>
    {{ $slot }}
</div>
