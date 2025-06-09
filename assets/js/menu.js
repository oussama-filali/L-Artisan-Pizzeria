// === FICHIER menu.js ===
// Chargement dynamique des menus JSON

// Fonction utilitaire de chargement JSON
async function chargerJSON(url) {
  const reponse = await fetch(url);
  return await reponse.json();
}

// Générer les cartes de pizzas
function genererPizzas(data, container) {
  container.innerHTML = '';
  data.forEach(pizza => {
    const col = document.createElement('div');
    col.className = 'col-md-4 mb-4';
    col.innerHTML = `
      <div class="menu-item card p-3">
        <img src="./assets/img/${pizza.nom.toLowerCase().replace(/ /g, '-')}.jpg" class="card-img-top" alt="${pizza.nom}">
        <div class="card-body text-center">
          <h3>${pizza.nom}</h3>
          <p>${pizza.description || ''}</p>
          <p class="price">${pizza.prix}</p>
        </div>
      </div>`;
    container.appendChild(col);
  });
}

// Générer les autres produits (boissons, desserts)
function genererProduits(data, container) {
  container.innerHTML = '';
  data.forEach(item => {
    const col = document.createElement('div');
    col.className = 'col-md-3 mb-4';
    col.innerHTML = `
      <div class="menu-item card p-3">
        <h3>${item.nom}</h3>
        <p>${item.description || ''}</p>
        <p class="price">${item.prix}</p>
      </div>`;
    container.appendChild(col);
  });
}

// Gestion des boutons de menu
document.querySelectorAll('#menu-tabs button').forEach(bouton => {
  bouton.addEventListener('click', async () => {
    document.querySelectorAll('#menu-tabs button').forEach(btn => btn.classList.remove('active'));
    bouton.classList.add('active');
    const menu = bouton.dataset.menu;
    const container = document.getElementById('menu-list');

    if (menu === 'base_tomate') {
      const data = await chargerJSON('./pizzas_tomate.json');
      genererPizzas(data, container);
    } else if (menu === 'base_creme') {
      const data = await chargerJSON('./pizzas_creme.json');
      genererPizzas(data, container);
    } else if (menu === 'desserts') {
      const data = await chargerJSON('./desserts.json');
      genererProduits(data, container);
    } else if (menu === 'boissons') {
      const data = await chargerJSON('./boissons.json');
      genererProduits(data, container);
    }
  });
});

// Chargement par défaut au démarrage
document.addEventListener('DOMContentLoaded', () => {
  document.querySelector('#menu-tabs button[data-menu="base_tomate"]').click();
});
