function fetchHeaderAndFooter(){
    fetch('header.html')
        .then(response => response.text())
        .then(data => {
            document.getElementById('header-container').innerHTML = data;
        })
        .catch(error => console.error('Error loading header:', error));

    fetch('footer.html')
        .then(response => response.text())
        .then(data =>{
            document.getElementById('footer-container').innerHTML = data;
        })
        .catch(error => console.error('Error loading footer:', error));
}

window.onload = fetchHeaderAndFooter();

let slideIndex = 0;

function moveSlide(n, slider){
    const totalSlides = slider.children.length;
    slideIndex += n;

    if(slideIndex >= totalSlides){
        slideIndex = 0;
    } else if (slideIndex < 0){
        slideIndex = totalSlides - 1;
    }
    slider.style.transform = `translateX(-${slideIndex * 100}%)`;
}