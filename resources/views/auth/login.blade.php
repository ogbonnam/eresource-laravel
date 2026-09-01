<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | eResource</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen">

    {{-- Background --}}
    <div
        class="relative min-h-screen bg-cover bg-center bg-no-repeat"
        style="background-image: url('{{ asset('storage/nh.png') }}');"
    >

        {{-- Dark overlay --}}
        <div class="absolute inset-0 bg-black/55"></div>

        {{-- Main content --}}
        <div class="relative z-10 min-h-screen flex items-center justify-center px-5 py-10">

            <div class="w-full max-w-md">

                {{-- Login Card --}}
                <div class="rounded-3xl bg-white/95 backdrop-blur-xl shadow-2xl
                            border border-white/40 overflow-hidden">

                    <div class="px-7 py-9 sm:px-10 sm:py-10">

                        {{-- Logo / Brand --}}
                        <div class="text-center">

                            <div class="mx-auto mb-5 flex h-20 w-20 items-center justify-center
                                        rounded-2xl bg-indigo-600 shadow-lg shadow-indigo-600/30">

                                <svg
                                    class="h-10 w-10 text-white"
                                    fill="none"
                                    stroke="currentColor"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.8"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M12 6.253v13m0-13C10.832 5.477
                                           9.246 5 7.5 5S4.168 5.477 3 6.253v13
                                           C4.168 18.477 5.754 18 7.5 18
                                           s3.332.477 4.5 1.253m0-13
                                           C13.168 5.477 14.754 5 16.5 5
                                           c1.746 0 3.332.477 4.5 1.253v13
                                           C19.832 18.477 18.246 18
                                           16.5 18c-1.746 0-3.332.477-4.5 1.253"
                                    />
                                </svg>

                            </div>

                            <h1 class="text-3xl font-extrabold tracking-tight text-gray-900">
                                NH eResource
                            </h1>

                            <p class="mt-2 text-sm font-medium text-gray-500">
                                Digital Learning Platform
                            </p>

                        </div>


                        {{-- Welcome message --}}
                        <div class="mt-9 mb-7 text-center">

                            


                        {{-- Google Login --}}
                        <a
                            href="{{ route('auth.google') }}"
                            class="group flex w-full items-center justify-center gap-3
                                   rounded-xl border border-gray-300
                                   bg-white px-5 py-3.5
                                   text-sm font-semibold text-gray-700
                                   shadow-sm
                                   transition-all duration-200
                                   hover:-translate-y-0.5
                                   hover:border-gray-400
                                   hover:bg-gray-50
                                   hover:shadow-md
                                   focus:outline-none focus:ring-4
                                   focus:ring-indigo-500/20"
                        >

                            {{-- Google Icon --}}
                            <svg
                                class="h-5 w-5 shrink-0"
                                viewBox="0 0 24 24"
                                fill="none"
                                xmlns="http://www.w3.org/2000/svg"
                            >
                                <path
                                    d="M21.8055 12.2303C21.8055 11.5303 21.7496 10.8553 21.6473 10.2053H12.25V14.0303H17.5227C17.2955 15.2603 16.6064 16.3053 15.5682 17.0103V19.5103H18.7364C20.5909 17.8053 21.8055 15.2903 21.8055 12.2303Z"
                                    fill="#4285F4"
                                />

                                <path
                                    d="M12.25 21.9999C14.95 21.9999 17.2136 21.1049 18.7364 19.5099L15.5682 17.0099C14.7045 17.5899 13.5955 17.9349 12.25 17.9349C9.64545 17.9349 7.43636 16.2099 6.67727 13.8899H3.40227V16.4699C4.91818 19.7349 8.40455 21.9999 12.25 21.9999Z"
                                    fill="#34A853"
                                />

                                <path
                                    d="M6.67727 13.89C6.48636 13.31 6.37727 12.69 6.37727 12.05C6.37727 11.41 6.48636 10.79 6.67727 10.21V7.63H3.40227C2.72273 8.99 2.34091 10.525 2.34091 12.05C2.34091 13.575 2.72273 15.11 3.40227 16.47L6.67727 13.89Z"
                                    fill="#FBBC05"
                                />

                                <path
                                    d="M12.25 6.165C13.7182 6.165 15.0455 6.67 16.0818 7.66L18.8091 4.93273C17.2091 3.44773 14.95 2.525 12.25 2.525C8.40455 2.525 4.91818 4.79 3.40227 8.055L6.67727 10.635C7.43636 8.315 9.64545 6.165 12.25 6.165Z"
                                    fill="#EA4335"
                                />
                            </svg>

                            <span>
                                Continue with Google
                            </span>

                            <svg
                                class="ml-auto h-4 w-4 text-gray-400 transition-transform
                                       group-hover:translate-x-1"
                                fill="none"
                                stroke="currentColor"
                                viewBox="0 0 24 24"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M9 5l7 7-7 7"
                                />
                            </svg>

                        </a>


                        


                        {{-- Information --}}
                        <div class="rounded-xl bg-indigo-50 px-4 py-3.5">

                            <div class="flex items-start gap-3">

                                <div class="mt-0.5 shrink-0">
                                    <svg
                                        class="h-5 w-5 text-indigo-600"
                                        fill="none"
                                        stroke="currentColor"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.8"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M12 15v2m0-10v4m0 0
                                               c-1.657 0-3 1.343-3 3h6
                                               c0-1.657-1.343-3-3-3zm0-6
                                               a9 9 0 100 18 9 9 0 000-18z"
                                        />
                                    </svg>
                                </div>

                                <p class="text-xs leading-5 text-indigo-800">
                                    Use your authorised school Google account
                                    to access eResource.
                                </p>

                            </div>

                        </div>

                    </div>


                    {{-- Card footer --}}
                    <div class="border-t border-gray-100 bg-gray-50/80 px-7 py-4 text-center">

                        <p class="text-xs text-gray-400">
                            © {{ date('Y') }} eResource
                            <span class="mx-1">•</span>
                            Digital Learning
                        </p>

                    </div>

                </div>


                {{-- Background caption --}}
                <div class="mt-6 text-center">

                    <p class="text-sm font-medium text-white/90 drop-shadow">
                        Learn. Collaborate. Grow.
                    </p>

                    <p class="mt-1 text-xs text-white/70">
                        Your digital classroom, wherever learning takes you.
                    </p>

                </div>

            </div>

        </div>

    </div>

</body>

</html>
