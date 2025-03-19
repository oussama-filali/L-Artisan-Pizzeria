"use strict";

// Animation du header au défilement
window.addEventListener('scroll', function () {
  var header = document.querySelector('header');
  header.classList.toggle('scrolled', window.scrollY > 50);
}); // Animation style Pixar pour le titre

document.addEventListener('DOMContentLoaded', function () {
  var promoTitle = document.querySelector('.promo-title');
  var text = promoTitle.textContent;
  promoTitle.innerHTML = text.split('').map(function (letter) {
    if (letter === ' ') {
      return "<span>\xA0</span>"; // Espace insécable
    }

    return "<span>".concat(letter, "</span>");
  }).join(''); // Animation de la lumière sur les lettres (style Pixar)

  var spotlight = document.querySelector('.spotlight');
  var spans = document.querySelectorAll('.promo-title span');
  var currentIndex = 0;

  function moveSpotlight() {
    if (currentIndex < spans.length) {
      var span = spans[currentIndex];
      var rect = span.getBoundingClientRect();
      var promoTitleRect = promoTitle.getBoundingClientRect(); // Positionner le spotlight au centre de la lettre

      spotlight.style.left = "".concat(rect.left - promoTitleRect.left + rect.width / 2, "px");
      spotlight.style.top = "".concat(rect.top - promoTitleRect.top - 20, "px"); // Légèrement au-dessus

      spotlight.style.opacity = '1';
      spotlight.style.transform = 'scale(1.2)'; // Effet de grossissement
      // Ajouter un effet temporaire à la lettre

      span.style.color = '#fff'; // Lettre éclairée en blanc

      span.style.transition = 'color 0.3s ease';
      setTimeout(function () {
        span.style.color = '#ff6347'; // Retour à la couleur initiale

        spotlight.style.transform = 'scale(1)'; // Retour à la taille normale

        currentIndex++;
        moveSpotlight();
      }, 300); // Temps par lettre
    } else {
      spotlight.style.opacity = '0'; // Cacher le spotlight à la fin
    }
  } // Démarrer l'animation après un léger délai


  setTimeout(moveSpotlight, 500);
}); // Animation olives tombantes

document.addEventListener('DOMContentLoaded', function () {
  var oliveContainer = document.querySelector('.olive-fall-container');

  for (var i = 0; i < 10; i++) {
    var olive = document.createElement('div');
    olive.classList.add('olive');
    olive.style.left = "".concat(Math.random() * 100, "vw");
    olive.style.animationDelay = "".concat(Math.random() * 3, "s");
    oliveContainer.appendChild(olive);
  }

  setTimeout(function () {
    return oliveContainer.style.display = 'none';
  }, 5000); // Arrête après 5s
}); // Gestion des clics sur les pizzas

document.querySelectorAll('.menu-item').forEach(function (item) {
  item.addEventListener('click', function () {
    var pizza = {
      title: this.querySelector('h3').innerText,
      description: this.querySelector('p').innerText,
      price: this.querySelector('.price').innerText,
      img: this.querySelector('img').src,
      ingredients: this.querySelector('p').innerText.split(', ')
    };
    var modal = $('#pizzaModal');
    modal.find('.modal-title').text(pizza.title);
    modal.find('.modal-description').text(pizza.description);
    modal.find('.modal-price').text(pizza.price);
    modal.find('.modal-pizza-img').attr('src', pizza.img);
    var ingredientsList = modal.find('.ingredients-list');
    ingredientsList.empty();
    pizza.ingredients.forEach(function (ingredient) {
      ingredientsList.append("<span>".concat(ingredient, "</span>"));
    });
    modal.modal('show');
  });
}); // Gestion du modal contact

function openModal() {
  document.getElementById('contactModal').style.display = 'block';
}

function closeModal() {
  document.getElementById('contactModal').style.display = 'none';
} // Soumission du formulaire


document.getElementById('contactForm').addEventListener('submit', function (e) {
  e.preventDefault();
  alert('Message envoyé !');
  closeModal();
}); // Initialisation AOS

document.addEventListener('DOMContentLoaded', function () {
  AOS.init({
    duration: 1000,
    once: true,
    easing: 'ease-in-out'
  });
});