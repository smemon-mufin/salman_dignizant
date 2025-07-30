const emoji = new EmojiConvertor();
emoji.replace_mode = 'img';

document.getElementById('comment-text').oninput = function() {
    let raw = this.value;
    let html = marked.parse(raw);
    html = emoji.replace_colons(html); // Converts :smile: to emoji images
    document.getElementById('preview').innerHTML = html;
};