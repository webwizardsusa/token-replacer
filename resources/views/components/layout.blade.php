@props([
    'title',
    'heading' => null
])
    <!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name') }} - {{ $title }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet"/>

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased dark:bg-gray-950 dark:text-white/75">
<section class="">
    <div class="border-b">

        <header
            class="p-4 sm:px-6  flex items-center w-full bg-white dark:bg-gray-950 justify-between">
            <div class="flex-1">
                <a class="break-words"
                   aria-label="TailwindBlog"
                   href="/">
                    <div class="flex items-center justify-between">
                        <div class="mr-3 lg:text-4xl font-extrabold md:text-3xl">
                            {{ config('app.name') }}
                        </div>
                    </div>
                </a>
            </div>
            <div class="flex gap-3">

                <div
                    class="relative"
                    x-data="themeSelector()"
                    @click.outside="dropdownOpen = false"
                >
                    <button @click.prevent="dropdownOpen = !dropdownOpen">Theme</button>
                    <div x-show="dropdownOpen"
                         class="absolute left-0 border py-2 shadow rounded-lg bg-white dark:bg-gray-950"
                         style="top:100%;">
                        @foreach(['dark', 'light', 'auto'] as $style)
                            <div class="mb-2 cursor-pointer px-5 py-2"
                                 @click="theme='{{ $style }}'"
                                 :class="{
                                        'bg-primary-500 text-white': theme === '{{ $style }}'
                                }"
                            >{{ \Illuminate\Support\Str::ucfirst($style) }}
                            </div>
                        @endforeach

                    </div>

                </div>

                @if(auth()->user())
                    <a class="block font-medium text-gray-900 hover:text-primary-500 dark:text-gray-100 dark:hover:text-primary-400"
                       href="/admin">Admin</a>
                    @else
                    <a class="block font-medium text-gray-900 hover:text-primary-500 dark:text-gray-100 dark:hover:text-primary-400"
                       href="/admin">Login</a>
                    @endif

            </div>
        </header>
    </div>
    <main class="mx-auto w-full max-w-7xl p-4">
        @if($heading!==false)
            <h1 class="text-4xl font-bold tracking-tight text-gray-900 dark:text-gray-100 sm:leading-10 md:leading-14">
                {{ $heading ?? $title }}</h1>
        @endif
        <section class="main-section mt-4">
            {{ $slot }}
        </section>

    </main>
</section>
<footer class="border-t py-16 text-center text-sm text-black dark:text-white/70">
    Filapress is under active development by WebWizardsUSA
</footer>
</body>
</html>
