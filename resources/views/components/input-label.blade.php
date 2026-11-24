@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-medium text-sm text-[#294D61]']) }}>
    {{ $value ?? $slot }}
</label>
