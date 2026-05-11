@extends('layouts.app')

@section('title', 'Editar hábito')

@section('content')

<div class="max-w-xl mx-auto">
    <div class="mb-6">
        <a href="{{ route('habits.index') }}" class="inline-flex items-center gap-1.5 text-slate-400 hover:text-white transition-colors text-sm">
            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/>
            </svg>
            Volver al panel
        </a>
        <h1 class="text-2xl font-bold text-white mt-3">Editar hábito</h1>
    </div>

    <form action="{{ route('habits.update', $habit) }}" method="POST" class="glass rounded-2xl p-6 space-y-6">
        @csrf @method('PUT')

        {{-- Preview --}}
        <div class="flex items-center gap-4 p-4 rounded-xl bg-slate-800/50 border border-slate-700">
            <div id="preview-icon" class="text-3xl w-14 h-14 flex items-center justify-center rounded-xl transition-all"
                 style="background: {{ $habit->color }}22; border: 1px solid {{ $habit->color }}44">{{ $habit->icon }}</div>
            <div>
                <div id="preview-name" class="text-white font-semibold text-lg">{{ $habit->name }}</div>
                <div id="preview-desc" class="text-slate-400 text-sm">{{ $habit->description ?: 'Descripción opcional' }}</div>
            </div>
        </div>

        {{-- Name --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Nombre <span class="text-red-400">*</span></label>
            <input type="text" name="name" value="{{ old('name', $habit->name) }}" required maxlength="100"
                   oninput="document.getElementById('preview-name').textContent = this.value || 'Nombre del hábito'"
                   class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-600 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
            @error('name')
                <p class="text-red-400 text-sm mt-1">{{ $message }}</p>
            @enderror
        </div>

        {{-- Description --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Descripción</label>
            <input type="text" name="description" value="{{ old('description', $habit->description) }}" maxlength="255"
                   oninput="document.getElementById('preview-desc').textContent = this.value || 'Descripción opcional'"
                   class="w-full px-4 py-3 rounded-xl bg-slate-800 border border-slate-600 text-white placeholder-slate-500 focus:outline-none focus:border-indigo-500 transition-colors">
        </div>

        {{-- Icon picker --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Icono</label>
            <input type="hidden" name="icon" id="icon-input" value="{{ old('icon', $habit->icon) }}">
            <div class="grid grid-cols-8 gap-2">
                @foreach($icons as $icon)
                    <button type="button" onclick="selectIcon('{{ $icon }}')"
                            class="icon-btn w-10 h-10 rounded-lg text-xl flex items-center justify-center transition-all hover:scale-110 border-2 {{ old('icon', $habit->icon) === $icon ? 'border-indigo-500 bg-indigo-500/20' : 'border-transparent bg-slate-800 hover:bg-slate-700' }}"
                            data-icon="{{ $icon }}">{{ $icon }}</button>
                @endforeach
            </div>
        </div>

        {{-- Color picker --}}
        <div>
            <label class="block text-sm font-medium text-slate-300 mb-2">Color</label>
            <input type="hidden" name="color" id="color-input" value="{{ old('color', $habit->color) }}">
            <div class="flex flex-wrap gap-3">
                @foreach($colors as $color)
                    <button type="button" onclick="selectColor('{{ $color }}')"
                            class="color-btn w-9 h-9 rounded-full transition-all hover:scale-110 ring-offset-2 ring-offset-slate-900 {{ old('color', $habit->color) === $color ? 'ring-2 ring-white scale-110' : '' }}"
                            style="background: {{ $color }}"
                            data-color="{{ $color }}"></button>
                @endforeach
            </div>
        </div>

        <div class="flex gap-3">
            <a href="{{ route('habits.index') }}"
               class="flex-1 py-3 rounded-xl font-semibold text-slate-300 text-center transition-all hover:bg-slate-700 border border-slate-600">
                Cancelar
            </a>
            <button type="submit"
                    class="flex-1 py-3 rounded-xl font-semibold text-white transition-all hover:opacity-90 active:scale-95"
                    style="background: #6366f1">
                Guardar cambios
            </button>
        </div>
    </form>
</div>

@endsection

@push('scripts')
<script>
function selectIcon(icon) {
    document.getElementById('icon-input').value = icon;
    document.getElementById('preview-icon').textContent = icon;
    document.querySelectorAll('.icon-btn').forEach(btn => {
        if (btn.dataset.icon === icon) {
            btn.classList.add('border-indigo-500', 'bg-indigo-500/20');
            btn.classList.remove('border-transparent', 'bg-slate-800');
        } else {
            btn.classList.remove('border-indigo-500', 'bg-indigo-500/20');
            btn.classList.add('border-transparent', 'bg-slate-800');
        }
    });
}

function selectColor(color) {
    document.getElementById('color-input').value = color;
    const preview = document.getElementById('preview-icon');
    preview.style.background = color + '22';
    preview.style.border = `1px solid ${color}44`;
    document.querySelectorAll('.color-btn').forEach(btn => {
        if (btn.dataset.color === color) {
            btn.classList.add('ring-2', 'ring-white', 'scale-110');
        } else {
            btn.classList.remove('ring-2', 'ring-white', 'scale-110');
        }
    });
}
</script>
@endpush
