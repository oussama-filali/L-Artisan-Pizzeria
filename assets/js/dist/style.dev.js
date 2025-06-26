"use strict"; // Regroupement des initialisations dans un seul DOMContentLoaded

document.addEventListener('DOMContentLoaded', function () {
  // Fix pour la compatibilité Firefox avec l'attribut playsinline
  document.querySelectorAll('.firefox-video').forEach(function (video) {
    video.playsInline = true;

    if (video.classList.contains('carousel-video')) {
      video.addEventListener('loadedmetadata', function () {
        var videoRatio = this.videoWidth / this.videoHeight;
        var containerWidth = this.parentElement.clientWidth;
        var containerHeight = this.parentElement.clientHeight;

        if (videoRatio > containerWidth / containerHeight) {
          this.style.width = '100%';
          this.style.height = 'auto';
        } else {
          this.style.width = 'auto';
          this.style.height = '100%';
        }
      });
    }
  }); // Animation du header au défilement

  var header = document.querySelector('header');
  window.addEventListener('scroll', function () {
    header.classList.toggle('scrolled', window.scrollY > 50);
  }); // Animation style Pixar pour le titre

  var promoTitle = document.querySelector('.promo-title');

  if (promoTitle) {
    var moveSpotlight = function moveSpotlight() {
      if (currentIndex < spans.length) {
        var span = spans[currentIndex];
        var rect = span.getBoundingClientRect();
        var promoTitleRect = promoTitle.getBoundingClientRect();
        spotlight.style.left = "".concat(rect.left - promoTitleRect.left + rect.width / 2, "px");
        spotlight.style.top = "".concat(rect.top - promoTitleRect.top - 20, "px");
        spotlight.style.opacity = '1';
        spotlight.style.transform = 'scale(1.2)';
        span.style.color = '#fff';
        span.style.transition = 'color 0.3s ease';
        setTimeout(function () {
          span.style.color = '#ff6347';
          spotlight.style.transform = 'scale(1)';
          currentIndex++;
          moveSpotlight();
        }, 300);
      } else {
        spotlight.style.opacity = '0';
      }
    };

    var text = promoTitle.textContent;
    promoTitle.innerHTML = text.split('').map(function (letter) {
      return letter === ' ' ? '<span> </span>' : "<span>".concat(letter, "</span>");
    }).join('');
    var spotlight = document.querySelector('.spotlight');
    var spans = document.querySelectorAll('.promo-title span');
    var currentIndex = 0;
    setTimeout(moveSpotlight, 500);
  } // Initialisation AOS


  AOS.init({
    duration: 1000,
    once: true,
    easing: 'ease-in-out'
  }); // Initialisation de i18next

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
  }, function (err, t) {
    jqueryI18next.init(i18next, $);
    $('body').localize();
  });
  $('#languageSwitcher, #languageSwitcherFooter').on('change', function () {
    var selectedLang = $(this).val();
    i18next.changeLanguage(selectedLang, function (err, t) {
      $('body').localize();
    });
  }); // --- Gestion ergonomique du carrousel vidéo sur mobile ---

  document.addEventListener('DOMContentLoaded', function () {
    var carousel = document.getElementById('videoCarousel');

    if (carousel) {
      // Bootstrap 4: événement 'slide.bs.carousel'
      $('#videoCarousel').on('slide.bs.carousel', function (e) {
        // Pause toutes les vidéos du carrousel
        document.querySelectorAll('.carousel-video').forEach(function (video) {
          video.pause();
          video.currentTime = 0;
        }); // Joue la vidéo de la prochaine slide

        var nextIndex = e.to;
        var items = carousel.querySelectorAll('.carousel-item');

        if (items[nextIndex]) {
          var video = items[nextIndex].querySelector('video');

          if (video) {
            // Nécessaire sur iOS pour permettre la lecture
            video.muted = true;
            video.play()["catch"](function () {});
          }
        }
      }); // Démarre la première vidéo au chargement

      var firstVideo = carousel.querySelector('.carousel-item.active video');

      if (firstVideo) {
        firstVideo.muted = true;
        firstVideo.play()["catch"](function () {});
      }
    }
  });
});