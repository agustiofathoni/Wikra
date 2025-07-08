@extends('layout/main')

@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-xl w-full max-w-sm p-8 text-center">
        <div class="flex justify-center items-center mb-2">
            <img src="{{ asset('images/logo.png') }}" alt="Logo" class="h-35 w-auto">
        </div>
        {{-- <h1 class="text-2xl font-bold mb-2">Wikra</h1> --}}
        <p class="mb-6 text-sm text-gray-600">Log in to continue</p>

        @if(session()->has('success'))
            <div class="bg-green-100 text-green-700 p-2 rounded mb-4">
                {{ session('success') }}
            </div>
        @endif

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/login" method="POST">
            @csrf
            <input
                type="email"
                name="email"
                placeholder="Enter your email"
                class="w-full border border-gray-300 rounded-md px-4 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />

            <input
                type="password"
                name="password"
                placeholder="Enter your password"
                class="w-full border border-gray-300 rounded-md px-4 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                required
            />

            <div class="flex items-center mb-4">
                <input id="remember" name="remember" type="checkbox" class="mr-2" />
                <label for="remember" class="text-sm text-gray-600 flex items-center">
                    Remember me
                </label>
            </div>

            <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded-md mb-4 hover:bg-blue-700 transition">
                Login
            </button>
        </form>

        <div class="flex justify-between text-xs text-blue-600 mt-6">
            <a href="/forgotpwd" class="hover:underline">Can't log in?</a>
            <a href="/register" class="hover:underline">Create an account</a>
        </div>
    </div>
</div>
@endsection
