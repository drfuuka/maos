<title>Cetak Laporan Gudep</title>
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
<h2 class="center">Laporan Gugus Depan</h2>
<p class="center">{{ $tanggal }}</p>
<table border="1">
    <thead>
        <tr>
            <th>No</th>
            <th>Nama Kegiatan</th>
            <th>Tanggal Kegiatan</th>
            <th>Tempat Kegiatan</th>
            <th>Jumlah Peserta</th>
            <th>Dibuat Oleh</th>
            <th>Dibuat Tanggal</th>
            <th>Evaluasi Kegiatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $key => $item)
            <tr>
                <td>{{ $key + 1 }}</td>
                <td>{{ $item['nama_kegiatan'] }}</td>
                <td>{{ $item['tanggal_kegiatan'] }}</td>
                <td>{{ $item['tempat_kegiatan'] }}</td>
                <td>{{ $item['jumlah_peserta'] }}</td>
                <td>{{ $item['dibuat_oleh'] }}</td>
                <td>{{ $item['dibuat_tanggal'] }}</td>
                <td>{{ $item['evaluasi_kegiatan'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
