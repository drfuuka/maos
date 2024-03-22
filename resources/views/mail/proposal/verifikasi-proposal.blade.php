{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# Permintaan Verifikasi Proposal

Pengguna dengan nama {{ $userData->fullname }} dengan role {{ $userData->role }} telah mengajukan permintaan verifikasi proposal. Harap segera merespons.

@if ($userData->role === 'Gudep')
<x-mail::button :url="route('proposal-gudep.edit', $proposal->id)">
    Lihat Proposal
</x-mail::button>

@elseif ($userData->role === 'Pengurus')
<x-mail::button :url="route('proposal-pengurus.edit', $proposal->id)">
    Lihat Proposal
</x-mail::button>
@endif

Terimakasih,<br>
Sistem {{ config('app.name') }}

</x-mail::message>
