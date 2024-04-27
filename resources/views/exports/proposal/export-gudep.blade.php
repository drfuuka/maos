<title>Cetak Proposal Gudep</title>
<style>
    table {
        border-collapse: collapse;
        width: 100%;
    }

    td {
        padding: 5px
    }

    .center {
        text-align: center
    }
</style>
<h2 class="center">Proposal Gugus Depan</h2>
<p class="center">{{ $tanggal }}</p>
<table border="1">
    <thead>
        <tr>
            <th>Nama Kegiatan</th>
            <th>Tema Kegiatan</th>
            <th>Dibuat Oleh</th>
            <th>Dasar Kegiatan</th>
            <th>Maksud Tujuan</th>
            <th>Jenis Proposal</th>
            <th>Kepanitiaan</th>
            <th>Tanggal Kegiatan</th>
            <th>Jadwal Kegiatan</th>
            <th>Rincian Dana</th>
            <th>Penutup</th>
            <th>Status Verifikasi</th>
            <th>Diverifikasi Oleh</th>
            <th>Dibuat Oleh</th>
            <th>Dibuat Tanggal</th>

        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $item['nama_kegiatan'] }}</td>
                <td>{{ $item['tema_kegiatan'] }}</td>
                <td>{{ $item['dibuat_oleh'] }}</td>
                <td>{{ $item['dasar_kegiatan'] }}</td>
                <td>{{ $item['maksud_tujuan'] }}</td>
                <td>{{ $item['jenis_proposal'] }}</td>
                <td>{{ $item['kepanitiaan'] }}</td>
                <td>{{ $item['tanggal_kegiatan'] }}</td>
                <td>{{ $item['jadwal_kegiatan'] }}</td>
                <td>{{ $item['rincian_dana'] }}</td>
                <td>{{ $item['penutup'] }}</td>
                <td>{{ $item['status_verifikasi'] }}</td>
                <td>{{ $item['diverifikasi_oleh'] }}</td>
                <td>{{ $item['dibuat_oleh'] }}</td>
                <td>{{ $item['dibuat_tanggal'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
