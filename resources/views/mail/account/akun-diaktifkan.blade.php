{{-- indentasi tidak boleh diubah, nanti format emailnya rusak --}}
<x-mail::message>

# Akun anda telah di-aktifkan

Halo, {{$userData->fullname}}, akun Anda telah di-aktifkan oleh admin. Sekarang Anda dapat login dan menggunakan aplikasi

<div style="display:flex;">

<x-mail::button :url="route('login.index')">
    Login Sekarang
</x-mail::button>
</div>

Terimakasih,<br>
Sistem {{ config('app.name') }}

</x-mail::message>
