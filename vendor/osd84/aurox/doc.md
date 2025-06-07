
# Aurox Documentation


# Globals variables

Activer le mode debug dans : `conf.php`[conf.php](conf.php)


```php
'debug' => true
```


## API

Permet de retourner des réponses JSON dans un format standardisé compris par toute l'application, y compris par l'interface Front-end.
Exemple d'utilisation :

```php
$res = new Api();
$res->status = true;  // status de la réponse
$res->success[] = I18n::t('User updated');
$res->redirect_url = AppUrls::ADMIN_USERS;
$res->returnJsonResponse();
Base::dieOrThrow();
```

Attributs spéciaux

```php
$res->status : `true` ou `false`.

$res->errors
$res->warnings
$res->infos
$res->success : Messages affichés sous forme de "toast" en JS.

$res->data : Contient les données à transmettre.
$res->html : Si le rendu HTML est fait côté Back-end.
$res->validators : Format spécifique pour la validation des formulaires côté JS côté Front-end.
```


## Cache

Le cache est stocké dans sous forme de fichiers plats. `/cache_system_h€re`


```php
$cache = new Cache();
$cache_key = "BLOG_CONTACT_MAIL:{$ip}";

// lire le cache
if ($cache->get($cache_key)) {
    // On fait semblant
    Flash::success("Votre message a bien été envoyé.");
    $pass = false;
}

// écrire dans le cache
$cache->set($cache_key, true, 120); // 120 secondes de timeout

// delete dans le cache
$cache->delete($key)

// supprimer tous le cache
$cache->clear()
```

## Csrf

```php
// protéger une view
$csrf = Csrf::protect();

// écrire le token dans un form html
<?= Csrf::inputHtml(); ?>
```

## Base


```php
Base::isMobile()   // retourne bool vrai si tablette ou mobile
Base::dieOrThrow()   // termine l'exect du script via die() ou Throw exeception en cas de test unitaire
Base:asSelectList($array, $value_field = 'name', $key_field = 'id') // retourne un tableau ['id' => , 'name' => ]
Base:redirect($url) // redirect comme il faut
```


## BaseModel


Alias pour les requêtes SQL via PDO, ce n'est pas un ORM, juste des alias. 
Il faut faire attention et lire la doc, certains arguments des fonctions sont sensibles au Sqli, et c'est normal.
Par contre les arguments utilisés pour la recherche sont sécurisés via bind et PDO.

