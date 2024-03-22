{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# LPJ Diterima

LPJ dengan nama kegiatan {{$lpj->proposal->name_kegiatan}} telah verifikasi dan diterima oleh Ketua {{$ketua->fullname}}

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
