<?php

namespace App\Support;

final class ProgressiveStatus
{
    private const NEXT = [
        'queja' => ['pendiente' => 'en_proceso', 'en_proceso' => 'resuelto'],
        'inspeccion' => ['pendiente' => 'en_proceso', 'en_proceso' => 'resuelto'],
        'aseo' => ['pendiente' => 'en_proceso', 'en_proceso' => 'completado'],
    ];

    public static function next(string $flow, ?string $current): ?string
    {
        return self::NEXT[$flow][$current ?? ''] ?? null;
    }

    public static function isFinal(?string $status): bool
    {
        return in_array($status, ['resuelto', 'resuelta', 'atendido', 'atendida', 'cerrado', 'cerrada', 'completado', 'completada'], true);
    }
}
