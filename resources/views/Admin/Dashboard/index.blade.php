@extends('layouts.admin')
@section('title') Dashboard @endsection
@section('content')
<div class="rounded-xl bg-gradient-to-r from-indigo-600 to-violet-600 p-6 text-white shadow-sm">
    <p class="text-sm text-indigo-100">{{ now()->format('l, d F Y') }}</p>
    <h2 class="mt-1 text-2xl font-semibold">Welcome back, {{ Auth::user()->name }}</h2>
    <p class="mt-1 text-sm text-indigo-100">Here is a quick look at your portal.</p>
</div>
@endsection
