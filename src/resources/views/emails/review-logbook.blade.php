<h2>Halo, {{ $logbook->mahasiswa->nama ?? 'Mahasiswa' }}</h2>
<p>Dosen pembimbing baru saja mereview logbook bimbingan Anda dan memberikan catatan sebagai berikut:</p>

<div style="padding: 15px; border-left: 4px solid #f59e0b; background-color: #fef3c7; margin-top: 10px; margin-bottom: 20px;">
    <strong>Catatan Dosen:</strong> <br/>
    {{ $logbook->catatan_dosen }}
</div>

<p><strong>Status Saat ini:</strong> {{ $logbook->status }}</p>

<p>Tolong segera buka portal Mahasiswa Siskripta untuk meninjau secara lengkap!</p>
