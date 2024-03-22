{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# Permintaan Verifikasi LPJ

Pengguna dengan nama {{ $userData->fullname }} dengan role {{ $userData->role }} telah mengajukan permintaan verifikasi LPJ. Harap segera merespons.

@if ($lpj->user->role === 'Gudep')
<x-mail::button :url="route('lpj-gudep.edit', $lpj->id)">
    Lihat LPJ
</x-mail::button>

@elseif ($lpj->user->role === 'Pengurus')
<x-mail::button :url="route('lpj-pengurus.edit', $lpj->id)">
    Lihat LPJ
</x-mail::button>
@endif

Terimakasih,<br>
Sistem {{ config('app.name') }}

</x-mail::message>
