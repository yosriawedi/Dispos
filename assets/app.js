/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './styles/app.css';

document.addEventListener('DOMContentLoaded', () => {
    const navbar = document.getElementById('navbar');
    if (navbar) {
        window.addEventListener('scroll', () => {
            navbar.classList.toggle('scrolled', window.scrollY > 20);
        });
    }

    const hamburger = document.getElementById('hamburger');
    const navLinks = document.getElementById('navLinks');
    if (hamburger && navLinks) {
        hamburger.addEventListener('click', () => {
            navLinks.classList.toggle('open');
        });
    }

    document.querySelectorAll('.flash').forEach((el) => {
        setTimeout(() => {
            el.style.transition = 'opacity 0.5s';
            el.style.opacity = '0';
            setTimeout(() => el.remove(), 500);
        }, 4000);
    });

    const themeToggle = document.getElementById('themeToggle');
    if (themeToggle) {
        themeToggle.addEventListener('click', () => {
            const root = document.documentElement;
            const next = root.getAttribute('data-theme') === 'light' ? 'dark' : 'light';
            root.setAttribute('data-theme', next);
            document.cookie = `dispos_theme=${next}; path=/; max-age=31536000; samesite=lax`;
        });
    }

    // Scroll reveal: toutes les cartes du site apparaissent en fondu/translation
    // à l'entrée dans le viewport. Dégrade proprement si IntersectionObserver
    // est indisponible (les cartes restent visibles par défaut, la classe
    // .reveal n'étant ajoutée qu'ici).
    if ('IntersectionObserver' in window && !window.matchMedia('(prefers-reduced-motion: reduce)').matches) {
        const revealTargets = document.querySelectorAll('.card');
        const revealObserver = new IntersectionObserver((entries) => {
            entries.forEach((entry) => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('in-view');
                    revealObserver.unobserve(entry.target);
                    // Une fois révélée, on retire les classes de transition pour
                    // laisser le hover (:hover) reprendre la main sans conflit.
                    entry.target.addEventListener('transitionend', () => {
                        entry.target.classList.remove('reveal', 'in-view');
                    }, { once: true });
                }
            });
        }, { threshold: 0.1, rootMargin: '0px 0px -40px 0px' });

        revealTargets.forEach((el, i) => {
            el.classList.add('reveal');
            el.style.transitionDelay = `${Math.min(i % 6, 5) * 60}ms`;
            revealObserver.observe(el);
        });
    }
});
