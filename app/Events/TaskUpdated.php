<?php

namespace App\Events;

use App\Models\Task;
use Illuminate\Broadcasting\Channel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Queue\SerializesModels;

class TaskUpdated implements ShouldBroadcastNow
{
    use SerializesModels;

    public function __construct(public Task $task, public string $action) {}

    public function broadcastOn(): Channel
    {
        return new Channel('tasks');
    }

    public function broadcastAs(): string
    {
        return 'task.updated';
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
            ]
        ];
    }
}
