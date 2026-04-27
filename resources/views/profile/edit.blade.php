@extends('layouts.dashboard')

@section('title', 'Profil')

@section('content')
    <div class="space-y-6">
        <div class="rounded-lg bg-white border border-gray-200 p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-profile-information-form')
            </div>
        </div>

        <div class="rounded-lg bg-white border border-gray-200 p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.update-password-form')
            </div>
        </div>

        <div class="rounded-lg bg-white border border-gray-200 p-4 sm:p-8">
            <div class="max-w-xl">
                @include('profile.partials.delete-user-form')
            </div>
        </div>
    </div>
@endsection