```php
use \OsdAurox\BaseModel

BaseModel::get(pdo, id: int, [select: string = '*']): mixed // retourne la row via son $id,  sqli possible sur $select
BaseModel::getWithRelations(pdo: PDO, id: mixed): array|null // retourne la row via son $îd, avec les relations pas implémenté par défault
BaseModel::getBy(pdo, field: string, value: mixed): array|false // retourne la row via recherche sur champ, $value sécurisé, $field sqli possible
BaseModel::getAll(pdo: PDO, [orderBy: null|string = 'id'], [orderDir: string = 'ASC'], [limit: int|null = 100]): array
BaseModel::getAllBy(pdo, field: string, value: mixed): // retourne les rows via recherche sur champ, $value sécurisé, $field sqli possible
BaseModel::getByIds(pdo, table: string, ids: array, [select: string = '*']): array // retourne les row via PDO::FETCH_ASSOC , $table et $select sont sensibles au Sqli, par contre $ids est sécurisé
BaseModel::count() // total d'un table
BaseModel::delete(pdo, id): bool // $id sécurisé
BaseModel::check_uniq(pdo, field, value): bool // regarde si la valeur pour le champ est unique en table, sqli possible sur $field, securisé sur $value

BaseModel::idsExistsOrEmpty(pdo, table: string, ids: array): bool // retourne True si $ids est vide ou si les $ids existent bien dans la table de la bdd, sinon False

BaseModel::getRules(): array // retourne une liste des OsdAurox\Validator liés à ce modele
BaseModel::canEdit(pdo: PDO, id : int) // retourne vrai si l'utilisateur logué actuel peut éditer cette entité pas implémenté par défault
BaseModel::canEditOrDie(pdo: PDO, id : int;
BaseModel::validate(): bool

BaseModel::getSelect([required: bool = true], [selected: int|null = null]): string // retourne un element Select HTML

// Conseils de nommage des méthodes

BaseModel::getWith<RelationName>(\PDO $pdo, mixed $id): array // Pour les méthodes qui retournent un array entité en chargeant des relations spécifiques
BaseModel::calc<VarName>(\PDO $pdo, $entity): mixed value // Pour les méthodes qui calculent des champs dynamiques
BaseModel::fetch<FieldName>(\PDO $pdo, $entity): mixed value // Quand on doit aller récupérer un champ unique d'une relation ou autre
BaseModel::translate<VarName>(array $entity): string // Pour les méthodes qui retournent des noms internes traduits
BaseModel::format<VarName>(array $entity): string // Pour les méthodes qui formatent des noms internes pour affichage
BaseModel::gen<VarName>(array $entity): mixed value // Pour les méthodes qui génèrent des compteurs ou nom spéciaux, des path
BaseModel::count<Name>(\PDO $pdo): int // Pour les méthodes qui comptent des rows sans calculs COUNT != SUM
BaseModel::resolve<RelationName>(\PDO $pdo, array $entity): array // Pour les méthodes qui cherchent et injectent dans l'array une relation dans $array['<relation_table_name>'] = array
BaseModel::update<fieldName>() // Pour les méthodes qui mettent à jour un Field spécifique et commit

$array = BaseModel::jsonArrayAggDecode($wine, 'myKey');  // Raccourcis pour extraire un JSON_ARRAYAGG ou [ ] si erreur; d'un résultat Array PDO
// SELECT JSON_ARRAYAGG( JSON_OBJECT( 'id', wg.id, 'name', wg.name, 'name_translated', COALESCE(NULLIF(wg.name_$locale, ''), wg.name, '') )
>>> [ [ 'id' => 1, 'name' => 'foo', 'name_translated' => 'bar'], ... ]
```

## LOG

Écris les logs dans /logs/

```php
Log::info()
Log::debug()
Log::warning()
Log::error()
```

## Dict

Utilitaire et Alias pour les dictionnaires

### Dict::get(\$array, \$key, \$default == null)

Récupérer la valeur d'une clef ou defaut, null.

```php
$tab = ['couleur' => 'bleu', 'prix' => 99]
Dict::get($tab, 'couleur')
>>> 'bleu'
Dict::get($tab, 'clef_existe_pas')
>>> null
Dict::get($tab, 'clef_existe_pas', 'defaut_vert')
>>> 'defaut_vert'
```

### Dict:Base::isInArrayAndRevelant()

Retourne vrai si la clef existe Et qu'elle est pertinante.
Ignore ces valeurs: `['null', 'undefined', 'None', '', '0', ' ']`

```php
$tab = ['ok' => '1', 'ko' => 'null']
Dict::isInArrayAndRevelant($tab, 'ok')
>>> true
Dict::isInArrayAndRevelant($tab, 'ko')
>>> false
Dict::isInArrayAndRevelant($tab, 'clef_existe_pas')
>>> false
```


## Discord

### Discord configuration

Ajouter le webhook dans [conf.php](conf.php)

```php
'discordWebhook' => 'https://discord.com/api/webhooks/{key}';
```

### Discord::send(\$message)

Envoyer un message sur un chan discord via webhook

```php
Discord::send($message);
```

## ErrorMonitoring

Permet d'alerter sur discord en PROD
Si des fatal errors arrivent

```php
ErrorMonitoring::initialize();
```

## Filter

Utilitaire pour les templates PHP

