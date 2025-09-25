Pour lancer le projet:
créer un fichier DatabaseParam.php (voir DatabaseParam_Exemple.php) et remplir les champs.
puis dans un powershell/bash:
- php init_database.php
-php -S localhost:8000 php -S localhost:8000

Gestion de la Liste de médias:
    ● Être capable d’ajouter, modifier et supprimer des Médias, de les emprunter & de les rendre.
        ○ Uniquement pour les utilisateurs authentifiés
    ● Un utilisateur peut rechercher un film de manière approximative.
    ● L’utilisateur doit pouvoir trier. ✅
    ● Les classes doivent être documentées. ✅
    ● Gérer des illustrations par Médias. (si par illustration on entend un template unique pour chaque media ?) ✅
    ● Gérer du Lazy Loading (Optionnel)

Authentification :
    ● Mettez en place un système d'inscription et de connexion pour les utilisateurs. ✅
    ● Utilisez des mots de passe hashés et des sessions pour gérer l'authentification. ✅
    ● Le mot de passe doit être soumis à une regex. ✅
        ○ Règle classique : 8 caractères min, majuscule, minuscule, chiffre et caractères spéciaux. ✅
        ○ Le mot de passe ne doit pas contenir l’identifiant de l’utilisateur. ✅

Tableau de Bord :
    ● Affichez un tableau de bord qui affiche la liste des médias et affiché si ils sont disponibles ou non. ✅
    ● Affichez le nom de l'utilisateur et un lien pour se déconnecter dans une barre de navigation. ✅
