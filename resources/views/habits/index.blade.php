@extends('layouts.app')

@section('title', 'Panel de hábitos')

@section('content')

{{-- Header stats --}}
<div class="mb-8">
    <h1 class="text-3xl font-bold text-white mb-1">Panel de hábitos</h1>
    <p class="text-slate-400">{{ now()->isoFormat('dddd, D [de] MMMM [de] YYYY') }}</p>

    <div class="grid grid-cols-3 gap-4 mt-6">
        <div class="glass rounded-2xl p-5 text-center">
            <div class="text-4xl font-bold text-white">{{ $stats['total'] }}</div>
            <div class="text-sm text-slate-400 mt-1">Hábitos activos</div>
        </div>
        <div class="glass rounded-2xl p-5 text-center">
            <div class="text-4xl font-bold text-emerald-400">{{ $stats['completed_today'] }}</div>
            <div class="text-sm text-slate-400 mt-1">Completados hoy</div>
        </div>
        <div class="glass rounded-2xl p-5 text-center">
            <div class="text-4xl font-bold text-orange-400">{{ $stats['best_streak'] }}</div>
            <div class="text-sm text-slate-400 mt-1">Mejor racha activa</div>
        </div>
    </div>

    @if($stats['total'] > 0)
    <div class="mt-4 glass rounded-2xl p-4">
        <div class="flex justify-between text-sm mb-2">
            <span class="text-slate-400">Progreso de hoy</span>
            <span class="text-white font-semibold">{{ $stats['completed_today'] }} / {{ $stats['total'] }}</span>
        </div>
        <div class="h-3 bg-slate-700 rounded-full overflow-hidden">
            <div class="h-full rounded-full progress-bar bg-gradient-to-r from-indigo-500 to-emerald-500"
                 style="width: {{ $stats['total'] > 0 ? round(($stats['completed_today'] / $stats['total']) * 100) : 0 }}%"></div>
        </div>
    </div>
    @endif
</div>

{{-- Habits grid --}}
@if($habits->isEmpty())
    <div class="glass rounded-3xl p-16 text-center">
        <div class="text-6xl mb-4">🌱</div>
        <h2 class="text-xl font-semibold text-white mb-2">Aún no tienes hábitos</h2>
        <p class="text-slate-400 mb-6">Comienza agregando tu primer hábito diario.</p>
        <a href="{{ route('habits.create') }}"
           class="inline-flex items-center gap-2 px-6 py-3 rounded-xl font-semibold text-white"
           style="background: #6366f1">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M12 4v16m8-8H4"/>
            </svg>
            Crear primer hábito
        </a>
    </div>
