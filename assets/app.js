// ===== Modal Utility =====
function showModal(id) {
    document.getElementById(id).classList.add('active');
}
function hideModal(id) {
    document.getElementById(id).classList.remove('active');
}
document.querySelectorAll('.modal').forEach(modal => {
    modal.addEventListener('click', function(e){
        if (e.target === modal) hideModal(modal.id);
    });
});

function ajax(url, data, callback, method = 'POST') {
    var xhr = new XMLHttpRequest();
    if (method === 'GET' && data) {
        url += (url.indexOf('?') === -1 ? '?' : '&') + data;
    }
    xhr.open(method, url, true);
    if (method === 'POST') {
        xhr.setRequestHeader('Content-Type', 'application/x-www-form-urlencoded');
        xhr.send(data);
    } else {
        xhr.send();
    }
    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            callback(xhr.responseText, xhr.status);
        }
    };
}
function serializeForm(form) {
    var fd = new FormData(form);
    var arr = [];
    fd.forEach((v, k) => arr.push(encodeURIComponent(k) + "=" + encodeURIComponent(v)));
    return arr.join("&");
}

// ===== Projects Table Updates =====
function addProjectRow(project) {
    let tbody = document.getElementById('projects-tbody');
    let tr = document.createElement('tr');
    tr.className = 'table-row-animate';
    tr.setAttribute('data-project-id', project.id);
    tr.innerHTML = `
        <td>${project.title}</td>
        <td>${project.deadline}</td>
        <td>${project.description}</td>
        <td>${project.members_html}</td>
        <td>
            <button class="btn" onclick="openEditProjectModal(${project.id})">Edit</button>
            <button class="btn btn-delete" onclick="deleteProject(${project.id})">Delete</button>
        </td>
    `;
    tbody.appendChild(tr);
}
function updateProjectRow(project) {
    let row = document.querySelector(`tr[data-project-id="${project.id}"]`);
    if (row) {
        row.children[0].innerText = project.title;
        row.children[1].innerText = project.deadline;
        row.children[2].innerText = project.description;
        row.children[3].innerHTML = project.members_html;
    }
}
function removeProjectRow(id) {
    let row = document.querySelector(`tr[data-project-id="${id}"]`);
    if (row) row.parentNode.removeChild(row);
}

// ===== Project CRUD AJAX =====
var createProjectForm = document.getElementById('project-create-form');
if (createProjectForm) {
    createProjectForm.onsubmit = function(e) {
        e.preventDefault();
        ajax('ajax_projects.php', serializeForm(this), function(res){
            let data = JSON.parse(res);
            if (data.success) {
                addProjectRow(data.project);
                hideModal('modalProject');
                createProjectForm.reset();
            }
        });
    };
}

function openEditProjectModal(id) {
    ajax('ajax_projects.php', 'action=get&id='+id, function(res){
        let data = JSON.parse(res);
        if (data.success) {
            let f = document.getElementById('project-edit-form');
            if (!f) return;
            f.project_id.value = data.project.id;
            f.title.value = data.project.title;
            f.deadline.value = data.project.deadline;
            f.description.value = data.project.description;
            // Set members (multi-select) SAFELY
            let selected = data.project.members.map(String);
            let membersSelect = f.querySelector('select[name="members[]"]');
            if (membersSelect) {
                Array.from(membersSelect.options).forEach(opt => {
                    opt.selected = selected.includes(opt.value);
                });
            }
            showModal('modalProjectEdit');
        }
    }, 'GET');
}

var editProjectForm = document.getElementById('project-edit-form');
if (editProjectForm) {
    editProjectForm.onsubmit = function(e) {
        e.preventDefault();
        ajax('ajax_projects.php', serializeForm(this), function(res){
            let data = JSON.parse(res);
            if (data.success) {
                updateProjectRow(data.project);
                hideModal('modalProjectEdit');
            }
        });
    };
}

function deleteProject(id) {
    if (!confirm('Delete project?')) return;
    ajax('ajax_projects.php', 'action=delete&project_id='+id, function(res){
        let data = JSON.parse(res);
        if (data.success) removeProjectRow(id);
    });
}

// ===== Tasks Table Updates =====
function addTaskRow(task) {
    let tbody = document.getElementById('tasks-tbody');
    let tr = document.createElement('tr');
    tr.className = 'table-row-animate';
    tr.setAttribute('data-task-id', task.id);
    tr.innerHTML = `
        <td>${task.project_title}</td>
        <td>${task.title}</td>
        <td>${task.description}</td>
        <td>${task.status}</td>
        <td>${task.priority}</td>
        <td>${task.assigned_to_name}</td>
        <td>${task.deadline}</td>
        <td>
            <button class="btn" onclick="openEditTaskModal(${task.id})">Edit</button>
            <button class="btn btn-delete" onclick="deleteTask(${task.id})">Delete</button>
        </td>
    `;
    tbody.appendChild(tr);
}
function updateTaskRow(task) {
    let row = document.querySelector(`tr[data-task-id="${task.id}"]`);
    if (row) {
        row.children[0].innerText = task.project_title;
        row.children[1].innerText = task.title;
        row.children[2].innerText = task.description;
        row.children[3].innerText = task.status;
        row.children[4].innerText = task.priority;
        row.children[5].innerText = task.assigned_to_name;
        row.children[6].innerText = task.deadline;
    }
}
function removeTaskRow(id) {
    let row = document.querySelector(`tr[data-task-id="${id}"]`);
    if (row) row.parentNode.removeChild(row);
}

// ===== Task CRUD AJAX =====
var createTaskForm = document.getElementById('task-create-form');
if (createTaskForm) {
    createTaskForm.onsubmit = function(e) {
        e.preventDefault();
        ajax('ajax_tasks.php', serializeForm(this), function(res){
            let data = JSON.parse(res);
            if (data.success) {
                addTaskRow(data.task);
                hideModal('modalTask');
                createTaskForm.reset();
            }
        });
    };
}

function openEditTaskModal(id) {
    ajax('ajax_tasks.php', 'action=get&id='+id, function(res){
        let data = JSON.parse(res);
        if (data.success) {
            let f = document.getElementById('task-edit-form');
            if (!f) return;
            f.task_id.value = data.task.id;
            f.project_id.value = data.task.project_id;
            f.title.value = data.task.title;
            f.description.value = data.task.description;
            f.status.value = data.task.status;
            f.priority.value = data.task.priority;
            f.assigned_to.value = data.task.assigned_to;
            f.deadline.value = data.task.deadline;
            showModal('modalTaskEdit');
        }
    }, 'GET');
}

var editTaskForm = document.getElementById('task-edit-form');
if (editTaskForm) {
    editTaskForm.onsubmit = function(e) {
        e.preventDefault();
        ajax('ajax_tasks.php', serializeForm(this), function(res){
            let data = JSON.parse(res);
            if (data.success) {
                updateTaskRow(data.task);
                hideModal('modalTaskEdit');
            }
        });
    };
}

function deleteTask(id) {
    if (!confirm('Delete task?')) return;
    ajax('ajax_tasks.php', 'action=delete&task_id='+id, function(res){
        let data = JSON.parse(res);
        if (data.success) removeTaskRow(id);
    });
}