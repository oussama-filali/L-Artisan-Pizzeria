"use strict";

// === FICHIER menu.js ===
// Chargement dynamique des menus JSON
// Fonction utilitaire de chargement JSON
function chargerJSON(url) {
  var reponse;
  return regeneratorRuntime.async(function chargerJSON$(_context) {
    while (1) {
      switch (_context.prev = _context.next) {
        case 0:
          _context.next = 2;
          return regeneratorRuntime.awrap(fetch(url));

        case 2:
          reponse = _context.sent;
          _context.next = 5;
          return regeneratorRuntime.awrap(reponse.json());

        case 5:
          return _context.abrupt("return", _context.sent);

        case 6:
        case "end":
          return _context.stop();
      }
    }
  });
} // Générer les cartes de pizzas


function genererPizzas(data, container) {
  container.innerHTML = '';
  data.forEach(function (pizza) {
    var col = document.createElement('div');
    col.className = 'col-md-4 mb-4';
    col.innerHTML = "\n      <div class=\"menu-item card p-3\">\n        <img src=\"./assets/img/".concat(pizza.nom.toLowerCase().replace(/ /g, '-'), ".jpg\" class=\"card-img-top\" alt=\"").concat(pizza.nom, "\">\n        <div class=\"card-body text-center\">\n          <h3>").concat(pizza.nom, "</h3>\n          <p>").concat(pizza.description || '', "</p>\n          <p class=\"price\">").concat(pizza.prix, "</p>\n        </div>\n      </div>");
    container.appendChild(col);
  });
} // Générer les autres produits (boissons, desserts)


function genererProduits(data, container) {
  container.innerHTML = '';
  data.forEach(function (item) {
    var col = document.createElement('div');
    col.className = 'col-md-3 mb-4';
    col.innerHTML = "\n      <div class=\"menu-item card p-3\">\n        <h3>".concat(item.nom, "</h3>\n        <p>").concat(item.description || '', "</p>\n        <p class=\"price\">").concat(item.prix, "</p>\n      </div>");
    container.appendChild(col);
  });
} // Gestion des boutons de menu


document.querySelectorAll('#menu-tabs button').forEach(function (bouton) {
  bouton.addEventListener('click', function _callee() {
    var menu, container, data, _data, _data2, _data3;

    return regeneratorRuntime.async(function _callee$(_context2) {
      while (1) {
        switch (_context2.prev = _context2.next) {
          case 0:
            document.querySelectorAll('#menu-tabs button').forEach(function (btn) {
              return btn.classList.remove('active');
            });
            bouton.classList.add('active');
            menu = bouton.dataset.menu;
            container = document.getElementById('menu-list');

            if (!(menu === 'base_tomate')) {
              _context2.next = 11;
              break;
            }

            _context2.next = 7;
            return regeneratorRuntime.awrap(chargerJSON('./pizzas_tomate.json'));

          case 7:
            data = _context2.sent;
            genererPizzas(data, container);
            _context2.next = 30;
            break;

          case 11:
            if (!(menu === 'base_creme')) {
              _context2.next = 18;
              break;
            }

            _context2.next = 14;
            return regeneratorRuntime.awrap(chargerJSON('./pizzas_creme.json'));

          case 14:
            _data = _context2.sent;
            genererPizzas(_data, container);
            _context2.next = 30;
            break;

          case 18:
            if (!(menu === 'desserts')) {
              _context2.next = 25;
              break;
            }

            _context2.next = 21;
            return regeneratorRuntime.awrap(chargerJSON('./desserts.json'));

          case 21:
            _data2 = _context2.sent;
            genererProduits(_data2, container);
            _context2.next = 30;
            break;

          case 25:
            if (!(menu === 'boissons')) {
              _context2.next = 30;
              break;
            }

            _context2.next = 28;
            return regeneratorRuntime.awrap(chargerJSON('./boissons.json'));

          case 28:
            _data3 = _context2.sent;
            genererProduits(_data3, container);

          case 30:
          case "end":
            return _context2.stop();
        }
      }
    });
  });
}); // Chargement par défaut au démarrage

document.addEventListener('DOMContentLoaded', function () {
  document.querySelector('#menu-tabs button[data-menu="base_tomate"]').click();
});