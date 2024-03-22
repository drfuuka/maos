<table>
    <thead>
        <tr>
            <th>Proposal</th>
            <th>Dibuat Oleh</th>
            <th>Evaluasi</th>
            <th>Saran</th>
            <th>Status Verifikasi</th>
            <th>Diverifikasi Oleh</th>
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
            </tr>
        @endforeach
    </tbody>
</table>
