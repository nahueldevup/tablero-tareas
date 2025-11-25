<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcastNow // En cuanto ocurra este evento, transmítelo inmediatamente, no lo pongas en una cola de espera larga
{
    use SerializesModels;

    public function __construct(public Task $task, public string $action) {}// Recibe la tarea que cambió y qué pasó con ella ('created', 'updated', 'deleted')

    public function broadcastOn(): Channel
    {
        return new Channel('tasks'); // Define el "canal de radio" o "sala de chat" donde se emitirá el mensaje.
    }

    public function broadcastAs(): string
    {
        return 'task.updated'; // Define el nombre del evento. Por defecto, Laravel usa el nombre completo de la clase (App\Events\TaskUpdated), pero aquí lo estás personalizando para que sea más corto y limpio.
    }

    public function broadcastWith(): array
    {
        return [
            'action' => $this->action, // 'created', 'updated', 'deleted'
            'task' => [
                'id' => $this->task->id,
                'title' => $this->task->title,
                'user' => $this->task->user,
                'status' => $this->task->status,
            ],
        ];
    }
}
