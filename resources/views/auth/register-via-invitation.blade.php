<!DOCTYPE html>
<html lang="sk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registrácia - {{ config('app.name') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700&display=swap" rel="stylesheet">
    @vite(['resources/css/filament/admin/theme.css'])
    <style>
        body { font-family: 'DM Sans', sans-serif; }
    </style>
</head>
<body class="min-h-screen bg-gray-50 flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="bg-white shadow-sm ring-1 ring-gray-950/5 p-8">
            <div class="text-center mb-6">
                <h1 class="text-2xl font-bold text-gray-950">Registrácia</h1>
                <p class="mt-2 text-sm text-gray-500">
                    Boli ste pozvaní do tímu <strong>{{ $invitation->team->getTranslation('name', 'sk') }}</strong>
                </p>
            </div>

            <form method="POST" action="{{ route('team-invitations.register', $invitation) }}" class="space-y-4">
                @csrf

                <div>
                    <label for="email" class="block text-sm font-medium text-gray-700 mb-1">E-mail</label>
                    <input
                        type="email"
                        id="email"
                        value="{{ $invitation->email }}"
                        disabled
                        class="w-full border-gray-300 bg-gray-50 text-gray-500 shadow-sm text-sm"
                    >
                </div>

                <div>
                    <label for="first_name" class="block text-sm font-medium text-gray-700 mb-1">Meno</label>
                    <input
                        type="text"
                        id="first_name"
                        name="first_name"
                        value="{{ old('first_name') }}"
                        required
                        autofocus
                        class="w-full border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    >
                    @error('first_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="last_name" class="block text-sm font-medium text-gray-700 mb-1">Priezvisko</label>
                    <input
                        type="text"
                        id="last_name"
                        name="last_name"
                        value="{{ old('last_name') }}"
                        required
                        class="w-full border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    >
                    @error('last_name')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700 mb-1">Heslo</label>
                    <input
                        type="password"
                        id="password"
                        name="password"
                        required
                        class="w-full border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    >
                    @error('password')
                        <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700 mb-1">Potvrdiť heslo</label>
                    <input
                        type="password"
                        id="password_confirmation"
                        name="password_confirmation"
                        required
                        class="w-full border-gray-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm"
                    >
                </div>

                <button
                    type="submit"
                    class="w-full bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition"
                >
                    Vytvoriť účet a prijať pozvánku
                </button>
            </form>
        </div>
    </div>
</body>
</html>
