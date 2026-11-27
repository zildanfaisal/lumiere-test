@extends('layouts.app', ['title' => 'Masuk — Lumière Skin'])

@section('content')
    <div class="mx-auto max-w-md py-12">
        <h1 class="font-serif text-5xl tracking-tight text-[#173f35]">
            Lumière Skin
        </h1>

        <p class="mt-4 text-stone-600">
            Le Laboratoire
        </p>

        <form method="POST" class="mt-8 space-y-5 rounded-2xl bg-white p-7 shadow-[0_12px_35px_rgba(23,63,53,.10)]">
            @csrf

            <label class="block">
                <span class="label">Email</span>
                <input
                    class="input"
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    required
                    autofocus
                >
            </label>

            <label class="block">
                <span class="label">Kata sandi</span>
                <input class="input" type="password" name="password" required>
            </label>

            <label class="flex items-center gap-2 text-sm">
                <input type="checkbox" name="remember" value="1">
                Ingat saya
            </label>

            <button class="btn-primary w-full">Masuk</button>
        </form>
    </div>
@endsection
