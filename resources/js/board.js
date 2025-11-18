if (window.Echo) {
    console.log("Echo conectado, escuchando canal 'tasks'...");

    window.Echo.channel("tasks")
        .listen(".task.updated", (e) => {
            console.log("Evento recibido:", e);

            if (e.action === 'created') {
                addTaskToBoard(e.task);
            } else if (e.action === 'updated') {
                moveTaskOnBoard(e.task);
            } else if (e.action === 'deleted') {
                removeTaskFromBoard(e.task.id);
            }
        });
} else {
    console.warn("Echo no se inicializó todavía.");
}

function addTaskToBoard(task) {
    const column = document.querySelector(`[data-status="${task.status}"]`);
    if (!column) return;

    // Verificar si la tarea ya existe
    if (document.querySelector(`[data-task-id="${task.id}"]`)) return;

    const taskEl = createTaskElement(task);
    column.appendChild(taskEl);
}

function moveTaskOnBoard(task) {
    const existingTask = document.querySelector(`[data-task-id="${task.id}"]`);
    if (existingTask) {
        existingTask.remove();
    }

    const column = document.querySelector(`[data-status="${task.status}"]`);
    if (!column) return;

    const taskEl = createTaskElement(task);
    column.appendChild(taskEl);
}

function removeTaskFromBoard(taskId) {
    const taskEl = document.querySelector(`[data-task-id="${taskId}"]`);
    if (taskEl) {
        taskEl.remove();
    }
}

function createTaskElement(task) {
    const div = document.createElement("div");
    div.className = "task-card";
    div.dataset.taskId = task.id;
    div.innerHTML = `
        <div class="task-title">${task.title}</div>
        <div class="task-user"><i class="bi bi-person"></i> ${task.user}</div>
        <button class="btn-delete" onclick="deleteTask(${task.id})"><i class="bi bi-x-lg"></i></button>
    `;

    // Hacer la tarea arrastrable
    div.draggable = true;
    div.addEventListener('dragstart', handleDragStart);

    return div;
}

// Funciones de drag and drop
function handleDragStart(e) {
    e.dataTransfer.setData('taskId', e.target.dataset.taskId);
    e.target.style.opacity = '0.5';
}

// Función para eliminar tarea
window.deleteTask = async function(taskId) {
    if (!confirm('¿Eliminar esta tarea?')) return;

    const res = await fetch(`/tasks/${taskId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        }
    });

    if (res.ok) {
        console.log('Tarea eliminada');
    }
}
