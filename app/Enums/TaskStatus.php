<?php

namespace App\Enums;

enum TaskStatus: string
{
    case Todo = 'todo';
    case InProgress = 'in_progress';
    case Done = 'done';

    public function label(): string
    {
        return match ($this) {
            self::Todo => 'To do',
            self::InProgress => 'In progress',
            self::Done => 'Done',
        };
    }

    public function badgeClasses(): string
    {
        return match ($this) {
            self::Todo => 'bg-slate-100 text-slate-700 ring-slate-500/20',
            self::InProgress => 'bg-indigo-50 text-indigo-700 ring-indigo-600/20',
            self::Done => 'bg-emerald-50 text-emerald-700 ring-emerald-600/20',
        };
    }

    /**
     * @return array<int, string>
     */
    public static function values(): array
    {
        return array_column(self::cases(), 'value');
    }
}
