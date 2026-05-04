@extends('install.layout')
@section('content')
<h2 class="text-xl mb-4">Step 1: Requirements Check</h2>
<ul class="mb-4">
    <li>PHP >= 8.2: {{ version_compare($phpVersion, '8.2.0', '>=') ? '✅' : '❌' }} {{$phpVersion}}</li>
    @foreach($extensions as $ext => $loaded)
        <li>{{$ext}}: {{ $loaded ? '✅' : '❌' }}</li>
    @endforeach
    @foreach($permissions as $path => $writable)
        <li>{{$path}} writable: {{ $writable ? '✅' : '❌' }}</li>
    @endforeach
</ul>
@if($errors->any())
    <div class="text-red-600 mb-4">{{ $errors->first() }}</div>
@endif
<form method="POST" action="{{ route('install.step2') }}">
    @csrf
    <h3 class="font-bold mt-6 mb-2">Database Setup</h3>
    <select name="db_connection" class="border p-2 w-full mb-2" required>
        <option value="mysql">MySQL</option>
        <option value="pgsql">PostgreSQL</option>
        <option value="sqlite">SQLite</option>
        <option value="sqlsrv">SQL Server</option>
    </select>
    <input name="db_host" value="127.0.0.1" placeholder="DB Host" class="border p-2 w-full mb-2" required>
    <input name="db_name" placeholder="DB Name" class="border p-2 w-full mb-2" required>
    <input name="db_user" placeholder="DB User" class="border p-2 w-full mb-2" required>
    <input name="db_pass" type="password" placeholder="DB Password" class="border p-2 w-full mb-4">
    <button class="bg-blue-600 text-white px-4 py-2 rounded">Continue</button>
</form>
@endsection