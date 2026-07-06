const uploadBox = document.querySelector('.upload-box');
const fileInput = document.getElementById('fileInput');
const fileList = document.getElementById('fileList');
const filedData = [];
function addFiles(files) {
    Array.from(files).forEach(file => {
        // Check if file already exists in the list
        const existing = Array.from(fileList.querySelectorAll('.file-item span'))
            .some(span => span.textContent.startsWith(file.name));

        if (existing) {
            ClsAlert({
                icon: "info",
                title: `The file "${file.name}" is already added.`
            });
            return; // skip adding duplicate
        }

        const item = document.createElement('div');
        item.className = 'file-item';
        item.innerHTML = `
        <span>${file.name} (${Math.round(file.size/1024)} KB)</span>
        <button class="remove-btn">Remove</button>  
        `;
        item.querySelector('.remove-btn').addEventListener('click', () => {
            fileList.removeChild(item);
            const index = filedData.indexOf(file);
            if (index > -1) {
                filedData.splice(index, 1);
            }
        });
        fileList.appendChild(item);
        filedData.push(file);
    });
}

// Drag & drop highlight
uploadBox.addEventListener('dragover', (e) => {
    e.preventDefault();
    uploadBox.style.background = '#ffecec';
});
uploadBox.addEventListener('dragleave', () => {
    uploadBox.style.background = '#fff';
});
uploadBox.addEventListener('drop', (e) => {
    e.preventDefault();
    uploadBox.style.background = '#fff';
    addFiles(e.dataTransfer.files);
});

// File browse
fileInput.addEventListener('change', () => {
    addFiles(fileInput.files);
    fileInput.value = ""; // reset so same file can be re-added
});