<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Habit extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'color', 'icon', 'active'];

    protected $casts = ['active' => 'boolean'];

    public function completions()
    {
        return $this->hasMany(HabitCompletion::class);
    }

    public function isCompletedToday(): bool
    {
        return $this->completions()
            ->where('completed_date', Carbon::today()->toDateString())
            ->exists();
    }

    public function getCurrentStreak(): int
    {
        $dates = $this->completions()
            ->orderByDesc('completed_date')
            ->pluck('completed_date')
            ->map(fn($d) => Carbon::parse($d))
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $streak = 0;
        $check = Carbon::today();

        // Allow today or yesterday as starting point
        if (!$dates->contains(fn($d) => $d->isSameDay($check))) {
            $check = Carbon::yesterday();
            if (!$dates->contains(fn($d) => $d->isSameDay($check))) {
                return 0;
            }
        }

        foreach ($dates as $date) {
            if ($date->isSameDay($check)) {
                $streak++;
                $check = $check->copy()->subDay();
            } elseif ($date->lt($check)) {
                break;
            }
        }

        return $streak;
    }

    public function getLongestStreak(): int
    {
        $dates = $this->completions()
            ->orderBy('completed_date')
            ->pluck('completed_date')
            ->map(fn($d) => Carbon::parse($d))
            ->values();

        if ($dates->isEmpty()) {
            return 0;
        }

        $longest = 1;
        $current = 1;

        for ($i = 1; $i < $dates->count(); $i++) {
            if ($dates[$i]->diffInDays($dates[$i - 1]) === 1) {
                $current++;
                $longest = max($longest, $current);
            } else {
                $current = 1;
            }
        }

        return $longest;
    }

    public function getTotalCompletions(): int
    {
        return $this->completions()->count();
    }

    public function getCompletionRateLast30Days(): int
    {
        $completed = $this->completions()
            ->where('completed_date', '>=', Carbon::today()->subDays(29)->toDateString())
            ->count();

        return (int) round(($completed / 30) * 100);
    }
}
