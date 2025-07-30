// Set these globals on page load
window.currentUserId = ...; // Your PHP user's ID
window.currentProjectId = ...; // Current project ID

const ws = new WebSocket('ws://localhost:8081');
ws.onopen = function() {
    ws.send(JSON.stringify({type: 'join', userId: window.currentUserId, projectId: window.currentProjectId}));
};
ws.onmessage = function(event) {
    let data = JSON.parse(event.data);
    if (data.type === 'onlineUsers') {
        document.getElementById('online-users').innerText =
            'Online: ' + data.users.join(', ');
    }
    if (data.type === 'taskStatus') {
        showToast(`${data.userName} changed status of "${data.taskTitle}" to ${data.status}`);
        // Optionally update status in UI
    }
    if (data.type === 'taskComment') {
        addComment(data.userName, data.comment);
        showToast(`${data.userName} commented: "${data.comment}"`);
    }
    if (data.type === 'taskAssigned') {
        showToast(`New task assigned: ${data.taskTitle} by ${data.fromUserName}`);
    }
};

function addComment(user, comment) {
    let div = document.createElement('div');
    div.innerText = `${user}: ${comment}`;
    document.getElementById('task-comments').appendChild(div);
}

function showToast(msg) {
    let toast = document.createElement('div');
    toast.className = 'toast';
    toast.innerText = msg;
    document.getElementById('toast-container').appendChild(toast);
    setTimeout(()=>toast.remove(), 3000);
}

// Example: send comment to PHP API
document.getElementById('comment-form').onsubmit = function(e) {
    e.preventDefault();
    let fd = new FormData(this);
    fetch('ajax_task_comment.php', { method: 'POST', body: fd })
      .then(res => res.json())
      .then(data => {
        if (data.success) {
            this.comment.value = '';
        }
      });
};