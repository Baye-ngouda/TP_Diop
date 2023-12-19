<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Contrôles Techniques en France</title>
    <!-- Inclusion de la feuille de style Leaflet pour la carte -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.6.0/dist/leaflet.css" />
    <!-- Style CSS pour définir la hauteur de la carte -->
    <style>
        #mapid { height: 400px; }
    </style>
</head>
<body>
    <!-- En-tête de la page avec le titre principal -->
    <h1>Prix des Contrôles Techniques en France</h1>
    <!-- Formulaire de recherche par ville -->
    <form id="searchForm">
        <input type="text" id="cityInput" placeholder="Entrez une ville">
        <button type="submit">Chercher</button>
    </form>
    <!-- Conteneur de la carte Leaflet -->
    <div id="mapid"></div>

    <!-- Inclusion de la bibliothèque Leaflet -->
    <script src="https://unpkg.com/leaflet@1.6.0/dist/leaflet.js"></script>
    <script>
        // Déclaration de la carte Leaflet avec un point de vue initial
        var mymap = L.map('mapid').setView([46.2276, 2.2137], 6);

        // Ajout d'une couche de tuiles OpenStreetMap à la carte
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            maxZoom: 18,
            attribution: '© OpenStreetMap contributors'
        }).addTo(mymap);

        // Fonction pour charger les données des centres de contrôle technique
        function loadData(city = "") {
            // Suppression des marqueurs existants sur la carte
            mymap.eachLayer(function (layer) {
                if (!!layer.toGeoJSON) { // Supprime les marqueurs existants
                    mymap.removeLayer(layer);
                }
            });

            // Appel à l'API pour récupérer les données en fonction de la ville spécifiée
            fetch(`https://data.economie.gouv.fr/api/records/1.0/search/?dataset=controle_techn&q=${city}&rows=100`)
                .then(response => response.json())
                .then(data => {
                    // Traitement des données pour afficher les marqueurs sur la carte
                    data.records.forEach(record => {
                        var lat = record.fields.latitude;
                        var lon = record.fields.longitude;
                        // Création et ajout d'un marqueur à la carte avec une popup d'informations
                        var marker = L.marker([lat, lon]).addTo(mymap);
                        marker.bindPopup(`<b>${record.fields.cct_denomination}</b><br>${record.fields.cct_adresse}<br>Ville: ${record.fields.cct_code_commune}<br>Prix: ${record.fields.prix_visite}€`);
                    });
                })
                .catch(err => console.error(err));
        }

        // Écouteur d'événement pour le formulaire de recherche
        document.getElementById('searchForm').addEventListener('submit', function(e) {
            e.preventDefault();
            // Récupération de la ville saisie par l'utilisateur et chargement des données
            var city = document.getElementById('cityInput').value;
            loadData(city);
        });

        // Chargement initial des données au chargement de la page
        loadData();
    </script>
</body>
</html>
