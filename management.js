function fetchHeaderAndFooter(){
    fetch('header.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
            rebuildNavElements();
        })
        .catch(error => console.error('Error loading header:', error));

    fetch('footer.html')
        .then(response => response.text())
        .then(data =>{
            document.getElementById('footer-container').innerHTML = data;
        })
        .catch(error => console.error('Error loading footer:', error));
}

function rebuildNavElements () {
    let nav = document.getElementById('navigation');
    while(nav.firstChild){
        nav.removeChild(nav.firstChild);
    }
    let addProduct = document.createElement('a');
    let orders = document.createElement('a');
    addProduct.href = "management.php";
    addProduct.text = "Artikel hinzufügen";
    orders.href = "orders.php";
    orders.text = "Bestellungen";
    nav.appendChild(addProduct);
    nav.appendChild(orders);
}

window.onload = function (){
    fetchHeaderAndFooter();
    initDragAndDrop();
}

function initDragAndDrop() {
    let dropArea = document.getElementById('drop-area');

    // Falls das Drop-Feld nicht existiert
    if (!dropArea) {
        console.error('Drop-Feld nicht gefunden.');
        return;
    }

    ['dragenter', 'dragover', 'dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, preventDefaults, false);
    });

    ['dragenter', 'dragover'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.add('hover'), false);
    });

    ['dragleave', 'drop'].forEach(eventName => {
        dropArea.addEventListener(eventName, () => dropArea.classList.remove('hover'), false);
    });

    dropArea.addEventListener('drop', handleDrop, false);
}

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function handleDrop(e) {
    let dt = e.dataTransfer;
    let files = dt.files;

    handleFiles(files);
}

function handleFiles(files) {
    files = [...files];
    files.forEach(uploadFile);
    files.forEach(previewFile);
}

function previewFile(file) {
    let reader = new FileReader();
    reader.readAsDataURL(file);
    reader.onloadend = function () {
        let img = document.createElement('img');
        img.src = reader.result;
        document.getElementById('gallery').appendChild(img);
    }
}

function uploadFile(file) {
    let url = 'management.php';
    let formData = new FormData();
    formData.append('file', file);

    fetch(url, {
        method: 'POST',
        body: formData
    })
    .then(response => response.text())
    .then(data => console.log(data))
    .catch(error => console.error('Fehler beim Hochladen:', error));
}
