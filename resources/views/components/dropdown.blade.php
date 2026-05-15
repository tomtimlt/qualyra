@props(['align' => 'right', 'width' => '48'])

@php
    $alignClass = match ($align) {
        'left' => 'is-left',
        'top' => 'is-top',
        default => 'is-right',
    };
    $widthStyle = is_numeric($width) ? "width: {$width}px" : "width: {$width}";
@endphp

<div class="dropdown" x-data="{ open: false }" @click.outside="open = false" @close.stop="open = false">
    <div @click="open = ! open">
        {{ $trigger }}
    </div>

    <div x-show="open"
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95"
         x-transition:enter-end="opacity-100 scale-100"
         x-transition:leave="transition ease-in duration-100"
         x-transition:leave-start="opacity-100 scale-100"
         x-transition:leave-end="opacity-0 scale-95"
         class="dropdown__panel {{ $alignClass }}"
         style="display: none; {{ $widthStyle }}"
         @click="open = false">
        {{ $content }}
    </div>
</div>

@once
<style>
    .dropdown { position: relative; display: inline-block; }
    .dropdown__panel { position: absolute; z-index: 50; margin-top: 8px; background: var(--surface); border: 1px solid var(--hairline); border-radius: var(--r-sm); box-shadow: var(--shadow-2); padding: 4px 0; min-width: 192px; transform-origin: top right; }
    .dropdown__panel.is-right { right: 0; transform-origin: top right; }
    .dropdown__panel.is-left { left: 0; transform-origin: top left; }
    .dropdown__panel.is-top { left: 50%; transform: translateX(-50%); transform-origin: top center; }
</style>
@endonce
