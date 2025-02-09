@props(['value'])

<label {{ $attributes->merge(['class' => 'block font-semibold text-sm md:text-base text-[#10453A]']) }}>
    {{ $value ?? $slot }}
</label>
