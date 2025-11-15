<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>

    <!-- Tailwind CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <style>
        @keyframes float {
            0% { transform: translateY(0px); }
            50% { transform: translateY(-30px); }
            100% { transform: translateY(0px); }
        }

        @keyframes slideIn {
            0% { opacity: 0; transform: translateY(30px); }
            100% { opacity: 1; transform: translateY(0px); }
        }

        .floating {
            animation: float 6s ease-in-out infinite;
        }

        .slide-in {
            animation: slideIn 0.7s ease forwards;
        }
    </style>
</head>

<body class="min-h-screen bg-gray-900 overflow-hidden relative">

    <!-- Floating background shapes -->
    <div class="absolute top-0 left-0 w-72 h-72 bg-purple-600 blur-3xl opacity-20 rounded-full floating"></div>
    <div class="absolute bottom-0 right-0 w-80 h-80 bg-blue-500 blur-3xl opacity-20 rounded-full floating" style="animation-delay: 2s;"></div>

    <!-- Glass Navbar -->
    <nav class="fixed top-0 left-0 w-full z-50 bg-white/10 backdrop-blur-md border-b border-white/10 px-6 py-4 flex justify-between items-center">
        <h1 class="text-white font-semibold text-lg tracking-wide">Aykon</h1>
        <a href="{{route('register')}}" class="text-purple-300 hover:text-purple-400 transition">Register</a>
    </nav>

    <!-- Center Login Container -->
    <div class="flex items-center justify-center min-h-screen px-4">
        <div class="w-full max-w-md slide-in">
            <div class="bg-white/10 backdrop-blur-xl shadow-2xl rounded-2xl p-8 border border-white/10 mt-20">

                <!-- Heading -->
                <h2 class="text-3xl font-bold text-center text-white mb-6 tracking-wide">
                    Welcome Back 👋
                </h2>

                <!-- Form -->
                <form method="POST" action="{{ route('login') }}" class="space-y-6">
                    @csrf

                    <!-- Email -->
                    <div>
                        <label class="block text-white mb-1 font-medium">Email Address</label>
                        <input
                            type="email"
                            name="email"
                            value="{{ old('email') }}"
                            required
                            class="w-full px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-300 border border-white/20 focus:border-purple-400 focus:ring-2 focus:ring-purple-300 transition"
                            placeholder="Enter your email"
                        />
                        @error('email')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Password -->
                    <div>
                        <label class="block text-white mb-1 font-medium">Password</label>
                        <input
                            type="password"
                            name="password"
                            required
                            class="w-full px-4 py-3 rounded-xl bg-white/20 text-white placeholder-gray-300 border border-white/20 focus:border-purple-400 focus:ring-2 focus:ring-purple-300 transition"
                            placeholder="Enter your password"
                        />
                        @error('password')
                            <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    <!-- Remember + Forgot -->
                    <div class="flex items-center justify-between text-white">
                        <label class="flex items-center gap-2 cursor-pointer">
                            <input type="checkbox" name="remember" class="w-4 h-4 rounded border-white/40 bg-white/10">
                            <span>Remember Me</span>
                        </label>

                        @if (Route::has('password.request'))
                        <a href="{{ route('password.request') }}" class="text-purple-300 hover:text-purple-400 text-sm transition">
                            Forgot Password?
                        </a>
                        @endif
                    </div>

                    <!-- Login Button -->
                    <button
                        type="submit"
                        class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 rounded-xl shadow-lg shadow-purple-500/20 hover:shadow-purple-500/40 transition active:scale-95">
                        Login
                    </button>
                </form>
            </div>

            <p class="text-center text-gray-300 mt-6 text-sm">
                © {{ date('Y') }} Aykon • All rights reserved
            </p>
        </div>
    </div>

</body>
</html>
