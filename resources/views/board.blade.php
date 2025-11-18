<!doctype html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>Tablero Kanban</title>
    @vite(['resources/css/app.css','resources/js/app.js'])
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <style>
        :root {
            /* Paleta de colores exacta del diseño */
            --c-blue: #35a1f4;
            --c-blue-bg: #f0f8ff;
            --c-orange: #fca510;
            --c-orange-bg: #fffbf0;
            --c-purple: #5b21b6; /* Ajustado para legibilidad */
            --c-purple-main: #6d28d9;
            --c-purple-bg: #f5f3ff;
            --c-green: #22c55e;
            --c-green-bg: #f0fdf4;
            --text-dark: #111827;
            --text-gray: #6b7280;
        }

        * { margin: 0; padding: 0; box-sizing: border-box; }

        body {
            font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            background-color: #ffffff;
            color: var(--text-dark);
            height: 100vh;
            overflow: hidden; /* Evita scroll doble si hay mucho contenido */
        }

        /* Layout Principal */
        .main-layout {
            display: grid;
            grid-template-columns: 300px 1fr; /* Barra lateral fija, resto flexible */
            height: 100vh;
        }

        /* --- BARRA LATERAL (Izquierda) --- */
        .sidebar {
            padding: 40px 20px;
            border-right: 1px solid #f0f0f0;
            background: #fff;
            display: flex;
            flex-direction: column;
            gap: 30px;
            overflow-y: auto;
        }

        .brand-box {
            background: #f3f4f6;
            padding: 20px;
            border-radius: 12px;
            margin-bottom: 10px;
        }

        .brand-title {
            font-size: 28px;
            font-weight: 800;
            line-height: 1.2;
            margin-bottom: 10px;
        }

        .user-pill {
            background: #f3f4f6; /* Mismo tono que brand-box o un poco más oscuro */
            padding: 10px 20px;
            border-radius: 20px;
            font-weight: 600;
            border: none;
            width: 100%;
            font-size: 16px;
            margin-bottom: 20px;
            outline: none;
        }

        .user-pill:focus { box-shadow: 0 0 0 2px var(--c-blue); }

        /* Pasos de instrucciones */
        .step-item {
            display: flex;
            gap: 15px;
            margin-bottom: 10px;
        }

        .step-number {
            background: #9ca3af;
            color: white;
            width: 32px;
            height: 32px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            flex-shrink: 0;
        }

        .step-text {
            font-size: 14px;
            color: var(--text-gray);
            line-height: 1.4;
            padding-top: 5px;
        }

        /* Formulario integrado visualmente como "Notas adhesivas" */
        .sticky-form {
            position: relative;
            margin-left: 45px; /* Indentado para alinear con texto */
        }

        .sticky-note-input {
            background: #fff;
            border: 1px solid #e5e7eb;
            border-radius: 4px;
            padding: 15px;
            width: 100%;
            box-shadow: 3px 3px 0 rgba(0,0,0,0.05);
            transform: rotate(-1deg);
            transition: transform 0.2s;
            font-family: inherit;
            resize: none;
            margin-bottom: 10px;
        }

        .sticky-note-input:focus {
            transform: rotate(0deg) scale(1.02);
            outline: 2px solid var(--c-blue);
            border-color: transparent;
        }

        .btn-add {
            background: var(--text-dark);
            color: white;
            border: none;
            padding: 8px 16px;
            border-radius: 6px;
            cursor: pointer;
            font-weight: bold;
            width: 100%;
        }

        .btn-add:hover { opacity: 0.9; }


        /* --- TABLERO (Derecha) --- */
        .board-container {
            padding: 40px;
            background: #fff;
            overflow-x: auto;
        }

        .board-grid {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 20px;
            height: 100%;
            min-width: 1000px; /* Asegura que no se rompa en pantallas chicas */
        }

        /* Columnas */
        .column {
            display: flex;
            flex-direction: column;
            height: 100%;
        }

        .column-header {
            padding: 15px 20px;
            border-radius: 10px;
            color: white;
            font-weight: 600;
            font-size: 18px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }

        /* Estilos específicos por columna según imagen */
        .header-blue { background-color: var(--c-blue); }
        .header-orange { background-color: var(--c-orange); }
        .header-purple { background-color: var(--c-purple-main); }
        .header-green { background-color: var(--c-green); }

        .column-body {
            flex: 1;
            border-radius: 10px;
            padding: 15px;
            transition: background 0.3s;
            /* Fondo por defecto muy suave */
        }

        /* Fondos de cuerpo de columna */
        .bg-blue { background-color: var(--c-blue-bg); }
        .bg-orange { background-color: var(--c-orange-bg); }
        .bg-purple { background-color: var(--c-purple-bg); }
        .bg-green { background-color: var(--c-green-bg); }

        /* Drag interaction */
        .column-body.drag-over {
            background-color: rgba(0,0,0,0.05);
            box-shadow: inset 0 0 0 2px rgba(0,0,0,0.1);
        }

        /* Tarjetas */
        .task-card {
            background: white;
            padding: 15px;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.1);
            margin-bottom: 10px;
            cursor: grab;
            position: relative;
            transition: transform 0.2s, box-shadow 0.2s;
        }

        /* Borde izquierdo de color según columna (opcional, decorativo) */
        .bg-blue .task-card { border-left-color: var(--c-blue); }
        .bg-orange .task-card { border-left-color: var(--c-orange); }
        .bg-purple .task-card { border-left-color: var(--c-purple-main); }
        .bg-green .task-card { border-left-color: var(--c-green); }

        .task-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 6px rgba(0,0,0,0.1);
        }

        .task-card:active { cursor: grabbing; }

        .task-title { font-weight: 600; color: var(--text-dark); margin-bottom: 5px; }
        .task-user { font-size: 12px; color: var(--text-gray); }

        .btn-delete {
            position: absolute;
            top: 10px;
            right: 10px;
            background: transparent;
            border: none;
            color: #ef4444;

            opacity: 0;
            transition: opacity 0.2s;
            font-size: 14px;
        }

        .task-card:hover .btn-delete { opacity: 1; }

    </style>
