@extends('layouts.guest')

@section('title', __('carbex.auth.verify_email_title'))

@section('content')
<div class="sm:mx-auto sm:w-full sm:max-w-md">
    <div class="flex justify-center">
        <x-application-logo class="h-12 w-auto" />
    </div>
    <h2 class="mt-6 text-center text-2xl font-bold leading-9 tracking-tight text-gray-900">
        {{ __('carbex.auth.verify_email_title') }}
    </h2>
    <p class="mt-2 text-center text-sm text-gray-600">
        {{ __('carbex.auth.verify_email_subtitle') }}
    </p>
</div>

<div class="mt-8 sm:mx-auto sm:w-full sm:max-w-md">
    <div class="bg-white px-6 py-8 shadow sm:rounded-lg sm:px-12">
        <div class="text-center">
            <!-- Email Icon -->
            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-full bg-green-100">
                <svg class="h-8 w-8 text-green-600" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21.75 6.75v10.5a2.25 2.25 0 01-2.25 2.25h-15a2.25 2.25 0 01-2.25-2.25V6.75m19.5 0A2.25 2.25 0 0019.5 4.5h-15a2.25 2.25 0 00-2.25 2.25m19.5 0v.243a2.25 2.25 0 01-1.07 1.916l-7.5 4.615a2.25 2.25 0 01-2.36 0L3.32 8.91a2.25 2.25 0 01-1.07-1.916V6.75" />
                </svg>
            </div>

            <p class="mt-6 text-sm text-gray-600">
                {{ __('carbex.auth.verify_email_message') }}
            </p>

            @if (session('status') == 'verification-link-sent')
                <div class="mt-4 rounded-md bg-green-50 p-4">
                    <p class="text-sm font-medium text-green-800">
                        {{ __('carbex.auth.verification_link_sent') }}
                    </p>
                </div>
            @endif

            <div class="mt-6 space-y-4">
                <!-- Resend Verification Email -->
                <form method="POST" action="{{ route('verification.send') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-green-600 px-3 py-2 text-sm font-semibold text-white shadow-sm hover:bg-green-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-green-600">
                        {{ __('carbex.auth.resend_verification') }}
                    </button>
                </form>

                <!-- Logout -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button type="submit"
                        class="flex w-full justify-center rounded-md bg-white px-3 py-2 text-sm font-semibold text-gray-900 shadow-sm ring-1 ring-inset ring-gray-300 hover:bg-gray-50">
                        {{ __('carbex.auth.logout') }}
                    </button>
                </form>
            </div>
        </div>
    </div>

    <p class="mt-6 text-center text-sm text-gray-500">
        {{ __('carbex.auth.wrong_email') }}
        <a href="{{ route('register') }}" class="font-semibold text-green-600 hover:text-green-500">
            {{ __('carbex.auth.register_new') }}
        </a>
    </p>
</div>
@endsection
