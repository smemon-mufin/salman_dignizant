const sockets = {};

function openSocket(taskId) {
    if (!sockets[taskId]) {
        const ws = new WebSocket('ws://localhost:8081');
        ws.onmessage = function(e) {
            const chatBox = document.getElementById('chatBox-' + taskId);
            const msg = JSON.parse(e.data);
            if (msg.task_id == taskId) {
                const div = document.createElement('div');
                div.className = 'chat-message';
                div.innerText = msg.username + ": " + msg.comment;
                chatBox.appendChild(div);
                chatBox.scrollTop = chatBox.scrollHeight;
            }
        };
        sockets[taskId] = ws;
    }
}

function sendComment(taskId, user) {
    openSocket(taskId);
    var input = document.getElementById('commentInput-' + taskId);
    var comment = input.value;
    if (!comment) return;
    sockets[taskId].send(JSON.stringify({
        task_id: taskId,
        comment: comment,
        username: user
    }));
    input.value = "";
}