<x-mail::message>
# Potvrdenie registrácie

Dobrý deň{{ $user->name ? ', ' . $user->name : '' }},

boli ste úspešne zaregistrovaný na **{{ $registrationType }}**: **{{ $registrationTitle }}**.

@if($isNewUser)
Na základe vašej registrácie sme vám vytvorili účet. Kliknutím na tlačidlo nižšie sa automaticky prihlásite do svojho účtu.
@else
Kliknutím na tlačidlo nižšie sa prihlásite do svojho účtu.
@endif

<x-mail::button :url="$magicUrl">
Prihlásiť sa
</x-mail::button>

Tento odkaz je platný 7 dní.

Ďakujeme,<br>
{{ config('app.name') }}
</x-mail::message>
