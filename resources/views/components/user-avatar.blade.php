@props(['name' => 'User'])

@php
    $initials = collect(explode(' ', trim($name)))
        ->map(fn ($part) => strtoupper(substr($part, 0, 1)))
        ->take(2)
        ->implode('');

    $colors = [
        'bg-pink-500', 'bg-purple-500', 'bg-indigo-500', 'bg-blue-500',
        'bg-teal-500', 'bg-green-500', 'bg-yellow-500', 'bg-orange-500', 'bg-red-500',
    ];

    $hash = 0;
    foreach (str_split($name) as $char) {
        $hash = ord($char) + (($hash << 5) - $hash);
    }
    $color = $colors[abs($hash) % count($colors)];
@endphp

<div {{ $attributes->merge(['class' => "w-7 h-7 rounded-full $color flex items-center justify-center text-white text-[10px] font-bold shrink-0"]) }}>
    {{ $initials }}
</div>