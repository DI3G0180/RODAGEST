<?php

namespace App\Support;

use App\Models\Queja;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class OperationalMail
{
    public function criticalReport(Queja $report): void
    {
        $recipients = User::query()->where('role', 'admin')->where('activo', true)->pluck('email')->all();
        $report->loadMissing('unidad');
        $this->send($recipients, 'RODAGEST: nuevo reporte crítico', sprintf(
            "Se registró un reporte crítico.\nECO: %s\nÁrea: %s\nTipo: %s\nDescripción: %s",
            $report->eco ?: $report->unidad?->economico ?: 'Sin ECO',
            $report->area ?: $report->area_reportada ?: 'Sin área',
            $report->tipo_queja,
            $report->descripcion,
        ));
    }

    public function reportStatusChanged(Queja $report): void
    {
        $report->loadMissing(['operador', 'unidad']);
        $recipient = $report->operador?->email;
        if (! $recipient) return;

        $this->send([$recipient], 'RODAGEST: actualización de reporte', sprintf(
            "El estado de tu reporte cambió a: %s.\nECO: %s\nTipo: %s",
            $report->estado,
            $report->eco ?: $report->unidad?->economico ?: 'Sin ECO',
            $report->tipo_queja,
        ));
    }

    public function employeeCreated(User $employee): void
    {
        $this->send([$employee->email], 'Bienvenido a RODAGEST', sprintf(
            "Hola %s, tu cuenta de RODAGEST ya está activa. Inicia sesión con tu correo y la contraseña que te entregó el Administrador. Si necesitas restablecerla, solicítalo directamente al Administrador.",
            $employee->name,
        ));
    }

    private function send(array $recipients, string $subject, string $body): void
    {
        foreach (array_unique(array_filter($recipients, fn ($email) => filter_var($email, FILTER_VALIDATE_EMAIL))) as $email) {
            try {
                Mail::raw($body, fn ($message) => $message->to($email)->subject($subject));
            } catch (\Throwable $exception) {
                Log::warning('No se pudo enviar una notificación operativa.', [
                    'recipient' => $email,
                    'subject' => $subject,
                    'exception' => $exception::class,
                ]);
            }
        }
    }
}
