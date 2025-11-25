<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tablero Kanban</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">
</head>
<body>

<div class="main-layout">

    <aside class="sidebar">
        <input type="text" id="task-user" class="user-pill" placeholder="Nombre de usuario">

        <div class="step-item">
            <div class="step-number">1</div>
            <div class="step-text">Copia una nota adhesiva y luego escribe la tarea que debe hacerse.</div>
        </div>

        <div class="sticky-form">
            <textarea id="task-title" class="sticky-note-input" rows="3" placeholder="Escribe tu tarea aquí..."></textarea>
            <button class="btn-add" onclick="addTask()">+ Crear Tarea</button>
        </div>

        <div class="step-item" style="margin-top: 20px;">
            <div class="step-number">2</div>
            <div class="step-text">Arrástrala a la columna correspondiente.</div>
        </div>

        <div class="step-item">
            <div class="step-number">3</div>
            <div class="step-text">Pon la tarea en la columna siguiente si cambia de estatus.</div>
        </div>
    </aside>

    <main class="board-container">
        <div class="board-grid">

            <div class="column">
                <div class="column-header header-blue">
                    <span>1 | Por hacer</span>
                    <span>&rarr;</span>
                </div>
                <div class="column-body bg-blue" data-status="por_hacer" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                    @foreach($tasksByStatus['por_hacer'] as $task)
                        <div class="task-card" data-task-id="{{ $task->id }}" draggable="true" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                            <div class="task-title">{{ $task->title }}</div>
                            <div class="task-user"><i class="bi bi-person"></i>{{ $task->user }}</div>
                            <button class="btn-delete" onclick="deleteTask({{ $task->id }})"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="column">
                <div class="column-header header-orange">
                    <span>2 | En proceso</span>
                    <span>&rarr;</span>
                </div>
                <div class="column-body bg-orange" data-status="en_proceso" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                    @foreach($tasksByStatus['en_proceso'] as $task)
                        <div class="task-card" data-task-id="{{ $task->id }}" draggable="true" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                            <div class="task-title">{{ $task->title }}</div>
                            <div class="task-user"><i class="bi bi-person"></i>{{ $task->user }}</div>
                            <button class="btn-delete" onclick="deleteTask({{ $task->id }})"></button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="column">
                <div class="column-header header-purple">
                    <span>3 | Hecho</span>
                    <span>&rarr;</span>
                </div>
                <div class="column-body bg-purple" data-status="hecho" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                    @foreach($tasksByStatus['hecho'] as $task)
                        <div class="task-card" data-task-id="{{ $task->id }}" draggable="true" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                            <div class="task-title">{{ $task->title }}</div>
                            <div class="task-user"><i class="bi bi-person"></i>{{ $task->user }}</div>
                            <button class="btn-delete" onclick="deleteTask({{ $task->id }})"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforeach
                </div>
            </div>

            <div class="column">
                <div class="column-header header-green">
                    <span>4 | Aprobado</span>
                    <span>&#10003;</span>
                </div>
                <div class="column-body bg-green" data-status="aprobado" ondrop="handleDrop(event)" ondragover="handleDragOver(event)" ondragleave="handleDragLeave(event)">
                    @foreach($tasksByStatus['aprobado'] as $task)
                        <div class="task-card" data-task-id="{{ $task->id }}" draggable="true" ondragstart="handleDragStart(event)" ondragend="handleDragEnd(event)">
                            <div class="task-title">{{ $task->title }}</div>
                            <div class="task-user"><i class="bi bi-person"></i>{{ $task->user }}</div>
                            <button class="btn-delete" onclick="deleteTask({{ $task->id }})"><i class="bi bi-x-lg"></i></button>
                        </div>
                    @endforeach
                </div>
            </div>

        </div>
    </main>
</div>

<script>
    // AGREGAR TAREA
    async function addTask() {
        const title = document.getElementById('task-title').value.trim();
        const user = document.getElementById('task-user').value.trim() || 'Anónimo';

        if (!title) {
            alert('Por favor escribe una tarea');
            return;
        }

        try {
            const res = await fetch('{{ route('tasks.store') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify({ title, user })
            });

            if (res.ok) {
                document.getElementById('task-title').value = '';
                location.reload();
            } else {
                alert('Error al guardar');
            }
        } catch (error) {
            console.error(error);
        }
    }

    // ENTER PARA ENVIAR
    document.getElementById('task-title').addEventListener('keypress', (e) => {
        if (e.key === 'Enter' && !e.shiftKey) {
            e.preventDefault();
            addTask();
        }
    });

    // BORRAR TAREA
    async function deleteTask(id) {
        if(!confirm('¿Eliminar tarea?')) return;

        try {
            const res = await fetch(`/tasks/${id}`, {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                }
            });

            if (res.ok) {
                // Remover elemento del DOM visualmente
                const card = document.querySelector(`.task-card[data-task-id="${id}"]`);
                if(card) card.remove();
            }
        } catch (error) {
            console.error(error);
        }
    }

    // LÓGICA DE ARRASTRAR Y SOLTAR
    function handleDragStart(e) {
        e.dataTransfer.setData('taskId', e.target.dataset.taskId);
        e.target.style.opacity = '0.4';
        e.target.style.transform = 'scale(0.95)';
    }

    function handleDragOver(e) {
        e.preventDefault();
        if(e.currentTarget.classList.contains('column-body')) {
            e.currentTarget.classList.add('drag-over');
        }
    }
    
    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }


    function handleDragEnd(e) {
        e.target.style.opacity = '1';
        e.target.style.transform = 'none';
    }

    

    async function handleDrop(e) {
    e.preventDefault();
    const columnBody = e.currentTarget;
    columnBody.classList.remove('drag-over');

    const taskId = e.dataTransfer.getData('taskId');
    const newStatus = columnBody.dataset.status;

    // Mover visualmente la tarjeta inmediatamente
    const card = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
    if(card) {
        columnBody.appendChild(card);
    }

    // Guardar en Backend
    try {
        const res = await fetch(`/tasks/${taskId}`, {
            method: 'PUT',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({ status: newStatus })
        });

        if (!res.ok) console.error('Error actualizando estado');
    } catch (error) {
        console.error(error);
    }
           
}
</script>
</body>
</html>
