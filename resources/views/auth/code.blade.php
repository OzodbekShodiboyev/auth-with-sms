<x-guest-layout>
    @if(Session::has('flash_message'))
        <div class="alert alert-success">
            <h3 class="text-red-600">{{ Session::get('flash_message') }}</h3>
        </div>
    @endif

    <form method="POST" action="{{ url('register-confirm') }}">
        @csrf
        <input type="hidden" name="name" value="{{$name}}">
        <input type="hidden" name="phone" value="{{$phone}}">
        <input type="hidden" name="code" value="{{$code}}">
        <!-- Name -->
        <div>
            <x-input-label name="code" for="code" :value="__('Code')" />
            <x-text-input id="sms" class="block mt-1 w-full" type="text" name="sms"  required />
        </div>
        <div class="flex items-center justify-end mt-4">
            <a class="underline text-sm text-gray-600 dark:text-gray-400 hover:text-gray-900 dark:hover:text-gray-100 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 dark:focus:ring-offset-gray-800" href="{{ route('login') }}">
                {{ __('Already registered?') }}
            </a>

            <x-primary-button class="ml-4">
                {{ __('Confirm') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>
