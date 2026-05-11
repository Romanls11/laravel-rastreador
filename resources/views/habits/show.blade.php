@extends('layouts.app')

@section('title', $habit->name)

@section('content')

<div class="max-w-2xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('habits.index') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-white transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al panel
        </a>
    </div>

    {{-- Habit header --}}
    <div class="glass rounded-2xl p-6 mb-6">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-4">
                <div class="text-4xl w-16 h-16 flex items-center justify-center rounded-xl"
                     style="background: {{ $habit->color }}22; border: 2px solid {{ $habit->color }}44">
                    {{ $habit->icon }}
                </div>
                <div>
                    <h1 class="text-2xl font-bold text-white">{{ $habit->name }}</h1>
                    @if($habit->description)
                        <p class="text-slate-400 mt-1">{{ $habit->description }}</p>
                    @endif
                    <p class="text-slate-500 text-xs mt-1">Creado {{ $habit->created_at->diffForHumans() }}</p>
                </div>
            </div>
            <a href="{{ route('habits.edit', $habit) }}"
               class="px-3 py-2 rounded-lg text-sm text-slate-300 hover:text-white border border-slate-600 hover:border-slate-400 transition-all">
                Editar
            </a>
        </div>
    </div>

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-3 mb-6">
        <div class="glass rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-orange-400">{{ $streak }}</div>
            <div class="text-xs text-slate-400 mt-1">Racha actual</div>
        </div>
        <div class="glass rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-yellow-400">{{ $longestStreak }}</div>
            <div class="text-xs text-slate-400 mt-1">Racha máxima</div>
        </div>
        <div class="glass rounded-xl p-4 text-center">
            <div class="text-3xl font-bold text-emerald-400">{{ $total }}</div>
            <div class="text-xs text-slate-400 mt-1">Total completados</div>
        </div>
        <div class="glass rounded-xl p-4 text-center">
            <div class="text-3xl font-bold" style="color: {{ $habit->color }}">{{ $rate }}%</div>
            <div class="text-xs text-slate-400 mt-1">Últimos 30 días</div>
        </div>
    </div>

    {{-- 30-day calendar --}}
    <div class="glass rounded-2xl p-6">
        <h2 class="text-lg font-semibold text-white mb-4">Últimos 30 días</h2>

        <div class="grid grid-cols-7 gap-1.5">
            @foreach(['Dom','Lun','Mar','Mié','Jue','Vie','Sáb'] as $label)
                <div class="text-center text-xs text-slate-500 py-1">{{ $label }}</div>
            @endforeach

            {{-- Fill empty cells before first day --}}
            @php
                $firstDay = \Carbon\Carbon::parse($days->first()['date'])->dayOfWeek; // 0=Sun
            @endphp
            @for($i = 0; $i < $firstDay; $i++)
                <div></div>
            @endfor

            @foreach($days as $day)
                <button
                    onclick="toggleDay('{{ $day['date'] }}')"
                    data-date="{{ $day['date'] }}"
                    class="day-btn aspect-square rounded-lg flex items-center justify-center text-sm font-medium transition-all hover:scale-105
                        {{ $day['isToday'] ? 'ring-2 ring-white ring-offset-1 ring-offset-slate-900' : '' }}
                        {{ $day['completed'] ? 'text-white' : 'text-slate-500 bg-slate-800 hover:bg-slate-700' }}"
                    style="{{ $day['completed'] ? 'background:' . $habit->color : '' }}"
                    title="{{ $day['date'] }}">
                    {{ $day['label'] }}
                </button>
            @endforeach
        </div>

        <div class="flex items-center justify-end gap-4 mt-4 text-xs text-slate-500">
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded bg-slate-800 inline-block"></span> No completado
            </span>
            <span class="flex items-center gap-1.5">
                <span class="w-3 h-3 rounded inline-block" style="background: {{ $habit->color }}"></span> Completado
            </span>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;
const habitColor = '{{ $habit->color }}';

async function toggleDay(date) {
    const btn = document.querySelector(`[data-date="${date}"]`);
    if (!btn) return;

    try {
        const res = await fetch(`/habits/{{ $habit->id }}/toggle`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' },
            body: JSON.stringify({ date })
        });
        const data = await res.json();

        if (data.completed) {
            btn.style.background = habitColor;
            btn.classList.remove('text-slate-500', 'bg-slate-800', 'hover:bg-slate-700');
            btn.classList.add('text-white');
        } else {
            btn.style.background = '';
            btn.classList.add('text-slate-500', 'bg-slate-800', 'hover:bg-slate-700');
            btn.classList.remove('text-white');
        }

        // Update streak display
        document.querySelector('.text-orange-400').textContent = data.streak;
    } catch(e) {
        console.error(e);
    }
}
</script>
@endpush
