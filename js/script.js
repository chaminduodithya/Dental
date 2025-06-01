let menu = document.querySelector('#menu-btn');
let nav = document.querySelector(' .header .nav');
let header = document.querySelector('.header');

menu.onclick = () => {
    menu.classList.toggle('fa-times');
    nav.classList.toggle('active');
}

window.onscroll = () => {
    menu.classList.remove('fa-times');
    nav.classList.remove('active');

    if(window.scrollY > 0) {
        header.classList.add('active');
    }
    else {
        header.classList.remove('active');
    }
}