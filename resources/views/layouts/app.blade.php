<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Rastreador de Hábitos')</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    animation: {
                        'bounce-once': 'bounce 0.5s ease-in-out 1',
                        'fade-in': 'fadeIn 0.3s ease-in-out',
                    },
                    keyframes: {
                        fadeIn: { '0%': { opacity: '0', transform: 'translateY(-8px)' }, '100%': { opacity: '1', transform: 'translateY(0)' } }
                    }
                }
            }
        }
    </script>
    <style>
        body { background-color: #0f172a; }
        .glass { background: rgba(255,255,255,0.05); backdrop-filter: blur(12px); border: 1px solid rgba(255,255,255,0.1); }
        .glass-hover:hover { background: rgba(255,255,255,0.08); transition: all 0.2s; }
        .streak-badge { background: linear-gradient(135deg, #f97316, #ef4444); }
        .progress-bar { transition: width 0.6s ease-out; }
        .check-btn { transition: all 0.2s cubic-bezier(0.34, 1.56, 0.64, 1); }
        .check-btn:active { transform: scale(0.9); }
        .check-btn.done { transform: scale(1.05); }
    </style>
</head>
<body class="min-h-screen text-slate-100">

    <nav class="glass sticky top-0 z-50 px-4 py-3">
        <div class="max-w-5xl mx-auto flex items-center justify-between">
            <a href="{{ route('habits.index') }}" class="flex items-center gap-2 text-xl font-bold text-white">
                <span class="text-2xl">🎯</span>
                <span>MisHábitos</span>
            </a>
            <a href="{{ route('habits.create') }}"
               class="flex items-center gap-1.5 px-4 py-2 rounded-xl text-sm font-semibold text-white transition-all"
               style="background: #6366f1">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
                </svg>
                Nuevo hábito
            </a>
        </div>
    </nav>

    <main class="max-w-5xl mx-auto px-4 py-8">
        @if(session('success'))
            <div class="mb-6 flex items-center gap-3 px-4 py-3 rounded-xl bg-emerald-500/20 border border-emerald-500/30 text-emerald-300 animate-fade-in">
                <svg class="w-5 h-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                {{ session('success') }}
            </div>
        @endif

        @yield('content')
    </main>

    <footer class="text-center py-6 text-slate-600 text-sm">
        MisHábitos &mdash; Construye mejores rutinas cada día
    </footer>

    @stack('scripts')
</body>
</html>
