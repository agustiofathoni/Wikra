@extends('layout/main')

@section('container')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
	<div class="bg-white shadow-lg rounded-xl w-full max-w-sm p-8 text-center">
		<h1 class="text-2xl font-bold mb-2">Forgot Password</h1>
		<p class="mb-6 text-sm text-gray-600">Enter your email and we'll send you a link to reset your password.</p>
		
		<form class="space-y-4 text-left">
		  <div>
			<label class="block text-sm text-gray-700 mb-1">Email</label>
			<input
			  type="email"
			  placeholder="Enter your email"
			  class="w-full border border-gray-300 rounded-md px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
			  required
			/>
		  </div>
	
		  <button type="submit" class="bg-blue-600 text-white w-full py-2 rounded-md hover:bg-blue-700 transition">
			Send Reset Link
		  </button>
		</form>
	
		<p class="text-sm text-gray-600 mt-6">
		  <a href="/login" class="text-blue-600 hover:underline">Back to login</a>
		</p>
	
	  </div>
</div>
@endsection