<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="dark">
    <head>
        @include('partials.head')
    </head>
    <body class="min-h-screen bg-white antialiased dark:bg-gradient-to-b dark:from-neutral-950 dark:to-neutral-900">
        <div class="relative grid grid-cols-1 lg:grid-cols-2 h-dvh flex-col items-center justify-center px-8 sm:px-0 lg:max-w-none lg:px-0">

            <div class="relative hidden lg:flex h-full flex-col p-10 text-white dark:border-e dark:border-neutral-800"
                 style="background-image: url('/fondo.jpg'); background-size: cover; background-position: center;">

                <div class="absolute inset-0 bg-black/60"></div>

                <a href="{{ route('home') }}" class="relative z-20 flex items-center text-lg font-medium" wire:navigate>
                    <span class="flex h-10 w-10 items-center justify-center rounded-md">
                        <flux:icon name="cog" class="h-8 w-8 text-blue-600" />
                    </span>
                    {{ config('app.name', 'Laravel') }}
                </a>

            </div>

            <div class="w-full py-10 lg:p-8 flex justify-center items-center">
                <div class="mx-auto flex w-full flex-col justify-center space-y-6 sm:w-[350px]">

                    {{ $slot }}
                </div>
            </div>
        </div>
        @fluxScripts
    </body>
</html>
