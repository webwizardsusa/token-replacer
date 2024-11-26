@props([
    'name',
    'title' => null,
    'actions' => null,
    ]
)
<template name="{{ $name }}" title="{{ $title }}">
    {{ $slot }}
    @if($actions)
        <div class="tiptap-popper-actions">
            {{ $actions }}
        </div>
        @endif
</template>
