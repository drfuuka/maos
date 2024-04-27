<title>Cetak LPJ Gudep</title>
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
<h2 class="center">LPJ Gugus Depan</h2>
<p class="center">{{ $tanggal }}</p>
<table border="1">
    <thead>
        <tr>
            <th>Proposal</th>
            <th>Dibuat Oleh</th>
            <th>Evaluasi</th>
            <th>Saran</th>
            <th>Status Verifikasi</th>
            <th>Diverifikasi Oleh</th>
            <th>Dibuat Oleh</th>
            <th>Dibuat Tanggal</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $item['nama_proposal'] }}</td>
                <td>{{ $item['dibuat_oleh'] }}</td>
                <td>{{ $item['evaluasi'] }}</td>
                <td>{{ $item['saran'] }}</td>
                <td>{{ $item['status_verifikasi'] }}</td>
                <td>{{ $item['diverifikasi_oleh'] }}</td>
                <td>{{ $item['dibuat_oleh'] }}</td>
                <td>{{ $item['dibuat_tanggal'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
