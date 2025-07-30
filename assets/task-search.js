const searchInput = document.getElementById('search-input');
if (searchInput) {
    searchInput.addEventListener('input', function () {
        const search = this.value;
        window.location.href = '?search=' + encodeURIComponent(search);
    });
}