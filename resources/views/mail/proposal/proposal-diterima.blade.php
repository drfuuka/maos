{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# Proposal Diterima

Proposal dengan nama kegiatan {{$proposal->nama_kegiatan}} telah verifikasi dan diterima oleh Ketua {{$ketua->fullname}}

@if ($proposal->user->role === 'Gudep')
<x-mail::button :url="route('proposal-gudep.edit', $proposal->id)">
    Lihat Proposal
</x-mail::button>

@elseif ($proposal->user->role === 'Pengurus')
<x-mail::button :url="route('proposal-pengurus.edit', $proposal->id)">
    Lihat Proposal
</x-mail::button>
@endif

Terimakasih,<br>
Sistem {{ config('app.name') }}

</x-mail::message>
