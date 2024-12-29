<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'POKAREZ WEB') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=lobster:400|nunito-sans:400,500,600,700,800,900" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body>
    <section class="bg-white">
        <div class="container flex items-center justify-center min-h-screen px-6 py-12 mx-auto">
            <div class="w-full ">
                <div class="flex flex-col items-center mx-auto text-center">
                    <h1 class="mt-3 text-3xl font-bold text-orange-400 capitalize md:text-5xl">Halaman sedang dalam
                        tahap
                        pembangunan</h1>
                    <p class="mt-6 text-gray-700 text-lg md:text-2xl">Kami sedang mengerjakan halaman ini.</p>

                    <div class="flex items-center w-full mt-6 gap-x-3 shrink-0 sm:w-auto justify-center">
                        <button onclick="window.history.back()"
                            class="flex items-center justify-center w-1/2 px-5 py-2 text-base text-white transition-colors duration-200 bg-orange-400 border rounded-lg gap-x-2 sm:w-auto hover:bg-orange-300">
                            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"
                                stroke-width="1.5" stroke="currentColor" class="w-5 h-5 rtl:rotate-180">
                                <path stroke-linecap="round" stroke-linejoin="round"
                                    d="M6.75 15.75L3 12m0 0l3.75-3.75M3 12h18" />
                            </svg>
                            <span>Kembali</span>
                        </button>
                    </div>
                </div>

                <div class="relative w-full mt-8 lg:w-1/2 lg:mt-0 mx-auto">
                    <img class=" w-full lg:h-[32rem] h-80 md:h-96 rounded-lg object-cover object-center"
                        src="https://img.freepik.com/free-vector/computer-trouble-shooting-concept-illustration_114360-7376.jpg?t=st=1734457052~exp=1734460652~hmac=a08e27b2ebb1d6d7df4b16ee2f4ac7c51a6be22812851097f55d39a648cd6f3c&w=1380"
                        alt="" width="">
                </div>
            </div>
        </div>
    </section>
</body>

</html>
