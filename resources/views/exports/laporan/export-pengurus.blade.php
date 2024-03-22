<table>
    <thead>
        <tr>
            <th>Nama Kegiatan</th>
            <th>Tanggal Kegiatan</th>
            <th>Tempat Kegiatan</th>
            <th>Jumlah Peserta</th>
            <th>Dibuat Oleh</th>
            <th>Evaluasi Kegiatan</th>
        </tr>
    </thead>
    <tbody>
        @foreach ($data as $item)
            <tr>
                <td>{{ $item['nama_kegiatan'] }}</td>
                <td>{{ $item['tanggal_kegiatan'] }}</td>
                <td>{{ $item['tempat_kegiatan'] }}</td>
                <td>{{ $item['jumlah_peserta'] }}</td>
                <td>{{ $item['dibuat_oleh'] }}</td>
                <td>{{ $item['evaluasi_kegiatan'] }}</td>
            </tr>
        @endforeach
    </tbody>
</table>
