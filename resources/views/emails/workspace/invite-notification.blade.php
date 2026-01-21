<x-mail::message>
    # added to workspace

    hi {{ $user->full_name }}

    this is the link {{ env('FRONTEND_URL') . '/workspace/' . $workspace->slug . '/invite/' . $invitation->token }}





    <x-mail::button :url="''">
        Button Text
    </x-mail::button>

    Thanks,<br>
    {{ config('app.name') }}
</x-mail::message>
