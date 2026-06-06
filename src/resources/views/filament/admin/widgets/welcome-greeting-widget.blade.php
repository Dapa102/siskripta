<x-filament-widgets::widget>
    <x-filament::section class="border-transparent bg-gradient-to-br from-indigo-50 to-white dark:from-gray-800 dark:to-gray-900 shadow-xl overflow-hidden relative group">

        <!-- Animated Circle Background -->
        <div class="absolute -top-24 -right-24 w-64 h-64 bg-indigo-500 rounded-full mix-blend-multiply filter blur-3xl opacity-20 transform group-hover:scale-150 transition-transform duration-700"></div>

        <div class="flex items-center gap-x-6 relative z-10 px-2 py-4">

            <!-- Foto Profil Interaktif -->
            <div class="relative shrink-0">
                <div class="w-24 h-24 rounded-full bg-indigo-100 dark:bg-gray-700 flex items-center justify-center ring-4 ring-white dark:ring-gray-800 shadow-lg relative overflow-hidden">
                    @if(auth()->user()->avatar_url)
                        <img src="{{ asset('storage/' . auth()->user()->avatar_url) }}" alt="Avatar" class="w-full h-full object-cover">
                    @else
                        <span class="text-4xl font-bold text-indigo-500">{{ substr(auth()->user()->name, 0, 1) }}</span>
                    @endif
                </div>

                <!-- Status Ping / Dot -->
                <span class="absolute bottom-1 right-1 flex h-4 w-4">
                  <span class="animate-ping absolute inline-flex h-full w-full rounded-full bg-green-400 opacity-75"></span>
                  <span class="relative inline-flex rounded-full h-4 w-4 bg-green-500 border-2 border-white items-center justify-center"></span>
                </span>
            </div>

            <!-- Pesan Sapaan -->
            <div>
                <h1 class="text-3xl font-extrabold text-gray-900 dark:text-white tracking-tight">
                    Selamat datang kembali, <span class="text-transparent bg-clip-text bg-gradient-to-r from-indigo-600 to-blue-500">{{ auth()->user()->name }}</span>! 👋
                </h1>
                <p class="mt-2 text-lg text-gray-500 dark:text-gray-400 font-medium">
                    {{ auth()->user()->role == 'mahasiswa' ? 'Semangat menyusun Skripsi!' : 'Selamat bertugas melakukan penilaian hari ini.' }}
                </p>
                <div class="mt-4 flex gap-4">
                    @if(auth()->user()->role == 'mahasiswa')
                    <div class="px-4 py-1 bg-white dark:bg-gray-800 rounded-full shadow-sm text-sm border font-medium text-gray-700 dark:text-gray-300">
                        Semester: <span class="text-indigo-600">{{ auth()->user()->semester ?? '-' }}</span>
                    </div>
                    @endif
                    <div class="px-4 py-1 bg-white dark:bg-gray-800 rounded-full shadow-sm text-sm border font-medium text-gray-700 dark:text-gray-300">
                        {{ auth()->user()->role == 'mahasiswa' ? 'NIM' : 'NIDN' }}: <span class="text-indigo-600 dark:text-indigo-400">{{ auth()->user()->nidn_nim ?? '-' }}</span>
                    </div>
                </div>
            </div>

        </div>
    </x-filament::section>
</x-filament-widgets::widget>
