# Aurox

**Une collection d’utilitaires PHP inspirée du Brutalisme et du Brutalism Dev Design.**

Documentation : https://aurox.fr


---

⚠️ **ALPHA - Ne pas utiliser en production**  
Le projet est en cours de développement, tout peut encore changer. À vos risques et périls. 😉

**Licence : MIT**  
**Prérequis : Apache2 + PHP ≥ 8.1**

---

## À propos

Aurox peut s’utiliser de deux façons :

- comme **librairie utilitaire via Composer**
- comme **moteur d’application web minimaliste**

Ce dépôt contient un **kit de démarrage** dans [`/public`](public) avec un exemple fonctionnel.

> Aurox n’est pas un framework.  
> C’est un moteur simple, brut, avec des outils basiques pour démarrer vite.

Le code est en **phase de R&D**. Beaucoup de composants sont encore au stade de prototype :  
tests, benchmarks, sécurité et documentation sont à venir.

![screen.png](public/img/screen.png)

---

## Utilisation comme librairie

Aurox peut être intégrée à vos projets via Composer :

```bash
composer require osd84/aurox
````

Puis, dans votre projet :

```php
require_once __DIR__ . '/vendor/autoload.php';
```

Toutes les classes et fonctions sont accessibles, selon les exemples fournis ici.

---

## Utilisation comme squelette d’application

Vous trouverez ici le Starter Pack pour Website et les infos :<br>

Starter Pack Website : https://github.com/osd84/aurox_website <br>
Starter Pack Blog : #Bientôt <br>
Starter Pack E-commerce : #Bientôt <br>

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

Documentation : [doc.md](doc.md)

