@extends('layouts.admin')

@section('title', __('Settings'))

@section('content')
<h1 class="font-display text-2xl font-semibold text-lake-950">{{ __('Settings') }}</h1>
<p class="mt-2 text-sm text-stone-600">{{ __('Change the admin login email and password.') }}</p>

<div class="mt-8 max-w-xl space-y-8">
    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Login & profile') }}</h2>
        <p class="mt-1 text-sm text-stone-600">{{ __('Your email address is used to sign in.') }}</p>

        <form method="post" action="{{ route('admin.settings.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('patch')

            <div>
                <label for="name" class="block text-sm font-medium text-stone-700">{{ __('Name') }}</label>
                <input
                    id="name"
                    name="name"
                    type="text"
                    value="{{ old('name', $user->name) }}"
                    required
                    autocomplete="name"
                    class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-lake-500 focus:outline-none focus:ring-1 focus:ring-lake-500"
                />
                @error('name')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label for="email" class="block text-sm font-medium text-stone-700">{{ __('Email') }}</label>
                <input
                    id="email"
                    name="email"
                    type="email"
                    value="{{ old('email', $user->email) }}"
                    required
                    autocomplete="username"
                    class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-lake-500 focus:outline-none focus:ring-1 focus:ring-lake-500"
                />
                @error('email')
                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <button type="submit" class="rounded-full bg-lake-900 px-5 py-2 text-sm font-semibold text-white hover:bg-lake-800">
                    {{ __('Save') }}
                </button>
            </div>
        </form>
    </section>

    <section class="rounded-2xl border border-stone-200 bg-white p-6 shadow-sm">
        <h2 class="font-display text-lg font-semibold text-lake-950">{{ __('Password') }}</h2>
        <p class="mt-1 text-sm text-stone-600">{{ __('Use a strong, unique password.') }}</p>

        @php($pwdBag = $errors->getBag('updatePassword'))
        <form method="post" action="{{ route('password.update') }}" class="mt-6 space-y-4">
            @csrf
            @method('put')

            <div>
                <label for="current_password" class="block text-sm font-medium text-stone-700">{{ __('Current password') }}</label>
                <input
                    id="current_password"
                    name="current_password"
                    type="password"
                    required
                    autocomplete="current-password"
                    class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-lake-500 focus:outline-none focus:ring-1 focus:ring-lake-500"
                />
                @if($pwdBag->has('current_password'))
                    <p class="mt-1 text-sm text-red-600">{{ $pwdBag->first('current_password') }}</p>
                @endif
            </div>

            <div>
                <label for="password" class="block text-sm font-medium text-stone-700">{{ __('New password') }}</label>
                <input
                    id="password"
                    name="password"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-lake-500 focus:outline-none focus:ring-1 focus:ring-lake-500"
                />
                @if($pwdBag->has('password'))
                    <p class="mt-1 text-sm text-red-600">{{ $pwdBag->first('password') }}</p>
                @endif
            </div>

            <div>
                <label for="password_confirmation" class="block text-sm font-medium text-stone-700">{{ __('Confirm new password') }}</label>
                <input
                    id="password_confirmation"
                    name="password_confirmation"
                    type="password"
                    required
                    autocomplete="new-password"
                    class="mt-1 w-full rounded-lg border border-stone-300 px-3 py-2 text-sm shadow-sm focus:border-lake-500 focus:outline-none focus:ring-1 focus:ring-lake-500"
                />
                @if($pwdBag->has('password_confirmation'))
                    <p class="mt-1 text-sm text-red-600">{{ $pwdBag->first('password_confirmation') }}</p>
                @endif
            </div>

            <div>
                <button type="submit" class="rounded-full bg-lake-900 px-5 py-2 text-sm font-semibold text-white hover:bg-lake-800">
                    {{ __('Update password') }}
                </button>
            </div>
        </form>
    </section>
</div>
@endsection
