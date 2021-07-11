# MONBLOGAMOI
`MONBLOGAMOI` est un projet de blog entièrement basé sur le framework PHP Symfony 5.

Version 0.2.1 (en cours de développement)

## Template
Entièrement basé sur Bootsrap 5 (+ CSS/JS)

## prérequis
* PHP 7.4.3=+
* MySQL
* Symfony CLI (5)
* Composer
* Mail server (registration, contact, ...)

## Fonctionnalités
### Languages, librairies et frameworks
* PHP 7.4.3
* MySQL (MariaDB)
* HTML / CSS
* Javascript (Aos, Bootstrap)
* JQuery (Select2)
* Bootstrap (v5)
* Google fonts (Poppins)
* Font awesome 5
* Captcha
### Traduction
* Multilingual
* TransalatorInterface : FR, EN
Vos pouvez ajouter ou mettre à jour les fichiers de langues avec (par exemple pour le français): `symfony console translation:update --force fr`
Votre fichier se trouve dans /translations. Vous devez terminer la traduction à la main.
Utilisez la meme commande pour mettre à jour le fichier si vous avez ajouté/modifié la traduction.
Si vous ajoutez une nouvelle langue, n'oubliez pas de mettre à jour config/services.yaml en ajoutant les 2 première lettre de la langue à `app.locales` comme ceci (exemple d'ajout de l'espagnol) : `app.locales: [fr, en, es]`
### Entités
* Article
* Comment
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
* admin section et dashboard
* Rss
* login
* registration

## Pour initialiser le projet :
* clonez le repository: `git clone https://github.com/citizenz7/blog.git`
* Placez vous dans le nouveau dossier : `cd blog`
* Installez tous les packages : `composer install`
* Créez et configurez `.env.local` en copiant `.env` et en modifiant :
    * Les infos de connexions à MySQL (login/mot de passe, serveur, base de données)
    * MAILER_DSN : pour envoyer des mails (serveur SMTP)
    * Changez l'APP-ENV en prod : `APP_ENV=prod`
* Configurez les variables d'info du site dans **config/packages/twig.yaml**. Ces infos s'appliqueront partout sur le site.
* Configurez **src/Controller/ContactController.php** en mettant les bonnes infos (e-mail, URL site web, etc.)
* Créez la nouvelle base de données : `symfony console doctrine:database:create`
* Créez la migration : `symfony console make:migration`
* Exportez dans MySQL : `symfony console doctrine:migrations:migrate`
* Installez CKEditor : `symfony console ckeditor:install`
* Installez CKEditor assets : `symfony console assets:install public`
* Videz le cache : `symfony console cache:clear`
* Créez un premier compte (/register) puis attribuez lui un role admin ["ROLE_ADMIN"] directement depuis PHPMyadmin
* Connectez vous avec ce nouveau compte (/login) puis rendez-vous sur le panneau d'administration (/admin)
* Créez des catégories (/admin/category/new)
* Créez un article. Les tags seront automatiquement créés quand vous les écrirez pour la première fois en faisant un espace après chaque tag (SELECT2 JQuery plugin). Vous les retrouverez dans la liste déroulante.
* Modifiez/adaptez la page `A propos`

# TODO
* Catégories et Tags index page avec cards
* Colonnes catégories articles sur page BLOG
* Messages flash
* traduction messages flash dans Controller + flash bags dans templates
* pages d'erreurs personnalisées
