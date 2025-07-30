document.querySelectorAll('.upload-form').forEach(form => {
    form.addEventListener('submit', function(e) {
        e.preventDefault();
        let fd = new FormData(form);
        let xhr = new XMLHttpRequest();
        let progress = form.querySelector('.progress');
        xhr.open('POST', 'upload.php', true);
        xhr.upload.onprogress = function(e) {
            if (e.lengthComputable) {
                let percent = Math.round((e.loaded / e.total) * 100);
                progress.textContent = "Uploading: " + percent + "%";
            }
        };
        xhr.onload = function() {
            progress.textContent = xhr.status === 200 ? "Complete!" : "Failed!";
            if (xhr.status === 200) setTimeout(()=>{progress.textContent='';location.reload();}, 800);
        };
        xhr.send(fd);
    });
});