```php
<?= Filter::truncate($text, $length, $ending= '...') ?>
>>> Mon super texte ...

<?= Filter::dateFr($date) ?>
>>> d/m/Y

<?= Filter::dateMonthFr($date) ?>
>>> Avril 2025

<?= Filter::dateUs($date) ?>
>>> Y-m-d

<?= Filter::toDayDateUs($date) ?>
>>> Y-m-d // (date du jour direct)
```

## Flash

Injecte les messages par catégorie dans  $_SESSION['messages']

```php
Flash::error()
Flash::success()
Flash::info()
Flash::warning()
Flash::add()
Flash::get($clear=false) // peut récupérer tous les messages et les effacer de $_SESSION 
```

## Fmt

Permet de changer l'affichage de certain champs dans les tempaltes / formulaire

```php
Fmt::bool($field)  
>>> Yes / No, Oui / non (I18n)
```

## Forms

Alias et raccourcis pour générer des formulaires HTML Boostrap

todo

```php
Forms()

Forms::valueAttrOrBlank(entity: array, key: string, [safe: bool = false]): string // Gère le champ value='' dans un input en lui passant une entité 
```

Exemple d'utlisation

```php
Forms()

$formValidator = new FormValidator();
$myEntity = ['title' => 'A title']

$form = new Forms(AppUrls::LOGIN, $formValidator, $myEntity);
<?= $form->input('title', required: true, id: 'title') ?>


```

## Validator & FormValidator

Permet de créer des règles pour valider un tableau associatif

```php
use OsdAurox\Validator

// règles
$rules = [
    'email' => Validator::create('email')->email(),
    'username' => Validator::create('username')->notEmpty(),
];

// données à validées
$data = [
'email' => 'invalid-email',
'username' => '',
];


// vérification d'une seule règle via Validator::validate()
$rule[0]->validate($data['email'])
>>> [ 0 => [ 'field' => 'email', 'valid' => false, 'msg' => 'must be valid email', ] ]


// FormValidator permet de valider directement un tableau provenant d'un Formulaire
// vérification via un FormValidator::validate($data, $rules)
$validator = new FormValidator();
$result = $validator->validate($data, $rules);
>>> $result == False // validation échouée
$errors = $validator->getErrors(); // on regarde les erreurs
>>> [ 'username' => [ 0 => 'must not be empty', ], 'email' => [ 0 => 'must be valid email' ] ]

```

Validator disponibles

Note : required() > optionnal()

```php
use OsdAurox\Validator

Validator::optional() // la règle ne lève pas d'erreur si le champ est empty
Validator::required() // si présent le champ est requis, même si optional() est actif

Validator::notEmpty() // une valeur non vide
Validator::stringType()
Validator::intType()
Validator::floatType()

Validator::email()
Validator::positive() // int ou float, str > 0

Validator::date() // string date au format 'Y-m-d'
Validator::dateTime() // string date au format 'dateTime'

Validator::length([min: int|null = null], [max: int|null = null])
Validator::max(maximum: float|int)
Validator::min(minimum: float|int)
Validator::inArray(allowedValues: array, [strict: bool = false])

Validator::startWith(prefix: string, [caseSensitive: bool = true])
```

On peut les enchaîner 


```php
result = Validator::create('field')
    ->required()
    ->intType()
    ->min(0)
    ->max(100)
    ->validate(50);
```
Les traductions des Validateurs sont stockées dans : [Translations.php](src/OsdAurox/Translations.php)
C'est chargé par le module [I18n.php](src/OsdAurox/I18n.php)

## FormsFilter

Query builder pour côté Admin

todo


## I18n

Traduire avec des fichiers JSON

FIchier dans : [fr.php](translations/fr.php)

