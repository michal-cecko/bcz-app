<x-mail::message>
# Pozvánka do tímu {{ $invitation->team->getTranslation('name', 'sk') }}

Boli ste pozvaní do tímu **{{ $invitation->team->getTranslation('name', 'sk') }}**.

Kliknutím na tlačidlo nižšie prijmete pozvánku.

<x-mail::button :url="$acceptUrl">
Prijať pozvánku
</x-mail::button>

Táto pozvánka je platná do {{ $invitation->expires_at->format('d.m.Y H:i') }}.

Ďakujeme,<br>
{{ config('app.name') }}
</x-mail::message>
