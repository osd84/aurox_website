# Aurox Website

**Un Starter pack de Website basé sur OSD_Aurox**

Démo : https://demo-site.aurox.fr/

Doc : https://aurox.fr/start.php

Téléchargez la dernière release :  https://aurox.fr/versions/aurox_website-last.zip


## Structure de base

* Vos **routes** vont dans [`app/AppUrls.php`](app/AppUrls.php)
* Vos **modèles**, si besoin, dans [`app/Models`](app/Models)
* Incluez [`aurox.php`](aurox.php) dans vos fichiers `.php` comme dans [`public/index.php`](public/index.php)
* Créez vos propres templates, contrôleurs, etc.

> Aurox ne fournit **ni système de routing**, **ni sécurité intégrée**.
> C’est à vous de gérer. C’est volontaire.

---

## Sécurité & Limitations

* [`BaseModel.php`](src/OsdAurox/BaseModel.php) **n’est pas un ORM**
* Les arguments `$field` sont vulnérables aux injections SQL. **N’y passez jamais de variables.**
* Le reste utilise PDO et est normalement sécurisé.

---

## Note 

Ce projet est partagé **tel quel**, dans un esprit de liberté et de curiosité. <br>
Pas de promesse, pas de magie.

---

Documentation : [aurox.fr](https://aurox.fr)

