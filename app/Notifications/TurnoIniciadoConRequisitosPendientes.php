<?php

namespace App\Notifications;

use App\Models\Unidad;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;

class TurnoIniciadoConRequisitosPendientes extends Notification
{
    use Queueable;

    public function __construct(
        private readonly Unidad $unidad,
        private readonly User $operador,
        private readonly array $pendientes,
    ) {}

    public function via(object $notifiable): array
    {
        return ['database'];
    }

    public function toDatabase(object $notifiable): array
    {
        return [
            'titulo' => 'Turno iniciado con requisitos pendientes',
            'mensaje' => sprintf(
                '%s inició turno en %s. Pendiente: %s.',
                $this->operador->name,
                $this->unidad->economico,
                implode(', ', $this->pendientes),
            ),
            'unidad_id' => $this->unidad->id,
            'unidad' => $this->unidad->economico,
            'operador_id' => $this->operador->id,
            'operador' => $this->operador->name,
            'requisitos_pendientes' => $this->pendientes,
        ];
    }
}
