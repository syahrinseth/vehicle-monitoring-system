<!DOCTYPE html>
<html lang="ms">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sistem Pemantauan Kenderaan</title>

        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600,700,800" rel="stylesheet" />

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="min-h-screen bg-slate-950 text-slate-100">
        @php
            $roles = [
                [
                    'title' => 'Admin',
                    'description' => 'Urus pengguna, data sistem, pendaftaran, pelekat digital, dan pemantauan keseluruhan operasi VMS.',
                    'href' => url('/admin/login'),
                    'button' => 'Log Masuk Admin',
                    'accent' => 'from-blue-500 to-cyan-400',
                    'icon' => '<svg class="h-10 w-10 text-cyan-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M9.594 3.94c.09-.542.56-.94 1.11-.94h2.593c.55 0 1.02.398 1.11.94l.213 1.281c.063.374.313.686.645.87.074.04.147.083.22.127.325.196.72.257 1.075.124l1.217-.456a1.125 1.125 0 0 1 1.37.49l1.296 2.247a1.125 1.125 0 0 1-.26 1.431l-1.003.827c-.293.241-.438.613-.43.992a7.723 7.723 0 0 1 0 .255c-.008.378.137.75.43.991l1.004.827c.424.35.534.955.26 1.43l-1.298 2.247a1.125 1.125 0 0 1-1.369.491l-1.217-.456c-.355-.133-.75-.072-1.076.124a6.47 6.47 0 0 1-.22.128c-.331.183-.581.495-.644.869l-.213 1.281c-.09.543-.56.94-1.11.94h-2.594c-.55 0-1.019-.398-1.11-.94l-.213-1.281c-.062-.374-.312-.686-.644-.87a6.52 6.52 0 0 1-.22-.127c-.325-.196-.72-.257-1.076-.124l-1.217.456a1.125 1.125 0 0 1-1.369-.49l-1.297-2.247a1.125 1.125 0 0 1 .26-1.431l1.004-.827c.292-.24.437-.613.43-.991a6.932 6.932 0 0 1 0-.255c.007-.38-.138-.751-.43-.992l-1.004-.827a1.125 1.125 0 0 1-.26-1.43l1.297-2.247a1.125 1.125 0 0 1 1.37-.491l1.216.456c.356.133.751.072 1.076-.124.072-.044.146-.086.22-.128.332-.183.582-.495.644-.869l.214-1.28Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z"/></svg>',
                ],
                [
                    'title' => 'Pengawal',
                    'description' => 'Imbas kod QR atau semak nombor pendaftaran bagi mengawal kemasukan kenderaan di pintu masuk.',
                    'href' => url('/guard/login'),
                    'button' => 'Log Masuk Pengawal',
                    'accent' => 'from-amber-500 to-orange-400',
                    'icon' => '<svg class="h-10 w-10 text-amber-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M3.75 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 3.75 9.375v-4.5ZM3.75 14.625c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5a1.125 1.125 0 0 1-1.125-1.125v-4.5ZM13.5 4.875c0-.621.504-1.125 1.125-1.125h4.5c.621 0 1.125.504 1.125 1.125v4.5c0 .621-.504 1.125-1.125 1.125h-4.5A1.125 1.125 0 0 1 13.5 9.375v-4.5Z"/><path stroke-linecap="round" stroke-linejoin="round" d="M6.75 6.75h.75v.75h-.75v-.75ZM6.75 16.5h.75v.75h-.75v-.75ZM16.5 6.75h.75v.75h-.75v-.75ZM13.5 13.5h.75v.75h-.75v-.75ZM13.5 19.5h.75v.75h-.75v-.75ZM19.5 13.5h.75v.75h-.75v-.75ZM19.5 19.5h.75v.75h-.75v-.75ZM16.5 16.5h.75v.75h-.75v-.75Z"/></svg>',
                ],
                [
                    'title' => 'Pihak Berkuasa',
                    'description' => 'Semak, sahkan, dan luluskan permohonan pendaftaran kenderaan mengikut keperluan institut.',
                    'href' => url('/authority/login'),
                    'button' => 'Log Masuk Pihak Berkuasa',
                    'accent' => 'from-violet-500 to-fuchsia-400',
                    'icon' => '<svg class="h-10 w-10 text-violet-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M10.125 2.25h-4.5c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125v-9M10.125 2.25h.375a9 9 0 0 1 9 9v.375M10.125 2.25A3.375 3.375 0 0 1 13.5 5.625v1.5c0 .621.504 1.125 1.125 1.125h1.5a3.375 3.375 0 0 1 3.375 3.375M9 15l2.25 2.25L15 12"/></svg>',
                ],
                [
                    'title' => 'Pelajar',
                    'description' => 'Daftar kenderaan, hantar maklumat yang diperlukan, dan akses pelekat QR digital selepas kelulusan.',
                    'href' => url('/student/login'),
                    'button' => 'Log Masuk Pelajar',
                    'accent' => 'from-emerald-500 to-lime-400',
                    'icon' => '<svg class="h-10 w-10 text-emerald-300" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" aria-hidden="true" data-slot="icon"><path stroke-linecap="round" stroke-linejoin="round" d="M4.26 10.147a60.438 60.438 0 0 0-.491 6.347A48.62 48.62 0 0 1 12 20.904a48.62 48.62 0 0 1 8.232-4.41 60.46 60.46 0 0 0-.491-6.347m-15.482 0a50.636 50.636 0 0 0-2.658-.813A59.906 59.906 0 0 1 12 3.493a59.903 59.903 0 0 1 10.399 5.84c-.896.248-1.783.52-2.658.814m-15.482 0A50.717 50.717 0 0 1 12 13.489a50.702 50.702 0 0 1 7.74-3.342M6.75 15a.75.75 0 1 0 0-1.5.75.75 0 0 0 0 1.5Zm0 0v-3.675A55.378 55.378 0 0 1 12 8.443m-7.007 11.55A5.981 5.981 0 0 0 6.75 15.75v-1.5"/></svg>',
                ],
            ];
        @endphp

        <div class="relative overflow-hidden">
            <div class="absolute inset-0 bg-[radial-gradient(circle_at_top,_rgba(59,130,246,0.22),_transparent_30%),radial-gradient(circle_at_bottom_right,_rgba(34,197,94,0.16),_transparent_24%),linear-gradient(180deg,_#020617,_#0f172a)]"></div>

            <main class="relative mx-auto flex min-h-screen max-w-7xl flex-col px-6 py-10 lg:px-8 lg:py-14">
                <section class="grid gap-8 lg:grid-cols-[1.15fr_0.85fr] lg:items-start">
                    <div class="rounded-[2rem] border border-white/10 bg-white/8 p-7 shadow-2xl shadow-slate-950/30 backdrop-blur md:p-10">
                        <div class="flex flex-col gap-6 sm:flex-row sm:items-center">
                            <div class="self-start">
                                <img
                                    src="{{ asset('images/jata-negara-ai-01-cf49d576.png') }}"
                                    alt="Jata Negara"
                                    class="block h-full w-full object-contain"
                                >
                            </div>

                            <div>
                                <p class="text-sm font-semibold uppercase tracking-[0.32em] text-cyan-300">e-Daftar Kenderaan</p>
                                <h1 class="mt-3 max-w-3xl text-4xl font-extrabold tracking-tight text-white">
                                    Sistem e-Daftar Kenderaan Digital <br/>(e-Daftar)
                                </h1>
                                <p class="mt-3 text-base font-medium text-slate-300 md:text-lg">
                                    Platform digital untuk e-Daftar Kenderaan pelajar, kelulusan permohonan, pelekat QR, dan kawalan akses ke kawasan institut.
                                </p>
                            </div>
                        </div>

                        <div class="mt-8 grid gap-4 sm:grid-cols-3">
                            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                <p class="text-sm text-slate-400">Modul utama</p>
                                <p class="mt-2 text-2xl font-bold text-white">4 Peranan</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                <p class="text-sm text-slate-400">Pengesahan masuk</p>
                                <p class="mt-2 text-2xl font-bold text-white">QR & Plat</p>
                            </div>
                            <div class="rounded-2xl border border-white/10 bg-slate-900/60 p-4">
                                <p class="text-sm text-slate-400">Pengurusan akses</p>
                                <p class="mt-2 text-2xl font-bold text-white">Masa Nyata</p>
                            </div>
                        </div>
                    </div>

                    <div class="rounded-[2rem] border border-cyan-400/20 bg-slate-900/75 p-7 shadow-2xl shadow-slate-950/30 md:p-8">
                        <p class="text-sm font-semibold uppercase tracking-[0.32em] text-cyan-300">Info Ringkas</p>
                        <h2 class="mt-4 text-2xl font-bold text-white">Tentang aplikasi ini</h2>
                        <p class="mt-4 text-sm leading-7 text-slate-300 md:text-base">
                            Sistem Pemantauan Kenderaan membantu institusi mengurus pendaftaran kenderaan pelajar secara lebih teratur dan selamat.
                            Pelajar boleh menghantar permohonan secara dalam talian, pihak pentadbiran dan pihak berkuasa membuat semakan serta kelulusan,
                            manakala pengawal mengesahkan kemasukan menggunakan pelekat digital berasaskan kod QR atau nombor plat.
                        </p>
                        <div class="mt-6 space-y-3 text-sm text-slate-300">
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Pendaftaran kenderaan pelajar secara berpusat</div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Aliran semakan dan kelulusan mengikut peranan</div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Pelekat QR digital untuk akses masuk kampus</div>
                            <div class="rounded-2xl border border-white/10 bg-white/5 px-4 py-3">Semakan pantas oleh pengawal di lokasi masuk</div>
                        </div>
                    </div>
                </section>

                <section class="mt-10">
                    <div class="mb-6 flex flex-col gap-2 sm:flex-row sm:items-end sm:justify-between">
                        <div>
                            <p class="text-sm font-semibold uppercase tracking-[0.32em] text-sky-300">Akses Sistem</p>
                            <h2 class="mt-2 text-3xl font-bold tracking-tight text-white">Pilih peranan untuk log masuk</h2>
                        </div>
                        <p class="max-w-2xl text-sm leading-6 text-slate-400">
                            Setiap panel direka khusus mengikut tanggungjawab pengguna supaya proses pengurusan kenderaan lebih tersusun dan cepat.
                        </p>
                    </div>

                    <div class="grid gap-6 md:grid-cols-2 xl:grid-cols-4">
                        @foreach ($roles as $role)
                            <article class="group flex h-full flex-col rounded-[1.75rem] border border-white/10 bg-slate-900/75 p-6 shadow-xl shadow-slate-950/25 transition duration-200 hover:-translate-y-1 hover:border-white/20 hover:bg-slate-900">
                                <div class="h-1.5 w-24 rounded-full bg-gradient-to-r {{ $role['accent'] }}"></div>
                                <div class="mt-5 inline-flex h-16 w-16 items-center justify-center rounded-2xl border border-white/10 bg-white/5 text-white shadow-lg shadow-slate-950/20">
                                    {!! $role['icon'] !!}
                                </div>
                                <h3 class="mt-5 text-2xl font-bold text-white">{{ $role['title'] }}</h3>
                                <p class="mt-3 flex-1 text-sm leading-7 text-slate-300">
                                    {{ $role['description'] }}
                                </p>
                                <a
                                    href="{{ $role['href'] }}"
                                    class="mt-6 inline-flex items-center justify-center rounded-2xl bg-white px-4 py-3 text-sm font-semibold text-slate-950 transition hover:bg-slate-200"
                                >
                                    {{ $role['button'] }}
                                </a>
                            </article>
                        @endforeach
                    </div>
                </section>

                <footer class="mt-10 border-t border-white/10 pt-6 text-sm text-slate-400">
                    <p>Sistem Pemantauan Kenderaan • ADTEC Melaka</p>
                </footer>
            </main>
        </div>
    </body>
</html>
