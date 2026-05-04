@extends('install.layout')
@section('content')
<h2 class="text-xl mb-4">Step 2: Site & Admin Setup</h2>
<form method="POST" action="{{ route('install.finish') }}">
    @csrf
    <input name="app_name" placeholder="Site Name" class="border p-2 w-full mb-2" required>
    <h3 class="font-bold mt-4 mb-2">Admin Account</h3>
    <input name="name" placeholder="Admin Name" class="border p-2 w-full mb-2" required>
    <input name="email" type="email" placeholder="Admin Email" class="border p-2 w-full mb-2" required>
    <input name="password" type="password" placeholder="Password" class="border p-2 w-full mb-4" required>
    <button class="bg-green-600 text-white px-4 py-2 rounded">Install Laravel</button>
</form>
@endsection