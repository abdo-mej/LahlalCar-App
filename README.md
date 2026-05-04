# LahlalCar Manager FR PRO

Application complète en français pour la gestion d'une agence de location de voitures.
## 🌐 Live Demo
https://lahlalcar.infinityfree.me

## Technologies
- PHP 8+
- MySQL / SQL
- HTML
- CSS
- JavaScript

## Installation
1. Démarrer MySQL dans XAMPP.
2. Extraire le dossier.
3. Lancer `start_app.bat`.
4. Ouvrir `http://127.0.0.1:8080/install.php`.
5. Cliquer sur installer.
6. Ouvrir `http://127.0.0.1:8080`.

## Comptes
- Admin : `admin / admin123`
- Gérant : `gerant / gerant123`
- Agent : `agent / agent123`
- Comptable : `comptable / comptable123`
- Mécanicien : `mecanicien / mecanicien123`

## Modules
- Tableau de bord
- Clients
- Véhicules
- Réservations
- Contrats imprimables/PDF
- Deuxième conducteur optionnel dans le contrat
- Paiements
- Maintenance
- Rapports
- Utilisateurs et rôles

## Logique corrigée
- Pas de saisie d'ID par l'utilisateur : sélection par nom/client/véhicule.
- Le 2e conducteur est une option dans la réservation/contrat.
- Accès limités selon rôle.
- Bouton de déconnexion dans le menu.
