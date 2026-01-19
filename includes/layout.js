// js/main.js

/* SMOOTH SCROLL FOR NAVBAR LINKS */
const $$ = s => document.querySelectorAll(s);

$$('a[href^="#"]').forEach(link => {
    link.addEventListener('click', e => {
        const target = document.querySelector(link.getAttribute('href'));
        if (target) {
            e.preventDefault();
            target.scrollIntoView({ behavior: 'smooth' });
        }
    });
});

/* ACTIVE NAVBAR LINK ON SCROLL */
const sections = $$('section');
const navLinks = $$('nav a');

window.addEventListener('scroll', () => {
    let current = '';
    sections.forEach(sec => {
        if (scrollY >= sec.offsetTop - 120) {
            current = sec.id;
        }
    });

    navLinks.forEach(a => {
        a.classList.remove('active');
        if (a.getAttribute('href') === `#${current}`) {
            a.classList.add('active');
        }
    });
});