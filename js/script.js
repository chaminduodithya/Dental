let menu = document.querySelector('#menu-btn');
let nav = document.querySelector('.header .nav');
let header = document.querySelector('.header');

// Mobile Menu Toggle
menu.onclick = () => {
    menu.classList.toggle('fa-times');
    nav.classList.toggle('active');
}

// Scroll Handling
window.onscroll = () => {
    menu.classList.remove('fa-times');
    nav.classList.remove('active');

    if (window.scrollY > 0) {
        header.classList.add('active');
    } else {
        header.classList.remove('active');
    }
}

// Scroll Reveal Observer
const revealObserver = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
        if (entry.isIntersecting) {
            entry.target.classList.add('active');
        }
    });
});

document.querySelectorAll('.reveal').forEach(el => {
    revealObserver.observe(el);
});

// Initial check to reveal elements already in view
window.addEventListener('load', () => {
    document.querySelectorAll('.reveal').forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight) {
            el.classList.add('active');
        }
    });
});