</head>
<body>

<div class="main-layout">

    <aside class="sidebar">
        <input type="text" id="task-user" class="user-pill" placeholder="Nombre de usuario" value="Usuario">

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
                            <div class="task-user">{{ $task->user }}</div>
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
                            <div class="task-user">👤 {{ $task->user }}</div>
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
                            <div class="task-user">👤 {{ $task->user }}</div>
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
                            <div class="task-user">👤 {{ $task->user }}</div>
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
                document.getElementById('task-title').value = ''; // Limpiar input
                location.reload(); // Recargar para ver la tarea (o usar JS para inyectarla)
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
            e.preventDefault(); // Evitar salto de linea en textarea
            addTask();
        }
    });

    // BORRAR TAREA (Faltaba la implementación en tu código original)
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

    // DRAG AND DROP LOGIC
    function handleDragStart(e) {
        e.dataTransfer.setData('taskId', e.target.dataset.taskId);
        e.target.style.opacity = '0.4';
        e.target.style.transform = 'scale(0.95)';
    }

    function handleDragEnd(e) {
        e.target.style.opacity = '1';
        e.target.style.transform = 'none';
    }

    function handleDragOver(e) {
        e.preventDefault();
        // Solo añadir clase si estamos sobre el contenedor correcto
        if(e.currentTarget.classList.contains('column-body')) {
            e.currentTarget.classList.add('drag-over');
        }
    }

    function handleDragLeave(e) {
        e.currentTarget.classList.remove('drag-over');
    }

    async function handleDrop(e) {
        e.preventDefault();
        const columnBody = e.currentTarget;
        columnBody.classList.remove('drag-over');

        const taskId = e.dataTransfer.getData('taskId');
        const newStatus = columnBody.dataset.status;

        // Mover visualmente la tarjeta inmediatamente (Optimistic UI)
        const card = document.querySelector(`.task-card[data-task-id="${taskId}"]`);
        if(card) {
            columnBody.appendChild(card);
            // Cambiar color de borde según la nueva columna
            card.style.borderLeftColor = getComputedStyle(columnBody).backgroundColor;
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