@else
    <div class="grid gap-4 sm:grid-cols-2">
        @foreach($habits as $habit)
            @php
                $done      = $habit->isCompletedToday();
                $streak    = $habit->getCurrentStreak();
                $rate      = $habit->getCompletionRateLast30Days();
            @endphp
            <div class="glass glass-hover rounded-2xl p-5 flex flex-col gap-4" id="habit-{{ $habit->id }}">
                <div class="flex items-start justify-between gap-3">
                    <div class="flex items-center gap-3 min-w-0">
                        <div class="text-3xl shrink-0 w-12 h-12 flex items-center justify-center rounded-xl"
                             style="background: {{ $habit->color }}22; border: 1px solid {{ $habit->color }}44">
                            {{ $habit->icon }}
                        </div>
                        <div class="min-w-0">
                            <a href="{{ route('habits.show', $habit) }}"
                               class="font-semibold text-white hover:text-indigo-300 transition-colors truncate block">
                                {{ $habit->name }}
                            </a>
                            @if($habit->description)
                                <p class="text-sm text-slate-400 truncate">{{ $habit->description }}</p>
                            @endif
                        </div>
                    </div>

                    {{-- Toggle button --}}
                    <button
                        onclick="toggleHabit({{ $habit->id }}, this)"
                        class="check-btn shrink-0 w-11 h-11 rounded-xl flex items-center justify-center border-2 transition-all {{ $done ? 'done border-transparent' : 'border-slate-600 hover:border-slate-400' }}"
                        style="{{ $done ? 'background:' . $habit->color : '' }}"
                        title="{{ $done ? 'Marcar como no completado' : 'Marcar como completado' }}">
                        @if($done)
                            <svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/>
                            </svg>
                        @else
                            <svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/>
                            </svg>
                        @endif
                    </button>
                </div>

                <div class="flex items-center justify-between text-sm">
                    {{-- Streak --}}
                    <div class="flex items-center gap-2">
                        @if($streak > 0)
                            <span class="streak-badge text-white px-2.5 py-1 rounded-lg text-xs font-bold flex items-center gap-1">
                                🔥 {{ $streak }} {{ $streak === 1 ? 'día' : 'días' }}
                            </span>
                        @else
                            <span class="text-slate-500 text-xs">Sin racha activa</span>
                        @endif
                    </div>

                    {{-- Rate --}}
                    <div class="flex items-center gap-1 text-slate-400 text-xs">
                        <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
                        </svg>
                        {{ $rate }}% últimos 30 días
                    </div>
                </div>

                {{-- Mini progress bar --}}
                <div class="h-1.5 bg-slate-700 rounded-full overflow-hidden">
                    <div class="h-full rounded-full progress-bar"
                         style="width: {{ $rate }}%; background: {{ $habit->color }}"></div>
                </div>

                {{-- Actions --}}
                <div class="flex items-center justify-end gap-2 pt-1 border-t border-slate-700/50">
                    <a href="{{ route('habits.show', $habit) }}" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 rounded">
                        Ver detalles
                    </a>
                    <a href="{{ route('habits.edit', $habit) }}" class="text-xs text-slate-400 hover:text-white transition-colors px-2 py-1 rounded">
                        Editar
                    </a>
                    <form action="{{ route('habits.destroy', $habit) }}" method="POST"
                          onsubmit="return confirm('¿Eliminar este hábito y todo su historial?')">
                        @csrf @method('DELETE')
                        <button type="submit" class="text-xs text-red-400 hover:text-red-300 transition-colors px-2 py-1 rounded">
                            Eliminar
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    </div>
@endif

@endsection

@push('scripts')
<script>
const csrfToken = document.querySelector('meta[name="csrf-token"]').content;

async function toggleHabit(id, btn) {
    btn.disabled = true;
    try {
        const res = await fetch(`/habits/${id}/toggle`, {
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json', 'Content-Type': 'application/json' }
        });
        const data = await res.json();

        const color = btn.closest('[id^="habit-"]').querySelector('[style*="background:"]')?.style.background
                   || btn.style.background || '#6366f1';

        if (data.completed) {
            btn.classList.add('done');
            btn.classList.remove('border-slate-600', 'hover:border-slate-400');
            btn.classList.add('border-transparent');
            btn.style.background = getHabitColor(id);
            btn.innerHTML = `<svg class="w-5 h-5 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>`;
        } else {
            btn.classList.remove('done', 'border-transparent');
            btn.classList.add('border-slate-600', 'hover:border-slate-400');
            btn.style.background = '';
            btn.innerHTML = `<svg class="w-5 h-5 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"/></svg>`;
        }

        // Update streak badge
        const card = document.getElementById(`habit-${id}`);
        const streakContainer = card.querySelector('.flex.items-center.gap-2');
        if (data.streak > 0) {
            streakContainer.innerHTML = `<span class="streak-badge text-white px-2.5 py-1 rounded-lg text-xs font-bold flex items-center gap-1">🔥 ${data.streak} ${data.streak === 1 ? 'día' : 'días'}</span>`;
        } else {
            streakContainer.innerHTML = `<span class="text-slate-500 text-xs">Sin racha activa</span>`;
        }
    } catch(e) {
        console.error(e);
    } finally {
        btn.disabled = false;
    }
}

function getHabitColor(id) {
    const card = document.getElementById(`habit-${id}`);
    const iconDiv = card?.querySelector('[style*="border:"]');
    if (!iconDiv) return '#6366f1';
    const border = iconDiv.style.border;
    const match = border.match(/#[0-9a-fA-F]{6}/);
    return match ? match[0] : '#6366f1';
}
</script>
@endpush
