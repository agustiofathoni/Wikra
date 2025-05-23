@extends('layout/main')

@section('content')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
    <div class="bg-white shadow-lg rounded-xl w-full max-w-sm p-8 text-center">
        <h1 class="text-2xl font-bold mb-2">Create Account</h1>
        <p class="mb-6 text-sm text-gray-600">Join Wikra and start managing your projects</p>

        @if($errors->any())
            <div class="bg-red-100 text-red-700 p-2 rounded mb-4">
                {{ $errors->first() }}
            </div>
        @endif

        <form action="/register" method="POST" class="space-y-4 text-left">
            @csrf
            <div>
                <label class="block text-sm text-gray-700 mb-1">Full Name</label>
                <input
                    type="text"
                    name="name"
                    placeholder="Enter your full name"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Email</label>
                <input
                    type="email"
                    name="email"
                    placeholder="Enter your email"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <div>
                <label class="block text-sm text-gray-700 mb-1">Password</label>
                <input
                    type="password"
                    name="password"
                    placeholder="Create a password"
                    class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required
                />
            </div>

            <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded-md hover:bg-blue-700 transition">
                Create Account
            </button>
        </form>

        <p class="text-xs text-gray-600 mt-6">
            Already have an account?
            <a href="/login" class="text-blue-600 hover:underline">Log in here</a>
        </p>
    </div>
</div>
@endsection
