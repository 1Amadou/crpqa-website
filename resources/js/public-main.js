
// resources/js/public-main.js

import AOS from 'aos'; // Importer AOS
import 'aos/dist/aos.css'; // Importer le CSS d'AOS (Vite s'en chargera)
/*=============== SHOW MENU ===============*/
const navMenu = document.getElementById('nav-menu'),
      navToggle = document.getElementById('nav-toggle'),
      navClose = document.getElementById('nav-close'),
      navLinks = document.querySelectorAll('.nav__link'); // Tous les liens de navigation

/*===== MENU SHOW =====*/
if(navToggle){
    navToggle.addEventListener('click', () =>{
        navMenu.classList.add('show-menu');
        document.body.classList.add('no-scroll'); // Empêcher le scroll de l'arrière-plan
    });
}

/*===== MENU HIDDEN =====*/
if(navClose){
    navClose.addEventListener('click', () =>{
        navMenu.classList.remove('show-menu');
        document.body.classList.remove('no-scroll');
    });
}

/*=============== REMOVE MENU MOBILE WHEN A NAV LINK IS CLICKED ===============*/
function linkAction(){
    navMenu.classList.remove('show-menu');
    document.body.classList.remove('no-scroll');
}
navLinks.forEach(n => n.addEventListener('click', linkAction));

/*=============== CHANGE BACKGROUND HEADER ON SCROLL ===============*/
function scrollHeader(){
    const header = document.getElementById('header');
    if(this.scrollY >= 80) { // Seuil de défilement un peu plus élevé
        header.classList.add('scroll-header');
    } else {
        header.classList.remove('scroll-header');
    }
}
window.addEventListener('scroll', scrollHeader);

/*=============== SHOW SCROLL UP ===============*/ 
function scrollUp(){
    const scrollUpButton = document.getElementById('scroll-up');
    if(this.scrollY >= 400){ // Afficher après 400px de scroll
        scrollUpButton.classList.add('show-scroll');
    } else {
        scrollUpButton.classList.remove('show-scroll');
    }
}
window.addEventListener('scroll', scrollUp);

/*=============== ACTIVE LINK SCROLLING ===============*/
const sections = document.querySelectorAll('section[id]');

function navHighlighter() {
  let scrollY = window.pageYOffset;
  
  sections.forEach(current => {
    const sectionHeight = current.offsetHeight;
    // Ajustement du décalage pour prendre en compte la hauteur du header fixe
    const sectionTop = current.offsetTop - (document.getElementById('header').offsetHeight + 50); 
    let sectionId = current.getAttribute('id');
    
    const navLink = document.querySelector('.nav__menu a[href*=' + sectionId + ']');
    if(navLink){ // Vérifier si le lien existe
        if (scrollY > sectionTop && scrollY <= sectionTop + sectionHeight){
            navLink.classList.add('active-link');
        } else {
            navLink.classList.remove('active-link');
        }
    }
  });
}
window.addEventListener('scroll', navHighlighter);
// Appel initial pour le cas où la page est chargée sur une section
document.addEventListener('DOMContentLoaded', navHighlighter);


/*=============== UPDATE FOOTER YEAR ===============*/
const currentYearSpan = document.getElementById('currentYear');
if (currentYearSpan) {
    currentYearSpan.textContent = new Date().getFullYear();
}

/*=============== AOS INITIALIZATION (déjà dans le HTML, mais si vous préférez ici) ===============*/
// AOS.init({
//     duration: 800, // Durée des animations
//     once: true,    // Animation une seule fois
//     offset: 50,    // Décalage avant que l'animation ne commence (en px)
//     easing: 'ease-in-out-cubic', // Type d'animation
// });

// Optionnel: Re-initialiser AOS sur certains événements si du contenu est chargé dynamiquement
// document.addEventListener('lazyload', () => { AOS.refresh(); });

console.log("Bienvenue sur la maquette du site CRPQA ! Prêt à explorer le monde quantique ?");