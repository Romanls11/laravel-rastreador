<?php

namespace App\Http\Controllers;

use App\Models\Habit;
use App\Models\HabitCompletion;
use Carbon\Carbon;
use Illuminate\Http\Request;

class HabitController extends Controller
{
    public function index()
    {
        $habits = Habit::where('active', true)->orderBy('created_at')->get();

        $stats = [
            'total'         => $habits->count(),
            'completed_today' => $habits->filter(fn($h) => $h->isCompletedToday())->count(),
            'best_streak'   => $habits->max(fn($h) => $h->getCurrentStreak()) ?? 0,
        ];

        return view('habits.index', compact('habits', 'stats'));
    }

    public function create()
    {
        $icons = ['⭐', '💪', '📚', '🏃', '🧘', '💧', '🥗', '😴', '🎯', '🎨', '🎵', '🌿', '🔥', '💡', '✍️', '🧹'];
        $colors = [
            '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
            '#f97316', '#eab308', '#22c55e', '#14b8a6',
            '#3b82f6', '#06b6d4', '#84cc16', '#a855f7',
        ];

        return view('habits.create', compact('icons', 'colors'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color'       => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'icon'        => 'required|string|max:10',
        ]);

        Habit::create($validated);

        return redirect()->route('habits.index')->with('success', 'Hábito creado exitosamente.');
    }

    public function edit(Habit $habit)
    {
        $icons = ['⭐', '💪', '📚', '🏃', '🧘', '💧', '🥗', '😴', '🎯', '🎨', '🎵', '🌿', '🔥', '💡', '✍️', '🧹'];
        $colors = [
            '#6366f1', '#8b5cf6', '#ec4899', '#ef4444',
            '#f97316', '#eab308', '#22c55e', '#14b8a6',
            '#3b82f6', '#06b6d4', '#84cc16', '#a855f7',
        ];

        return view('habits.edit', compact('habit', 'icons', 'colors'));
    }

    public function update(Request $request, Habit $habit)
    {
        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:255',
            'color'       => 'required|string|regex:/^#[0-9a-fA-F]{6}$/',
            'icon'        => 'required|string|max:10',
        ]);

        $habit->update($validated);

        return redirect()->route('habits.index')->with('success', 'Hábito actualizado exitosamente.');
    }

    public function destroy(Habit $habit)
    {
        $habit->delete();

        return redirect()->route('habits.index')->with('success', 'Hábito eliminado.');
    }

    public function toggle(Request $request, Habit $habit)
    {
        $date = $request->input('date', Carbon::today()->toDateString());

        $existing = HabitCompletion::where('habit_id', $habit->id)
            ->where('completed_date', $date)
            ->first();

        if ($existing) {
            $existing->delete();
            $completed = false;
        } else {
            HabitCompletion::create(['habit_id' => $habit->id, 'completed_date' => $date]);
            $completed = true;
        }

        if ($request->expectsJson()) {
            return response()->json([
                'completed' => $completed,
                'streak'    => $habit->getCurrentStreak(),
            ]);
        }

        return back();
    }

    public function show(Habit $habit)
    {
        // Last 30 days calendar
        $days = collect();
        for ($i = 29; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $days->push([
                'date'      => $date->toDateString(),
                'label'     => $date->format('d'),
                'month'     => $date->format('M'),
                'dayOfWeek' => $date->format('D'),
                'completed' => $habit->completions()
                    ->where('completed_date', $date->toDateString())
                    ->exists(),
                'isToday'   => $date->isToday(),
            ]);
        }

        return view('habits.show', [
            'habit'        => $habit,
            'days'         => $days,
            'streak'       => $habit->getCurrentStreak(),
            'longestStreak' => $habit->getLongestStreak(),
            'total'        => $habit->getTotalCompletions(),
            'rate'         => $habit->getCompletionRateLast30Days(),
        ]);
    }
}
