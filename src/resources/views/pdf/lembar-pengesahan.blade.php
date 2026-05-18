<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembar Pengesahan</title>
    <style>
        body { font-family: 'Times New Roman', Times, serif; font-size: 14pt; line-height: 1.5; padding: 2cm 2cm; }
        .text-center { text-align: center; }
        .bold { font-weight: bold; }
        .mt-4 { margin-top: 2rem; }
        .mt-8 { margin-top: 4rem; }
        table { width: 100%; margin-top: 2rem; }
        td { padding: 5px; vertical-align: top; }
        .signature-table td { text-align: center; width: 50%; }
    </style>
</head>
<body>

    <div class="text-center">
        <h3 class="bold">LEMBAR PENGESAHAN</h3>
        <p>PROJECT AKHIR / SKRIPSI</p>
    </div>

    <div class="mt-4">
        <p>Telah diuji dan dipertahankan dalam Ujian Sidang pada tanggal 
            <span class="bold">{{ \Carbon\Carbon::parse($record->jadwal)->translatedFormat('d F Y') }}</span>.
        </p>

        <table>
            <tr>
                <td style="width: 25%">Judul</td>
                <td style="width: 2%">:</td>
                <td>{{ $record->submission->judul }}</td>
            </tr>
            <tr>
                <td>Nama Mahasiswa</td>
                <td>:</td>
                <td>{{ $record->submission->mahasiswa->name }}</td>
            </tr>
            <tr>
                <td>NIM</td>
                <td>:</td>
                <td>{{ $record->submission->mahasiswa->nim ?? '0000000000' }}</td>
            </tr>
            <tr>
                <td>Status Kelulusan</td>
                <td>:</td>
                <td class="bold">
                    {{ $record->status_kelulusan === 'lulus' ? 'LULUS' : 'LULUS BERSYARAT/TIDAK LULUS' }}
                </td>
            </tr>
        </table>
    </div>

    <table class="signature-table mt-8">
        <tr>
            <td>
                Menyetujui,<br>
                <b>Dosen Pembimbing</b>
                <br><br><br><br>
                ( {{ $record->submission->dosen->name ?? '____________________' }} )
            </td>
            <td>
                Mengesahkan,<br>
                <b>Dosen Penguji</b>
                <br><br><br><br>
                ( {{ $record->dosen->name ?? '____________________' }} )
            </td>
        </tr>
    </table>

</body>
</html>