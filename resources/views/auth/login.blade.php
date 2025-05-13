@extends('layout/main')

@section('container')
<div class="bg-gray-100 flex items-center justify-center min-h-screen">
	<div class="bg-white shadow-lg rounded-xl w-full max-w-sm p-8 text-center">
		<h1 class="text-2xl font-bold mb-2">Wikra</h1>
		<p class="mb-6 text-sm text-gray-600">Log in to continue</p>
		
		<input
		  type="email"
		  placeholder="Enter your email"
		  class="w-full border border-gray-300 rounded-md px-4 py-2 mb-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
		/>
	
		<div class="flex items-center mb-4">
		  <input id="remember" type="checkbox" class="mr-2" />
		  <label for="remember" class="text-sm text-gray-600 flex items-center">
			Remember me
			<span class="ml-1 text-gray-400 cursor-help text-xs">‼️</span>
		  </label>
		</div>
	
		<button class="bg-blue-600 text-white w-full py-2 rounded-md mb-4 hover:bg-blue-700 transition">Continue</button>
	
		{{-- <p class="text-sm text-gray-500 mb-4">Or continue with:</p> --}}
	
		<div class="space-y-2">
		  {{-- <button class="w-full flex items-center justify-center border border-gray-300 rounded-md py-2 hover:bg-gray-100">
			<img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="Google" class="w-5 h-5 mr-2" />
			Google
		  </button> --}}
	
		  {{-- <button class="w-full flex items-center justify-center border border-gray-300 rounded-md py-2 hover:bg-gray-100">
			<img src="https://www.svgrepo.com/show/452213/microsoft.svg" alt="Microsoft" class="w-5 h-5 mr-2" />
			Microsoft
		  </button> --}}
	
		  {{-- <button class="w-full flex items-center justify-center border border-gray-300 rounded-md py-2 hover:bg-gray-100">
			<img src="https://www.svgrepo.com/show/303128/apple-logo.svg" alt="Apple" class="w-5 h-5 mr-2" />
			Apple
		  </button> --}}
	
		  {{-- <button class="w-full flex items-center justify-center border border-gray-300 rounded-md py-2 hover:bg-gray-100">
			<img src="https://www.svgrepo.com/show/374100/slack.svg" alt="Slack" class="w-5 h-5 mr-2" />
			Slack
		  </button> --}}
		</div>
	
		<div class="flex justify-between text-xs text-blue-600 mt-6">
		  <a href="/forgotpwd" class="hover:underline">Can't log in?</a>
		  <a href="/register" class="hover:underline">Create an account</a>
		</div>
	
		{{-- <hr class="my-6 border-gray-200" /> --}}
	
		{{-- <img src="https://upload.wikimedia.org/wikipedia/commons/8/8f/Atlassian-logo.svg" alt="Atlassian" class="w-24 mx-auto"> --}}
	  </div>
</div>
@endsection