```php
use OsdAurox\I18n;
// intialisation du traducteur dans le scope 
$GLOBALS['i18n'] = new I18n('en');

// support des traductions classique via /translations
I18n::t('English')
>>> Anglais

// support des traductions bdd via des array
$entity = [
    'name' => 'default',
    'name_en' => 'trad_en',
    'name_fr' => 'trad_fr',
    'name_it' => 'trad_it',
];
$r = I18n::entity($entity);
>>> 'trad_en'


// locale actuelle
$locale = I18n::currentLocale();
>>> 'fr'


// Récupérer le d'un champ localisé en fonction de la locale actuelle
$field = getLocalizedFieldName([fieldName: string = 'name']): string;
>>> name_fr || name_en
$field = I18n::getLocalizedFieldName('otherField');
>>> otherField_fr || otherField_en

```

## Mailer

Envoyer des mails via PHP

```php
$mail_sent = Mailer::send(to: $mail_to, subject: $mail_subject, content: $html_content);
```

## Paginator

todo

## Sec

```php

use OsdAurox\Sec

Sec::isPost() // true si POST
Sec:getAction()  // lit $_GET['action'] et standardise sa lecture sécurisée
Sec::jsonDatas()   // Retourne une request JSON en tableau

Sec::getRealIpAddr()  // retourne vrai adresse ip du src request

Sec::h($string) // alias htmlspecialchars
Sec::hNoHtml($string) // alias htmlspecialchars + suppression tags HTML
Sec::hArrayKey(array: array, key: string): array // alias htmlspecialchars hNoHtml sur tableau
Sec::hArrayInt(array: array, key: string): array  // alias htmlspecialchars hNoHtml sur tableau + cast en (int)

Sec::safeForLikeStrong($string)   // sécurise fortement un string pour son utilisation en LIKE SQL
Sec::safeForLike($string)   // sécurise légerement un string pour son utilisation en LIKE SQL

Sec::isAdminOrDie($flash = true, $redirect = true)    // regarde le $_SESSION['user']['role']
Sec::isAdminBool()    // regarde le $_SESSION['user']['role'] == 'admin'
Sec::isRoleOrDie($role, $flash = true, $redirect = true)
Sec::isRoleBool($role)  // $role == 'user' , regarde si $_SESSION['user']['role'] == $role et retourne true / false
Sec::isLogged($flash = true, $redirect = true)
Sec::isLoggedBool()  // retourne true ou false si utilisateur connecté
Sec::getUserIdOrDie() // retour l'id de l'user courant ou lève une exception

Sec::noneCsp() // retourne le NONCE Csp courant (typo)

Sec::getPage() // méthode securisée pour lire le $_GET['page']
Sec::getPerPage() // méthode securisée pour lire le $_GET['per_page']


Sec::storeReferer(); // enregistre le referer de la req actuelle
Sec::getReferer(); // retourne le referer de la req precedente
Sec::redirectReferer(); // redirect sur Referer si existe

```

## ViewsShortcuts

Vue complete en tant que méthode

```php
ViewsShortCuts::ListThisDirView($dir)
```

## Ban - Waf
```php

// ban system
Ban::blockBlackListed();
Ban::checkRequest();

# La liste des words sensibles dans les urls est ici 
Ban->$black_list_words

# Le waf de base s'utilise comme ça
Ban::blockBlackListed(); # 1 on regarde si l'ip est déjà bannie
Ban::checkRequest(); # 2 on regarde si la requete, son url actuelle mérite en ban

# Ban directement
Ban:ban()  # recherche l'ip réelle de la requête actuelle et la ban directement

# Ban sur detection de motif suspect en GET & POST
$r = Ban::banIfHackAttempt();
if($r) {
    Discord::send('[BAN] Hack attempt detected on ' . Sec::hNoHtml(AppConfig::get('appName')) . ' by ' . Sec::hNoHtml(Sec::getRealIpAddr()));
}

```

# AppConfig

```php
use OsdAurox\AppConfig
AppConfig::get('key', 'default') // recherche une clef dans /conf.php et retourne la valeur
AppConfig::isDebug() // retourne vrai si l'application est en mode debug conf['debug'] = true
```

# urls
```php
AppUrls::HOME;
AppUrls::LOGIN;
```

