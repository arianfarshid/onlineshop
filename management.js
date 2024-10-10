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
    let orders = document.createElement('a');
    orders.href = "orders.php";
    orders.text = "Bestellungen";
    nav.appendChild(orders);
}

window.onload = fetchHeaderAndFooter();