document.addEventListener('DOMContentLoaded', function () {


    // =====================================================
    // ACCUEIL — Pause du carrousel héro
    // =====================================================
    const banner = document.getElementById('heroBanner');
    const btn = document.getElementById('heroPauseBtn');

    if (banner && btn) {
        btn.addEventListener('click', () => {
            const paused = banner.classList.toggle('paused');
            btn.setAttribute('aria-pressed', paused);
            btn.setAttribute('aria-label', paused ? 'Reprendre le diaporama' : 'Mettre en pause le diaporama');
            btn.innerHTML = `<i class="bi ${paused ? 'bi-play-fill' : 'bi-pause-fill'}" aria-hidden="true"></i>`;
        });
    }

// =====================================================
// ADMIN PRODUIT — Ajout / suppression / aperçu d'images
// =====================================================

  // La liste qui contient toutes les images
    const liste = document.getElementById("product-images-list");

    // Le bouton "Ajouter une image"
    const boutonAjouter = document.getElementById("add-image-btn");


    // =====================================================
    // AJOUTER UNE IMAGE
    // =====================================================

    boutonAjouter.addEventListener("click", function () {

        // Récupérer l'index actuel (0,1,2,3...)
        let index = liste.dataset.index;

        // Récupérer le prototype créé par Symfony
        let prototype = liste.dataset.prototype;
    

        // Remplacer "__name__" par le numéro
        let formulaire = prototype.replace(/__name__/g, index);
        //<input type="file" name="product[productImages][__name__][imageFile]">
      

        // Créer un nouveau bloc
        let div = document.createElement("div");

        div.className = "product-image-item border rounded p-2 mb-2";

        // Ajouter le formulaire dans le bloc
        div.innerHTML = formulaire;


        // Créer le bouton Supprimer
        let boutonSupprimer = document.createElement("button");

        boutonSupprimer.type = "button";
        boutonSupprimer.className = "btn btn-danger btn-sm remove-image-btn";
        boutonSupprimer.textContent = "Supprimer";

        // Ajouter le bouton dans le bloc
        div.appendChild(boutonSupprimer);

        // Ajouter le bloc dans la liste
        liste.appendChild(div);

        // Augmenter l'index
        liste.dataset.index = Number(index) + 1;
    });



    // =====================================================
    // SUPPRIMER UNE IMAGE
    // =====================================================

    liste.addEventListener("click", function (event) {

        // Vérifier si on a cliqué sur le bouton Supprimer
        if (event.target.classList.contains("remove-image-btn")) {

            // Trouver le bloc parent
            let bloc = event.target.closest(".product-image-item");

            // Supprimer le bloc
            bloc.remove();
        }

    });



    // =====================================================
    // AFFICHER L'APERÇU DE L'IMAGE
    // =====================================================

    liste.addEventListener("change", function (event) {

        // Vérifier que c'est bien un champ fichier
        if (event.target.type !== "file") {
            return;
        }

        let input = event.target;

        // Le fichier sélectionné
        let fichier = input.files[0];

        if (!fichier) {
            return;
        }

        // Le bloc contenant le champ
        let bloc = input.closest(".product-image-item");

        // Chercher une image d'aperçu
        let image = bloc.querySelector(".image-preview");

        // Si elle n'existe pas, on la crée
        if (image === null) {

            image = document.createElement("img");

            image.className = "img-fluid d-block mx-auto my-2 image-preview";

            // Ajouter l'image juste avant le champ file
            input.before(image);
        }

        // Lire le fichier
        let lecteur = new FileReader();

        lecteur.onload = function (event) {

            // Afficher l'image
            image.src = event.target.result;

        };

        lecteur.readAsDataURL(fichier);

    });


    
});