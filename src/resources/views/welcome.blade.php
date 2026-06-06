<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Siskripta - Portal Akademik</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

    <style>
        body {
            font-family: 'Inter', sans-serif;
            background-color: #f8fafc; /* Warna abu-abu sangat muda bersih */
        }

        .card-hover:hover {
            transform: translateY(-5px);
            box-shadow: 0 20px 40px -10px rgba(0,0,0,0.08);
            border-color: #e2e8f0;
        }
    </style>
</head>
<body class="min-h-screen flex flex-col items-center justify-center">

    <div class="max-w-6xl w-full px-6 flex flex-col items-center">

        <!-- Header Section -->
        <div class="text-center mb-16 space-y-4">
            <h2 class="text-xl md:text-2xl font-semibold text-slate-500 uppercase tracking-widest mb-4">
                Sistem Informasi Akademik
            </h2>
            <h1 class="text-6xl md:text-8xl font-extrabold text-slate-900 tracking-tight">
                Portal <span class="text-blue-600">Siskripta</span>
            </h1>
            <p class="text-xl md:text-2xl text-slate-600 max-w-3xl mx-auto font-medium leading-relaxed mt-6">
                Platform terpadu untuk pengelolaan bimbingan, revisi, dan penilaian Tugas Akhir/Skripsi.
            </p>
        </div>

        <!-- Role Selection Cards -->
        <div class="grid md:grid-cols-2 gap-8 w-full max-w-4xl">

            <!-- Mahasiswa Card -->
            <a href="/mahasiswa" class="card-hover transition-all duration-300 bg-white rounded-2xl p-10 border border-slate-100 shadow-sm flex flex-col items-center justify-center cursor-pointer text-center group">
                <div class="w-24 h-24 bg-blue-50 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-blue-600 transition-colors duration-300">
                    <svg class="w-12 h-12 text-blue-600 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 14l9-5-9-5-9 5 9 5zm0 0l6.16-3.422a12.083 12.083 0 01.665 6.479A11.952 11.952 0 0012 20.055a11.952 11.952 0 00-6.824-2.998 12.078 12.078 0 01.665-6.479L12 14zm-4 6v-7.5l4-2.222"></path></svg>
                </div>

                <h3 class="text-3xl font-bold text-slate-800 mb-3 group-hover:text-blue-600 transition-colors">Ruang Mahasiswa</h3>
                <p class="text-slate-500 leading-relaxed font-medium">
                    Masuk untuk mengajukan judul, update logbook, dan cek bimbingan.
                </p>

                <div class="mt-8 px-6 py-3 bg-blue-50 text-blue-700 font-semibold rounded-lg group-hover:bg-blue-600 group-hover:text-white transition-colors">
                    Login Mahasiswa &rarr;
                </div>
            </a>

            <!-- Dosen Card -->
            <a href="/dosen" class="card-hover transition-all duration-300 bg-white rounded-2xl p-10 border border-slate-100 shadow-sm flex flex-col items-center justify-center cursor-pointer text-center group">

                <div class="w-24 h-24 bg-slate-100 rounded-2xl flex items-center justify-center mb-6 group-hover:bg-slate-800 transition-colors duration-300">
                    <svg class="w-12 h-12 text-slate-700 group-hover:text-white transition-colors duration-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                </div>

                <h3 class="text-3xl font-bold text-slate-800 mb-3 group-hover:text-slate-800 transition-colors">Ruang Dosen</h3>
                <p class="text-slate-500 leading-relaxed font-medium">
                    Masuk untuk mengevaluasi laporan dan menyetujui kelulusan sidang.
                </p>

                <div class="mt-8 px-6 py-3 bg-slate-100 text-slate-700 font-semibold rounded-lg group-hover:bg-slate-800 group-hover:text-white transition-colors">
                    Login Dosen &rarr;
                </div>
            </a>
        </div>

        <div class="mt-16 text-slate-400 font-medium text-sm">
            &copy; {{ date('Y') }} Siskripta.
        </div>
    </div>

</body>
</html>
