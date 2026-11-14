@php
    $brand = \App\Models\Company::current()?->name ?? config('app.name');
@endphp

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="h-full">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="theme-color" content="#f8f8fc">
    <link rel="icon" type="image/svg+xml" href="{{ asset('images/leadabox-mark.svg') }}">
    <title>{{ $brand }} — {{ __('auth.login_subtitle') }}</title>

    <script>
        (() => {
            const stored = localStorage.getItem('theme');
            const dark = stored ? stored === 'dark' : window.matchMedia('(prefers-color-scheme: dark)').matches;
            document.documentElement.classList.toggle('dark', dark);
        })();
    </script>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    <style>
        .hero-grid { background-image: linear-gradient(rgba(99, 91, 255, .045) 1px, transparent 1px), linear-gradient(90deg, rgba(99, 91, 255, .045) 1px, transparent 1px); background-size: 42px 42px; mask-image: linear-gradient(to bottom, black, transparent 88%); }
        .hero-particles { position: absolute; inset: 0; height: 100%; width: 100%; opacity: .72; }
        .hero-orb { animation: orb-drift 11s ease-in-out infinite alternate; }
        .hero-orb-delayed { animation-delay: -5s; }
        .hero-enter { animation: hero-enter .85s cubic-bezier(.2,.8,.2,1) both; }
        .hero-enter-delay { animation: hero-enter .9s .16s cubic-bezier(.2,.8,.2,1) both; }
        .preview-float { animation: preview-float 6s ease-in-out infinite; }
        .preview-pulse { animation: preview-pulse 2.8s ease-in-out infinite; }
        @keyframes hero-enter { from { opacity: 0; transform: translateY(22px); } to { opacity: 1; transform: translateY(0); } }
        @keyframes orb-drift { from { transform: translate3d(-10px, 8px, 0) scale(.96); } to { transform: translate3d(14px, -12px, 0) scale(1.06); } }
        @keyframes preview-float { 0%, 100% { transform: translateY(0) rotate(-1deg); } 50% { transform: translateY(-9px) rotate(0deg); } }
        @keyframes preview-pulse { 0%, 100% { box-shadow: 0 0 0 0 rgba(108, 99, 255, .18); } 50% { box-shadow: 0 0 0 7px rgba(108, 99, 255, 0); } }
        @media (prefers-reduced-motion: reduce) { *, *::before, *::after { animation-duration: .01ms !important; animation-iteration-count: 1 !important; scroll-behavior: auto !important; } }
    </style>
