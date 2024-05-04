<style>
    .center {
        text-align: center
    }

    .ttd-container {
        display: flex;
        margin-top: 50px
    }

    .ttd-wrapper {
        display: flex;
        flex-direction: column;
        text-align: center;
        margin: auto;
        row-gap: 20px
    }
</style>

<div class="center">
    <h2>Lembar Pengesahan Proposal <br> Gugus Depan</h2>
    <span>{{ \Carbon\Carbon::create($data->created_at)->format('d M, Y') }}</span>
</div>
<div class="center" style="margin-top: 60px; margin-bottom: 100px">

    <span>{{ $data->nama_kegiatan }}</span>

    <h3 style="margin-top: 50px; margin-bottom: 50px">{{ $data->status_verifikasi }}</h3>

    <span class="center">Mengetahui,</span>
</div>

<table>
    <tr>
        <td>
            <div class="ttd-wrapper">
                <span>Mabigus</span>
                <div style="max-height: 150px">
                    @if ($ttd_user)
                        <img src="data:image/jpeg;base64,{{ $ttd_user }}" alt="" width="350">
                    @endif
                </div>
                <span style="text-decoration: underline">{{ $data->user->fullname }}</span>
            </div>
        </td>
        <td>
            <div class="ttd-wrapper">
                <span>Ketua Kwartir Ranting</span>
                <div style="max-height: 150px">
                    @if ($ttd_verificator)
                        <img src="data:image/jpeg;base64,{{ $ttd_verificator }}" alt="" width="350">
                    @endif
                </div>
                <span style="text-decoration: underline">{{ $data->verificator->fullname }}</span>
            </div>
        </td>
    </tr>
</table>

</div>
