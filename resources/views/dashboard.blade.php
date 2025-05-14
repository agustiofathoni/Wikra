@extends('layout/main')

@section('container')
<div class="min-h-screen bg-gray-100">
    <!-- Navbar -->
  <div class="flex items-center justify-between px-6 py-3 border-b">
    <div class="flex items-center space-x-3">
      <div class="text-xl font-bold">Wikra</div>
      <div class="relative w-[500px]">
        <input type="text" placeholder="Search..." class="px-4 py-2 border rounded-md w-72" />
        <span class="absolute right-3 top-2.5 text-gray-400">🔍</span>
      </div>
    </div>
    <div class="flex items-center space-x-2">
      <button class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">Create</button>
      <div class="flex space-x-3 text-xl">
        <span>🗂️</span>
        <span>🔔</span>
        <span>❓</span>
        <span>👤</span>
      </div>
    </div>
  </div>

  <div class="flex min-h-screen">

    <!-- Sidebar -->
    <aside class="w-64 border-r p-6 space-y-6">
      <nav class="space-y-4">
        <div class="space-y-2">
          <button class="flex items-center space-x-2 text-blue-600 font-medium">
            <span>📋</span><span>Boards</span>
          </button>
          <button class="flex items-center space-x-2">
            <span>📄</span><span>Templates</span>
          </button>
          <button class="flex items-center space-x-2">
            <span>🏠</span><span>Home</span>
          </button>
        </div>
      </nav>
      <div>
        <p class="text-sm uppercase text-gray-400">Workspace</p>
        <div class="flex items-center space-x-2 mt-2">
          <div class="bg-green-200 text-green-800 px-2 py-1 rounded font-bold text-sm">W</div>
          <span>Wikra Workspace ▼</span>
        </div>
      </div>
    </aside>

    <!-- Main content -->
    <main class="flex-1 p-6 space-y-6">

      <!-- Popular Templates -->
      <section>
        <h2 class="text-xl font-semibold mb-1">Most popular templates</h2>
        <p class="text-sm text-gray-500 mb-3">Get going faster with a template from the Trello community or 
          <select class="ml-1 border rounded px-2 py-1 text-sm">
            <option>category</option>
          </select>
        </p>
        <div class="flex space-x-4">
          <div class="w-40 bg-blue-500 text-white rounded overflow-hidden">
            <div class="h-20"></div>
            <div class="px-2 py-1 text-sm bg-black bg-opacity-50">Basic Board</div>
          </div>
          <div class="w-40 bg-pink-300 text-white rounded overflow-hidden">
            <div class="h-20"></div>
            <div class="px-2 py-1 text-sm bg-black bg-opacity-50">Kanban Template</div>
          </div>
          <div class="w-40 bg-gray-400 text-white rounded overflow-hidden">
            <div class="h-20 bg-cover bg-center" style="background-image: url('https://picsum.photos/200')"></div>
            <div class="px-2 py-1 text-sm bg-black bg-opacity-50">Daily Task Management</div>
          </div>
        </div>
        <a href="#" class="text-sm text-blue-600 mt-2 inline-block">Browse the full template gallery</a>
      </section>

      <!-- Your Workspace -->
      <section>
        <h2 class="text-lg font-semibold mb-3">YOUR WORKSPACE</h2>
        <div class="flex items-center space-x-2 mb-3">
          <div class="bg-green-200 text-green-800 px-2 py-1 rounded font-bold text-sm">W</div>
          <span class="font-medium">Wikra Workspace</span>
          <div class="flex gap-1 text-xs text-gray-600">
            <span class="border px-2 py-0.5 rounded">Boards</span>
            <span class="border px-2 py-0.5 rounded">Highlights</span>
            <span class="border px-2 py-0.5 rounded">Members</span>
            <span class="border px-2 py-0.5 rounded">Settings</span>
          </div>
        </div>
        <div class="flex gap-4">
          <div class="w-48 h-28 bg-cover bg-center text-white rounded-md overflow-hidden relative" style="background-image: url('https://picsum.photos/300/200');">
            <div class="absolute bottom-0 bg-black bg-opacity-40 w-full p-2 text-sm">My Wikra board</div>
          </div>
          <div class="w-48 h-28 bg-gray-500 text-white rounded-md flex items-center justify-center cursor-pointer hover:bg-gray-600">
            Create new board
          </div>

          {{-- logout --}}

          <div class="flex items-center space-x-2">
            <span>👤</span>
            <form method="POST" action="/logout">
              <!-- CSRF token jika pakai Laravel -->
              <input type="hidden" name="_token" value="{{ csrf_token() }}">
              <button type="submit" class="text-sm text-red-500 hover:underline">Logout</button>
            </form>
          </div>
        </div>
</div>
@endsection
