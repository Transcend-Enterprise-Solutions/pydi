<x-authentication-layout>
    <style>
        @keyframes slideInLeft {
            from {
                transform: translateX(-100%);
                opacity: 0;
            }

            to {
                transform: translateX(0);
                opacity: 1;
            }
        }

        .animate-slide-in-left {
            opacity: 0;
            transform: translateX(-100%);
            animation: slideInLeft 0.5s ease-out forwards;
        }

        .animate-slide-in-left-delay-1 {
            opacity: 0;
            transform: translateX(-100%);
            animation: slideInLeft 0.5s ease-out 0.05s forwards;
        }

        .animate-slide-in-left-delay-2 {
            opacity: 0;
            transform: translateX(-100%);
            animation: slideInLeft 0.4s ease-out 0.1s forwards;
        }
    </style>

    <!-- Welcome Message -->
    <div class="p-4">
        <h1 class="text-3xl text-stone-700 font-bold mb-6 animate-slide-in-left">
            {{ __('Welcome back!') }}
        </h1>
        @if (session('status'))
            <!-- Status Message -->
            <div class="mb-4 font-medium text-sm text-green-600 animate-slide-in-left-delay-1">
                {{ session('status') }}
            </div>
        @endif
        <!-- Form -->
        <form method="POST" wire:submit.prevent='login' class="animate-slide-in-left-delay-2">
            @csrf
            <div class="space-y-4 w-full">
                <div>
                    <x-label for="email" value="{{ __('Email') }}" />
                    <x-input id="email" type="email" name="email" :value="old('email')" required autofocus
                    class="w-full h-10 px-2 py-1 text-black rounded-lg appearance-none bg-white/80 backdrop-blur-md border border-zinc-200 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm" />
                </div>
                <div class="relative w-full" x-data="{ show: false }">
                    <x-label for="password" value="{{ __('Password') }}" />
                    <x-input id="password" x-bind:type="show ? 'text' : 'password'" name="password" required
                        autocomplete="current-password" wire:model.live="password"
                        class="w-full h-10 px-2 py-1 text-black rounded-lg appearance-none bg-white/80 backdrop-blur-md border border-zinc-200 placeholder-zinc-300 focus:border-zinc-300 focus:outline-none focus:ring-zinc-300 sm:text-sm" />
                    <button type="button" class="absolute top-1/2 right-0 px-3 flex items-center text-sm leading-5"
                        @click="show = !show">
                        <i x-bind:class="show ? 'bi bi-eye' : 'bi bi-eye-slash'" class="text-lg text-gray-500"></i>
                    </button>
                    @error('password')
                        <span class="text-red-500 text-sm mt-1">This field is required!</span>
                    @enderror
                </div>
                <!-- Remember Me Checkbox -->
                <div class="flex items-center justify-between w-full mt-6">
                    <!-- Remember Me Checkbox -->
                        <div class="flex items-center">
                            <input
                                id="remember_me"
                                type="checkbox"
                                wire:model.defer="remember"
                                class="h-4 w-4 text-blue-600 focus:ring-blue-500 border-gray-300 rounded"
                            />
                            <label for="remember_me" class="ml-2 block text-sm text-gray-400">
                                {{ __('Remember me') }}
                            </label>
                        </div>
                        <!-- Forgot Password Link -->
                        @if (Route::has('password.request'))
                            <a class="text-sm underline hover:no-underline text-gray-400" href="{{ route('password.request') }}">
                                {{ __('Forgot Password?') }}
                            </a>
                        @endif
                    </div>
                </div>
                <div class="flex items-center justify-between mt-6">
                    <x-button>
                        {{ __('Sign in') }}
                    </x-button>
                </div>
        </form>
        <x-validation-errors class="mt-4" />
        @error('login')
            <span class="text-red-500 animate-slide-in-left-delay-2">
                {{ $message }}
            </span>
        @enderror
    </div>
</x-authentication-layout>
