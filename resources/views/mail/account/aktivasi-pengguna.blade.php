{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# Permintaan Aktivasi Pengguna

Pengguna dengan nama {{ $userData->fullname }} dengan role {{ $userData->role }} telah mengajukan permintaan aktivasi akun. Harap segera merespons.

<x-mail::button :url="route('pengguna.index')">
    Lihat Pengguna
</x-mail::button>

Terimakasih,<br>
Sistem {{ config('app.name') }}

</x-mail::message>
