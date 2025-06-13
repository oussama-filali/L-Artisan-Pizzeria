"use strict";

// Regroupement des initialisations dans un seul DOMContentLoaded
document.addEventListener('DOMContentLoaded', () => {
    // Fix pour la compatibilité Firefox avec l'attribut playsinline
    document.querySelectorAll('.firefox-video').forEach(video => {
        // Définir la propriété playsInline programmatiquement pour Firefox
        video.playsInline = true;
        
        // Ajuster les vidéos du carrousel pour qu'elles s'adaptent à leur conteneur
        if (video.classList.contains('carousel-video')) {
            video.addEventListener('loadedmetadata', function() {
                // Ajuster la taille de la vidéo en fonction de son ratio
                const videoRatio = this.videoWidth / this.videoHeight;
                const containerWidth = this.parentElement.clientWidth;
                const containerHeight = this.parentElement.clientHeight;
                
                if (videoRatio > containerWidth / containerHeight) {
                    // Vidéo plus large que haute par rapport au conteneur
                    this.style.width = '100%';
                    this.style.height = 'auto';
                } else {
                    // Vidéo plus haute que large par rapport au conteneur
                    this.style.width = 'auto';
                    this.style.height = '100%';
                }
            });
        }
    });

    // Animation du header au défilement
    const header = document.querySelector('header');
    window.addEventListener('scroll', () => {
        header.classList.toggle('scrolled', window.scrollY > 50);
    });

    // Animation style Pixar pour le titre
    const promoTitle = document.querySelector('.promo-title');
    const text = promoTitle.textContent;
    promoTitle.innerHTML = text.split('').map(letter => {
        return letter === ' ' ? '<span> </span>' : `<span>${letter}</span>`;
    }).join('');
    const spotlight = document.querySelector('.spotlight');
    const spans = document.querySelectorAll('.promo-title span');
    let currentIndex = 0;

    function moveSpotlight() {
        if (currentIndex < spans.length) {
            const span = spans[currentIndex];
            const rect = span.getBoundingClientRect();
            const promoTitleRect = promoTitle.getBoundingClientRect();
            spotlight.style.left = `${rect.left - promoTitleRect.left + rect.width / 2}px`;
            spotlight.style.top = `${rect.top - promoTitleRect.top - 20}px`;
            spotlight.style.opacity = '1';
            spotlight.style.transform = 'scale(1.2)';
            span.style.color = '#fff';
            span.style.transition = 'color 0.3s ease';
            setTimeout(() => {
                span.style.color = '#ff6347';
                spotlight.style.transform = 'scale(1)';
                currentIndex++;
                moveSpotlight();
            }, 300);
        } else {
            spotlight.style.opacity = '0';
        }
    }
    setTimeout(moveSpotlight, 500);

    // Animation olives supprimée pour optimisation

    // Initialisation AOS
    AOS.init({
        duration: 1000,
        once: true,
        easing: 'ease-in-out'
    });

    // Initialisation de i18next
    i18next.init({
        lng: 'fr',
        resources: {
            en: {
                translation: {
                    "Accueil": "Home",
                    "Menu": "Menu",
                    "Contact": "Contact",
                    "La meilleure pizza de la ville": "The best pizza in town",
                    "Où nous trouver ?": "Where to find us?",
                    "Contactez-nous": "Contact us",
                    "Envoyer un SMS": "Send an SMS",
                    "Suivez-nous sur Instagram": "Follow us on Instagram",
                    "Tous droits réservés.": "All rights reserved."
                }
            },
            fr: {
                translation: {
                    "Accueil": "Accueil",
                    "Menu": "Menu",
                    "Contact": "Contact",
                    "La meilleure pizza de la ville": "La meilleure pizza de la ville",
                    "Où nous trouver ?": "Où nous trouver ?",
                    "Contactez-nous": "Contactez-nous",
                    "Envoyer un SMS": "Envoyer un SMS",
                    "Suivez-nous sur Instagram": "Suivez-nous sur Instagram",
                    "Tous droits réservés.": "Tous droits réservés."
                }
            }
        }
    }, (err, t) => {
        jqueryI18next.init(i18next, $);
        $('body').localize();
    });

    $('#languageSwitcher, #languageSwitcherFooter').on('change', function() {
        const selectedLang = $(this).val();
        i18next.changeLanguage(selectedLang, (err, t) => {
            $('body').localize();
        });
    });

    // Données du menu avec images
    const menuData = {
        "base_tomate": [
            {"nom": "La Fromage", "description": "Emmental", "prix": "8€", "image": "https://via.placeholder.com/150?text=La+Fromage"},
            {"nom": "La Mozzarella", "description": "Mozza fraîche, mozza râpée", "prix": "9€", "image": "https://via.placeholder.com/150?text=La+Mozzarella"},
            {"nom": "La Jambon fromage", "description": "Jambon, emmental", "prix": "10€", "image": "https://via.placeholder.com/150?text=La+Jambon+Fromage"},
            {"nom": "La Royale", "description": "Champignons frais, jambon, emmental", "prix": "11€", "image": "https://via.placeholder.com/150?text=La+Royale"},
            {"nom": "La Merguez", "description": "Merguez, poivrons, emmental", "prix": "11€", "image": "https://via.placeholder.com/150?text=La+Merguez"},
            {"nom": "L’Aldente", "description": "Poulet rôti, merguez, boeuf haché, sauce BBQ, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=L’Aldente"},
            {"nom": "La Chorizo", "description": "Chorizo, emmental", "prix": "10€", "image": "https://via.placeholder.com/150?text=La+Chorizo"},
            {"nom": "La Figateli", "description": "Figateli, brousse, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Figateli"},
            {"nom": "La Végétarienne", "description": "Aubergines, poivrons grillés, mozza, oignons caramélisés, tomates cerises, roquette, olives", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Végétarienne"},
            {"nom": "La Dolce Vita", "description": "Mozzarella, jambon cru, tomates cerises, parmesan, roquette, olives", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Dolce+Vita"},
            {"nom": "La Pesto", "description": "Mozzarella, roquette, jambon cru, copeaux de parmesan, pesto, tomates cerises, olives", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Pesto"},
            {"nom": "La Parmigiana", "description": "Mozzarella fraîche, aubergines grillées, tomates cerises, roquette, parmesan, jambon cru, olives, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Parmigiana"},
            {"nom": "L’Arménienne", "description": "Boeuf haché cuisine, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=L’Arménienne"},
            {"nom": "La Boulette hachées", "description": "Mozzarella, oignons, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Boulette+hachées"},
            {"nom": "Anchois", "description": "Anchois", "prix": "10€", "image": "https://via.placeholder.com/150?text=Anchois"},
            {"nom": "Champignons", "description": "Champignons, emmental", "prix": "10€", "image": "https://via.placeholder.com/150?text=Champignons"},
            {"nom": "Roquefort", "description": "Roquefort, emmental", "prix": "10€", "image": "https://via.placeholder.com/150?text=Roquefort"},
            {"nom": "Fruits de mer", "description": "Fruits de mer", "prix": "12€", "image": "https://via.placeholder.com/150?text=Fruits+de+mer"},
            {"nom": "L’Hawaienne", "description": "Jambon, poulet, ananas, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=L’Hawaienne"},
            {"nom": "La provençale", "description": "Tomates fraîches, anchois, ail, persil, mozzarella fraîche", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+provençale"}
        ],
        "base_creme": [
            {"nom": "La 5 Fromages", "description": "Roquefort, brie, chèvre, brousse, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+5+Fromages"},
            {"nom": "La Chèvre Miel", "description": "Chèvre, emmental, filet de miel BIO", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Chèvre+Miel"},
            {"nom": "La Forestière", "description": "Poulet rôti, champignons, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Forestière"},
            {"nom": "La Camembert", "description": "Camembert, lardons, mozzarella", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Camembert"},
            {"nom": "La Thon", "description": "Thon à l’huile, poivrons grillés, oeuf, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Thon"},
            {"nom": "La Kebab", "description": "Viande et sauce kebab, oignons caramélisés, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Kebab"},
            {"nom": "La Boursin", "description": "Saumon avec filet de citron OU boeuf haché OU poulet rôti, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Boursin"},
            {"nom": "La Carbonara", "description": "Lardons, champignons, oeuf, oignons caramélisés, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Carbonara"},
            {"nom": "La Raclette", "description": "Crème légère, bacon, raclette, boeuf haché, oeuf, PDT, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Raclette"},
            {"nom": "La Tartiflette", "description": "Lardons, oignons, PDT, reblochon, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Tartiflette"},
            {"nom": "La Curry", "description": "Crème au curry, poulet rôti, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Curry"},
            {"nom": "La Burger", "description": "Crème légère burger, tomates cerises, boeuf haché, oignons rouges, cornichons, cheddar, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Burger"},
            {"nom": "La Mexicaine", "description": "Poulet, poivrons, épices Tex Mex", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+Mexicaine"},
            {"nom": "La fermière", "description": "Lardons, chèvre, emmental", "prix": "12€", "image": "https://via.placeholder.com/150?text=La+fermière"}
        ],
        "desserts": [
            {"nom": "Pizza Nutella", "description": "Nutella, avalanche de produits Kinder", "prix": "10€", "image": "https://via.placeholder.com/150?text=Pizza+Nutella"},
            {"nom": "Fondant au chocolat", "description": "", "prix": "3.50€", "image": "https://via.placeholder.com/150?text=Fondant+au+chocolat"},
            {"nom": "Tiramisu", "description": "", "prix": "3.50€", "image": "https://via.placeholder.com/150?text=Tiramisu"},
            {"nom": "Glace mini pot Häagen-Dazs", "description": "", "prix": "4€", "image": "https://via.placeholder.com/150?text=Glace+Häagen-Dazs"}
        ],
        "boissons": [
            {"nom": "Coca, Orangina, Oasis, Ice Tea", "description": "", "prix": "3€", "image": "https://via.placeholder.com/150?text=Coca+Orangina+Oasis+Ice+Tea"},
            {"nom": "Desperados", "description": "", "prix": "3€", "image": "https://via.placeholder.com/150?text=Desperados"},
            {"nom": "Bière 25cl", "description": "", "prix": "2.50€", "image": "https://via.placeholder.com/150?text=Bière+25cl"},
            {"nom": "Vin rouge ou rosé", "description": "", "prix": "6€", "image": "https://via.placeholder.com/150?text=Vin+rouge+ou+rosé"},
            {"nom": "San Pellegrino 1L", "description": "", "prix": "2.50€", "image": "https://via.placeholder.com/150?text=San+Pellegrino"},
            {"nom": "Canettes", "description": "", "prix": "1.50€", "image": "https://via.placeholder.com/150?text=Canettes"}
        ],
        "supplements": {
            "ingredient_supplementaire": "1€",
            "piment_sur_demande": ""
        },
        "modes_paiement": ["Espèces", "Carte bancaire", "Ticket restaurant"]
    };

    // Fonction utilitaire pour charger un fichier JSON
    async function loadJSON(path) {
        const response = await fetch(path);
        return await response.json();
    }

    // Affichage des éléments du menu sous forme de cards identiques
    function renderMenuItems(items) {
        const menuList = document.getElementById('menu-list');
        menuList.innerHTML = '';
        items.forEach((item) => {
            const card = document.createElement('div');
            card.className = 'col-12 col-sm-6 col-md-4 col-lg-3 mb-4 d-flex align-items-stretch';
            card.innerHTML = `
                <div class="card menu-card bg-dark text-white shadow-sm w-100 h-100">
                    <img src="${item.image || './assets/img/default-pizza.jpg'}" class="card-img-top" alt="${item.nom}">
                    <div class="card-body d-flex flex-column justify-content-between">
                        <h5 class="card-title text-center">${item.nom}</h5>
                        <p class="card-text text-center">${item.description || ''}</p>
                        <div class="price text-center mt-auto mb-2 font-weight-bold" style="color:#d4a017">${item.prix}</div>
                    </div>
                </div>
            `;
            menuList.appendChild(card);
        });
    }

    // Affichage des suppléments et modes de paiement
    function renderSupplementsAndPaiement() {
        const supplements = menuData.supplements;
        const paiement = menuData.modes_paiement;
        document.getElementById('supplements').innerHTML =
            `<strong>Suppléments :</strong> ingrédient supplémentaire (+${supplements.ingredient_supplementaire})` +
            (supplements.piment_sur_demande ? '<br>Piment sur demande' : '');
        document.getElementById('paiement').innerHTML =
            `<strong>Moyens de paiement :</strong> ${paiement.join(', ')}`;
    }

    // Gestion des onglets du menu
    renderMenuItems(menuData.base_tomate);
    renderSupplementsAndPaiement();

    document.querySelectorAll('#menu-tabs button').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('#menu-tabs button').forEach(b => b.classList.remove('active'));
            this.classList.add('active');
            const category = this.getAttribute('data-menu');
            renderMenuItems(menuData[category]);
        });
    });
});
col.innerHTML = `
  <div class="menu-item card p-3">
    <img src="./assets/img/${pizza.nom.toLowerCase().replace(/ /g, '-').replace(/[’']/g, '').normalize('NFD').replace(/[\u0300-\u036f]/g, '')}.jpg" class="card-img-top" alt="${pizza.nom}">
    <div class="card-body text-center">
      <h3>${pizza.nom}</h3>
      <p>${pizza.description || ''}</p>
      <p class="price">${pizza.prix}</p>
    </div>
  </div>
`;
