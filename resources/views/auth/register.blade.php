@extends('layout/main')

@section('container')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
	 <div class="bg-white shadow-lg rounded-xl w-full max-w-sm p-8 text-center">
    <h1 class="text-2xl font-bold mb-2">Create Account</h1>
    <p class="mb-6 text-sm text-gray-600">Join Wikra and start managing your projects</p>
    
    <form class="space-y-4 text-left">
      <div>
        <label class="block text-sm text-gray-700 mb-1">Full Name</label>
        <input
          type="text"
          placeholder="Enter your full name"
          class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Email</label>
        <input
          type="email"
          placeholder="Enter your email"
          class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
          required
        />
      </div>

      <div>
        <label class="block text-sm text-gray-700 mb-1">Password</label>
        <input
          type="password"
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