</head>
<body class="min-h-full overflow-x-hidden bg-[#f8f8fc] font-sans text-slate-900 antialiased dark:bg-slate-950 dark:text-slate-100">
    <div class="pointer-events-none fixed inset-0 -z-0 overflow-hidden" aria-hidden="true">
        <div class="hero-grid absolute inset-0 opacity-80 dark:opacity-30"></div>
        <canvas id="hero-particles" class="hero-particles" aria-hidden="true"></canvas>
        <div class="hero-orb absolute -left-32 top-20 size-[28rem] rounded-full bg-violet-300/25 blur-[100px] dark:bg-indigo-600/15"></div>
        <div class="hero-orb hero-orb-delayed absolute -right-24 bottom-0 size-[30rem] rounded-full bg-sky-200/35 blur-[110px] dark:bg-violet-700/15"></div>
    </div>

    <header class="relative z-10 mx-auto flex h-20 max-w-7xl items-center justify-between px-6 sm:px-10">
        <a href="{{ route('home') }}" class="flex items-center gap-2.5 text-lg font-semibold tracking-tight" aria-label="{{ $brand }} home">
            <img src="{{ asset('images/leadabox-mark.svg') }}" alt="" class="size-9" width="36" height="36">
            <span>{{ $brand }}</span>
        </a>
        <a href="{{ route('login') }}" class="group inline-flex items-center gap-2 rounded-full border border-slate-200/80 bg-white/70 px-4 py-2 text-sm font-semibold shadow-sm backdrop-blur transition hover:border-violet-300 hover:bg-white dark:border-slate-700 dark:bg-slate-900/70 dark:hover:border-violet-500">
            {{ __('auth.log_in') }}
            <span aria-hidden="true" class="transition group-hover:translate-x-0.5">↗</span>
        </a>
    </header>

    <main class="relative z-10 mx-auto grid min-h-[calc(100svh-5rem)] max-w-7xl items-center gap-14 px-6 pb-16 pt-10 sm:px-10 lg:grid-cols-[1.03fr_.97fr] lg:gap-8 lg:pb-20 lg:pt-4">
        <section class="hero-enter relative max-w-2xl">
            <div class="mb-7 inline-flex items-center gap-2 rounded-full border border-violet-200/80 bg-white/70 px-3.5 py-1.5 text-xs font-semibold tracking-wide text-violet-700 shadow-sm backdrop-blur dark:border-violet-400/20 dark:bg-slate-900/70 dark:text-violet-200">
                <span class="preview-pulse size-2 rounded-full bg-violet-500"></span>
                A clearer way to run your business
            </div>
            <h1 class="max-w-2xl text-[clamp(3.25rem,7vw,6.2rem)] font-semibold leading-[.98] tracking-[-.065em] text-slate-950 dark:text-white">
                From first hello to <span class="bg-gradient-to-r from-violet-600 via-indigo-500 to-sky-500 bg-clip-text text-transparent">paid.</span>
            </h1>
            <p class="mt-7 max-w-xl text-lg leading-8 text-slate-600 dark:text-slate-300 sm:text-xl">
                The shared management workspace for Leadabox customers and team. Keep customer relationships, quotes, projects and invoices moving together.
            </p>
            <div class="mt-9 flex flex-wrap items-center gap-4">
                <a href="{{ route('login') }}" class="group inline-flex items-center gap-3 rounded-xl bg-slate-950 px-6 py-3.5 text-sm font-semibold text-white shadow-xl shadow-violet-900/15 transition hover:-translate-y-0.5 hover:bg-violet-700 dark:bg-white dark:text-slate-950 dark:hover:bg-violet-100">
                    {{ __('auth.log_in') }}
                    <span aria-hidden="true" class="transition group-hover:translate-x-1">→</span>
                </a>
                <span class="text-sm text-slate-500 dark:text-slate-400">One calm flow for customers and the Leadabox team.</span>
            </div>
            <div class="mt-12 flex items-center gap-4 border-t border-slate-200/80 pt-6 text-sm text-slate-500 dark:border-slate-800 dark:text-slate-400">
                <div class="flex -space-x-2" aria-hidden="true">
                    <span class="grid size-8 place-items-center rounded-full border-2 border-[#f8f8fc] bg-violet-100 text-xs font-bold text-violet-700 dark:border-slate-950 dark:bg-violet-900 dark:text-violet-200">L</span>
                    <span class="grid size-8 place-items-center rounded-full border-2 border-[#f8f8fc] bg-sky-100 text-xs font-bold text-sky-700 dark:border-slate-950 dark:bg-sky-900 dark:text-sky-200">Q</span>
                    <span class="grid size-8 place-items-center rounded-full border-2 border-[#f8f8fc] bg-emerald-100 text-xs font-bold text-emerald-700 dark:border-slate-950 dark:bg-emerald-900 dark:text-emerald-200">✓</span>
                </div>
                <span>Lead. Quote. Deliver. Get paid.</span>
            </div>
        </section>

        <section class="hero-enter-delay relative mx-auto w-full max-w-[38rem]" aria-label="Leadabox workspace illustration">
            <div class="absolute inset-8 rounded-[3rem] bg-gradient-to-br from-violet-300/50 via-indigo-200/25 to-sky-200/50 blur-3xl dark:from-violet-700/20 dark:via-indigo-800/15 dark:to-sky-700/20"></div>
            <div class="preview-float relative">
                <img src="{{ asset('images/leadabox-workflow-hero.svg') }}" alt="Leadabox connects customer leads, the team, projects, quotes and invoices in one shared workspace." class="relative z-10 h-auto w-full drop-shadow-[0_30px_55px_rgba(50,44,105,.18)]" width="900" height="720">
            </div>
            <div class="absolute -left-2 top-[20%] hidden items-center gap-2.5 rounded-2xl border border-white bg-white/90 px-3.5 py-3 shadow-xl shadow-slate-900/10 backdrop-blur sm:flex dark:border-slate-700 dark:bg-slate-900/90">
                <span class="grid size-8 place-items-center rounded-xl bg-emerald-100 text-emerald-700 dark:bg-emerald-950 dark:text-emerald-300">✓</span><span><span class="block text-[10px] text-slate-500">Customer + team</span><span class="block text-xs font-semibold">Working in sync</span></span>
            </div>
            <div class="absolute -right-1 bottom-[10%] hidden rounded-2xl border border-white bg-white/90 px-4 py-3 shadow-xl shadow-slate-900/10 backdrop-blur sm:block dark:border-slate-700 dark:bg-slate-900/90">
                <span class="block text-[10px] text-slate-500">Lead to invoice</span><span class="mt-1 block text-xs font-semibold text-violet-700 dark:text-violet-300">One smooth workflow ✦</span>
            </div>
        </section>
    </main>
    <script>
        (() => {
            const canvas = document.getElementById('hero-particles');
            const context = canvas?.getContext('2d');

            if (!canvas || !context || window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
                return;
            }

            let width = 0;
            let height = 0;
            let pixelRatio = 1;
            let pointerX = 0;
            let pointerY = 0;
            let particles = [];

            const createParticles = () => Array.from(
                { length: Math.min(150, Math.max(72, Math.round(width * height / 10500))) },
                () => ({ x: Math.random(), y: Math.random(), depth: Math.random(), speed: 0.0015 + Math.random() * 0.0025 }),
            );

            const resize = () => {
                width = window.innerWidth;
                height = window.innerHeight;
                pixelRatio = Math.min(window.devicePixelRatio || 1, 2);
                canvas.width = Math.round(width * pixelRatio);
                canvas.height = Math.round(height * pixelRatio);
                context.setTransform(pixelRatio, 0, 0, pixelRatio, 0, 0);
                particles = createParticles();
            };

            const render = () => {
                context.clearRect(0, 0, width, height);

                const projected = particles.map((particle) => {
                    particle.depth += particle.speed;

                    if (particle.depth > 1) {
                        particle.depth = 0;
                        particle.x = Math.random();
                        particle.y = Math.random();
                    }

                    const scale = 0.28 + particle.depth * 1.08;

                    return {
                        x: width / 2 + (particle.x - 0.5) * width * scale + pointerX * particle.depth * 20,
                        y: height / 2 + (particle.y - 0.5) * height * scale + pointerY * particle.depth * 20,
                        radius: 0.7 + particle.depth * 1.5,
                        depth: particle.depth,
                    };
                });

                projected.forEach((point, index) => {
                    for (let nextIndex = index + 1; nextIndex < projected.length; nextIndex += 1) {
                        const next = projected[nextIndex];
                        const distance = Math.hypot(point.x - next.x, point.y - next.y);

                        if (distance < 92) {
                            context.beginPath();
                            context.moveTo(point.x, point.y);
                            context.lineTo(next.x, next.y);
                            context.strokeStyle = `rgba(112, 101, 235, ${(1 - distance / 92) * 0.12 * Math.min(point.depth + 0.25, next.depth + 0.25)})`;
                            context.lineWidth = 0.7;
                            context.stroke();
                        }
                    }

                    context.beginPath();
                    context.arc(point.x, point.y, point.radius, 0, Math.PI * 2);
                    context.fillStyle = `rgba(111, 99, 235, ${0.16 + point.depth * 0.5})`;
                    context.fill();
                });

                window.requestAnimationFrame(render);
            };

            window.addEventListener('resize', resize, { passive: true });
            window.addEventListener('pointermove', (event) => {
                pointerX = (event.clientX / width - 0.5) * 2;
                pointerY = (event.clientY / height - 0.5) * 2;
            }, { passive: true });
            resize();
            render();
        })();
    </script>
</body>
</html>
