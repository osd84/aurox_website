# Aurox Website

**Un Starter pack de Website basé sur OSD_Aurox**



## Utilisation comme squelette d’application

Vous pouvez aussi utiliser Aurox comme point de départ pour une application web.

1. Téléchargez la dernière release :
   👉 [https://github.com/osd84/aurox_website/releases/latest](Dernière Release Zip)

2. Décompressez-la à la racine de votre projet web.


3. Copiez le fichier `conf_example.php` en `conf.php` et adaptez-le à votre projet.

5. Configurez Apache pour exposer uniquement le dossier `/public` :

```apache
DocumentRoot /var/www/mon_projet/public/
```

---

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

