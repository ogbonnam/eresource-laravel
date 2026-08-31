<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Login | eResource</title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="min-h-screen bg-gray-100 flex items-center justify-center">

    <div class="w-full max-w-md px-6">

        <div class="bg-white rounded-2xl shadow-lg p-8">

            <div class="text-center mb-8">
                <h1 class="text-3xl font-bold text-gray-900">
                    eResource
                </h1>

                <p class="mt-2 text-gray-500">
                    Digital Learning Platform
                </p>
            </div>

            <a
                href="{{ route('auth.google') }}"
                class="w-full flex items-center justify-center gap-3
                       rounded-xl border border-gray-300
                       bg-white px-5 py-3
                       font-medium text-gray-700
                       hover:bg-gray-50 transition"
            >
                <svg
                    class="w-5 h-5"
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

                Continue with Google
            </a>

        </div>

    </div>

</body>
</html>