# Utils protection view
```php
$nonce_csp
```


## Forms

TODO

```php
$form = new Forms($action_url,
                    validator: $validator,
                    entity: $user ?? null,
                    ajax: isset($user));
<?= $form->formStart(autocomplete: false) ?>
<?= $form->input('email', type: 'email', required: true) ?>
<?= if($use ?? null) ? $form->input('password', type: 'password', placeholder: 'Mot de passe', required: true) : '' ?>
<?= $form->select2Ajax(
    ajax_url: AppUrls::ADMIN_COMPANIES . '?action=select2',
    name: 'id_company',
    id: 'id_company',
    label: 'Company',
    selected: $user['id_company'] ?? null,
)
?>
<?= $form->select2($l_users_types, 'id_user_type', selected: $user['type'] ?? 3) ?>
<?= $form->select($l_roles, 'role', value_field: 'value', name_field: 'label', selected: $user['role'] ?? 'user') ?>
<?= $form->checkbox('active', checked: $user['active'] ?? true) ?>
<?= $form->input('country') ?>
<?= $form->submit(I18n::t('Save')) ?>
<?= $form->formEnd() ?>
<?php if ($user ?? null): ?>
    <?= $form->ajaxSubmit() ?>
<?php endif; ?>


<?= $form->errorDiv('fieldName') ?> <!-- Retourne au format HTML les erreurs de validation d'un formulaire -->

```

## MobileDetect

Une re-implémentation de `Detection\MobileDetect` en version plus "light" intégré au Core de Aurox

À noter, une Table retournera  "Vrai" aussi sur `isMobile()`;

Il y a un Alias dans Base::isMobile() qui retourne Vrai si Mobile ou Tablet
Il est recommandé d'utiliser directement Base::isMobile() au lieu de MobileDetect

```php
use OsdAurox\MobileDetect();
$detect = new MobileDetect();
$detect->isMobile()
$detect->isTablet()
```

## Images

```php
use OsdAurox\Image

Image::resize(sourcePath: string, maxWidth: int, maxHeight: int): string   // redimensionne une image 
Image::reduceToMaxSize(sourcePath: string, [maxSize: float|int = 2]): string // réduit la qualité d'une image par boucle jusqu'à la taille indiqué
Image::resizeAndReduce(sourcePath: string, maxWidth: int, maxHeight: int, maxSize: float): string // redimensionne puis réduit la taille
```

## Modal

La classe `Modal` fournit un système léger pour créer et gérer des fenêtres modales Bootstrap 5 dans l'application Aurox.

Par default le template de la modale doit se trouver dans templates/core/modal.php

```php
$modal = new Modal(title, msg, [type: string = 'info'], [template = null], [btnAccept = null], [btnCancel = null], 
                                [id: string = 'modal-default'], [class: string = 'modal fade'], [showClose: true = true], 
                                [showInput: false = false], [showBtn: true = true])
```

```html
<?php
use OsdAurox\Modal;
?>
<?= Modal::newModal('Ma petite Modal', 'Contenu de la modal', 'info') ?>
<div class="row">
    <div class="col-12">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-default">
            Modale classique #1
        </button>
    </div>
</div>

<?= Modal::newLoader(showClose: True) ?>
<div class="row mt-2">
    <div class="col-12">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-loader">
            Modale de chargement #2
        </button>
    </div>
</div>

<?= Modal::newPrompt(showClose: True) ?>
<div class="row mt-2">
    <div class="col-12">
        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#modal-prompt">
            Modale de saisie #3
        </button>
    </div>
</div>
```

## loader-manager.js

Permet d'afficher une modale bloquante de chargement


## TESTS

Create a mysql database `aurox_test`
Restore the dump `aurox_test.sql`

```sh
cd tests && php run.php
```

Quiet mode 
```sh
php run.php  | grep  'fails' | grep -v Cannot | grep -v '0 fails' ; echo "test terminés"     
```
