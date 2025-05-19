const uploadArea = document.getElementById('uploadArea');
const fileInput = document.getElementById('fileInput');
const imagePreview = document.getElementById('image-preview');
const fileInfo = document.getElementById('fileInfo');

fileInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        if (file.type.match('image.*')) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                imagePreview.src = event.target.result;
                imagePreview.style.display = 'block';
                uploadArea.classList.add('has-image');
                const icon = uploadArea.querySelector('.upload-icon');
                const text = uploadArea.querySelector('.upload-text');
                if (icon) icon.style.display = 'none';
                if (text) text.style.display = 'none';
            }
            
            reader.readAsDataURL(file);
            fileInfo.textContent = file.name;
        } else {
            alert('Please select an image file.');
            fileInput.value = '';
        }
    }
});

uploadArea.addEventListener('click', function() {
    fileInput.click();
});

document.addEventListener('DOMContentLoaded', function() {
    const fileInput = document.getElementById('fileInput');
    const imagePreview = document.getElementById('image-preview');
    const fileInfo = document.getElementById('fileInfo');
    
    fileInput.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (file) {
            const reader = new FileReader();
            
            reader.onload = function(event) {
                imagePreview.src = event.target.result;
                imagePreview.style.display = 'block';
                fileInfo.textContent = file.name;
            }
            
            reader.readAsDataURL(file);
        }
    });
});