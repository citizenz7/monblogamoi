# MONBLOGAMOI
`MONBLOGAMOI` est un projet de blog entièrement basé sur le framework PHP Symfony 5.

## Template
Entièrement basé sur Bootsrap 5 "from scratch"

## prerequisite 
* PHP 7.4.3=+
* MySQL
* Symfony CLI (5)
* Composer
* Mail server (registration, contact, ...)

## features 
### Languages and frameworks
* PHP 7.4.3
* MySQL (MariaDB)
* HTML / CSS
* Javascript (Aos, Bootstrap)
* Bootstrap (v5)
* Google fonts (Poppins)
* Font awesome 5
### Translation
* Multilingual
* TransalatorInterface : EN, FR, ES
Vos pouvez ajouter ou mettre à jour les fichiers de langues avec (par exemple pour l'espagnol): `symfony console translation:update --force es`
Votre fichier se trouve dans /translations. Vous devez terminer la traduction à la main.
Utilisez la meme commande pour mettre à jour le fichier si vous avez ajouté/modifié la traduction.
Si vous ajoutez une nouvelle langue, n'oubliez pas de mettre à jour config/services.yaml en ajoutant les 2 première lettre de la langue à `app.locales` comme ceci : `app.locales: [en, fr, es]`
### Entités
* Article
* Comment **(TODO)**
* Tag
* Category
* User
### Pages
* home
* contact
* article single
* categories index
* category single
* tags index
* tag single
* user profile
* login
* registration
* admin section et dashboard

## Pour initialiser le projet :
* clonez le repository: `git clone https://github.com/citizenz7/blog.git`
* Installez tous les packages : `composer install`
* Configurez `.env.local` en copiant `.env` et en modifiant :
    * Les infos de connexions à MySQL (login/mot de passe, serveur, base de données)
    * MAILER_DSN : pour envoyer des mails (J'utilise Mailhog en dev et un "vrai" serveur SMTP en prod)
* Créez la nouvelle base de données : `symfony console doctrine:database:create`
* Créez la migration : `symfony console make:migration`
* Exportez dans MySQL : `symfony console doctrine:migrations:migrate`
* Installez CKEditor : `symfony console ckeditor:install`
* Installez CKEditor assets : `symfony console assets:install public`
* Changez l'APP-ENV en prod dans `.env.local` (APP_ENV=prod)
* Videz le cache : `symfony console cache:clear`
* Créez un premier compte (/register) puis attribuez lui un role admin ["ROLE_ADMIN"] directement depuis PHPMyadmin
* Connectez vous avec ce nouveau compte (/login) puis rendez-vous sur le panneau d'administration (/admin)
* Créez des catégories, puis des tags, ... puis des articles
* Les tags seront automatiquement créés quand vous les écrirez pour la première fois (SELECT2 JQuery plugin) **(TODO)**