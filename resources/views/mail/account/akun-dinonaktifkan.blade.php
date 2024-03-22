{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# Akun anda telah di-nonaktifkan

Halo, {{$userData->fullname}}, akun Anda telah di-nonaktifkan oleh admin.

Terimakasih,<br>
Sistem {{ config('app.name') }}

</x-mail::message>
