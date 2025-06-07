<?php

require_once '../aurox.php';

use OsdAurox\Validator;
use OsdAurox\I18n;
use osd84\BrutalTestRunner\BrutalTestRunner;


$tester = new BrutalTestRunner();
$tester->header(__FILE__);

// Test de la méthode addError et getErrors
$GLOBALS['i18n'] = new I18n('fr');

$rules = [
    Validator::create('email')->email()->length(20),
    Validator::create('username')->notEmpty(),
];
$data = [
    'email' => 'invalid-email',
    'username' => '',
];

$tester->header("Test de la méthode combiné");

$result = $rules[0]->validate($data['email']);
$tester->assertEqual($result[0]['msg'],  'doit être une email valide', 'email + length : doit être une email valide');
$tester->assertEqual($result[1]['msg'],  'doit contenir minimum 20 caractères', 'email + length : doit contenir minimum 20 caractères');

// test notEmpty
$tester->header("Test de la méthode notEmpty()");

$result = Validator::create('username')->notEmpty()->validate($data['username']);
$tester->assertEqual($result[0]['msg'], 'doit être rempli', 'notEmpty : doit être rempli');

$result = Validator::create('username')->notEmpty()->validate(null);
$tester->assertEqual($result[0]['msg'], 'doit être rempli', 'notEmpty : doit être rempli');


$tester->header("Test de la méthode length()");
// test length
$result = Validator::create('email')->length(min : 0, max: 10)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir maximum 10 caractères', 'length : doit contenir maximum 10 caractères');

$result = Validator::create('email')->length(min : 10, max: 0)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir minimum 10 caractères', 'length : doit contenir minimum 10 caractères');

$result = Validator::create('email')->length(min : 5, max: 10)->validate($data['email']);
$tester->assertEqual($result[0]['msg'], 'doit contenir entre 5 et 10 caractères', 'length :  doit contenir entre 5 et 10 caractères');

// test required
$tester->header("Test de la méthode required()");

// Test avec une chaîne vide
$result = Validator::create('field')->required()->validate('');
$tester->assertEqual($result[0]['msg'], 'champ obligatoire', 'required : chaîne vide doit être invalide');
$tester->assertEqual($result[0]['valid'], false, 'required : chaîne vide doit retourner false');

// Test avec une chaîne contenant uniquement des espaces
$result = Validator::create('field')->required()->validate('   ');
$tester->assertEqual($result[0]['valid'], false, 'required : chaîne avec espaces doit être invalide');

// Test avec une chaîne valide
$result = Validator::create('field')->required()->validate('valeur');
$tester->assertEqual(count($result), 0, 'required : chaîne non-vide doit être valide');

// Test avec null
$result = Validator::create('field')->required()->validate(null);
$tester->assertEqual($result[0]['valid'], false, 'required : null doit être invalide');

// Test avec tableau vide
$result = Validator::create('field')->required()->validate([]);
$tester->assertEqual($result[0]['valid'], false, 'required : tableau vide doit être invalide');

// Test avec tableau non-vide
$result = Validator::create('field')->required()->validate(['item']);
$tester->assertEqual(count($result), 0, 'required : tableau non-vide doit être valide');

// Test avec nombre
$result = Validator::create('field')->required()->validate(0);
$tester->assertEqual(count($result), 0, 'required : nombre doit être valide');

// Test avec booléen
$result = Validator::create('field')->required()->validate(false);
$tester->assertEqual(count($result), 0, 'required : booléen doit être valide');


$tester->header("Test de la méthode stringType()");
$result = Validator::create('field')->stringType()->validate("test");
$tester->assertEqual(count($result), 0, 'stringType : chaîne valide doit passer');

$result = Validator::create('field')->stringType()->validate("");
$tester->assertEqual(count($result), 0, 'stringType : chaîne vide doit passer');

$result = Validator::create('field')->stringType()->validate(123);
$tester->assertEqual($result[0]['valid'], false, 'stringType : nombre doit échouer');

$result = Validator::create('field')->stringType()->validate(null);
$tester->assertEqual($result[0]['valid'], false, 'stringType : null doit échouer');

$result = Validator::create('field')->stringType()->validate([]);
$tester->assertEqual($result[0]['valid'], false, 'stringType : tableau doit échouer');

// Tests pour intType()
$tester->header("Test de la méthode intType()");

$result = Validator::create('field')->intType()->validate(123);
$tester->assertEqual(count($result), 0, 'intType : entier valide doit passer');

$result = Validator::create('field')->intType()->validate(0);
$tester->assertEqual(count($result), 0, 'intType : zéro doit passer');

$result = Validator::create('field')->intType()->validate(-123);
$tester->assertEqual(count($result), 0, 'intType : entier négatif doit passer');

$result = Validator::create('field')->intType()->validate("123");
$tester->assertEqual($result[0]['valid'], false, 'intType : chaîne numérique doit échouer');

$result = Validator::create('field')->intType()->validate(12.3);
$tester->assertEqual($result[0]['valid'], false, 'intType : float doit échouer');

// Tests pour floatType()
$tester->header("Test de la méthode floatType()");

$result = Validator::create('field')->floatType()->validate(12.3);
$tester->assertEqual(count($result), 0, 'floatType : float valide doit passer');

$result = Validator::create('field')->floatType()->validate(-12.3);
$tester->assertEqual(count($result), 0, 'floatType : float négatif doit passer');

$result = Validator::create('field')->floatType()->validate(123);
$tester->assertEqual($result[0]['valid'], false, 'floatType : entier doit échouer');

$result = Validator::create('field')->floatType()->validate("12.3");
$tester->assertEqual($result[0]['valid'], false, 'floatType : chaîne numérique doit échouer');

// Tests pour min()
$tester->header("Test de la méthode min()");

$result = Validator::create('field')->min(10)->validate(15);
$tester->assertEqual(count($result), 0, 'min : nombre supérieur doit passer');

$result = Validator::create('field')->min(10)->validate(10);
$tester->assertEqual(count($result), 0, 'min : nombre égal doit passer');

$result = Validator::create('field')->min(10)->validate(5);
$tester->assertEqual($result[0]['valid'], false, 'min : nombre inférieur doit échouer');

$result = Validator::create('field')->min(10.5)->validate(10.6);
$tester->assertEqual(count($result), 0, 'min : float supérieur doit passer');

$result = Validator::create('field')->min(10)->validate("abc");
$tester->assertEqual($result[0]['valid'], false, 'min : chaîne non numérique doit échouer');

// Tests pour max()
$tester->header("Test de la méthode max()");

$result = Validator::create('field')->max(10)->validate(5);
$tester->assertEqual(count($result), 0, 'max : nombre inférieur doit passer');

$result = Validator::create('field')->max(10)->validate(10);
$tester->assertEqual(count($result), 0, 'max : nombre égal doit passer');

$result = Validator::create('field')->max(10)->validate(15);
$tester->assertEqual($result[0]['valid'], false, 'max : nombre supérieur doit échouer');

$result = Validator::create('field')->max(10.5)->validate(10.4);
$tester->assertEqual(count($result), 0, 'max : float inférieur doit passer');

$result = Validator::create('field')->max(10)->validate("abc");
$tester->assertEqual($result[0]['valid'], false, 'max : chaîne non numérique doit échouer');

// Tests pour startWith()
$tester->header("Test de la méthode startWith()");

$result = Validator::create('field')->startWith('test')->validate("test123");
$tester->assertEqual(count($result), 0, 'startWith : correspondance exacte doit passer');

$result = Validator::create('field')->startWith('test')->validate("abc123");
$tester->assertEqual($result[0]['valid'], false, 'startWith : sans correspondance doit échouer');

$result = Validator::create('field')->startWith('Test', false)->validate("test123");
$tester->assertEqual(count($result), 0, 'startWith : insensible à la casse doit passer');

$result = Validator::create('field')->startWith('Test')->validate("test123");
$tester->assertEqual($result[0]['valid'], false, 'startWith : sensible à la casse doit échouer');

$result = Validator::create('field')->startWith('test')->validate(123);
$tester->assertEqual($result[0]['valid'], false, 'startWith : non-string doit échouer');

$result = Validator::create('field')->startWith('')->validate("test");
$tester->assertEqual(count($result), 0, 'startWith : préfixe vide doit passer');

// Tests combinés
$tester->header("Tests combinés");

$result = Validator::create('field')
    ->required()
    ->intType()
    ->min(0)
    ->max(100)
    ->validate(50);
$tester->assertEqual(count($result), 0, 'combinaison : valeur valide doit passer');

$result = Validator::create('field')
    ->required()
    ->stringType()
    ->startWith('test')
    ->validate("test123");
$tester->assertEqual(count($result), 0, 'combinaison : chaîne valide doit passer');


// Tests pour positive()
$tester->header("Test de la méthode positive()");

$result = Validator::create('field')->positive()->validate(15);
$tester->assertEqual(count($result), 0, 'positive : nombre positif doit passer');

$result = Validator::create('field')->positive()->validate(0);
$tester->assertEqual($result[0]['valid'], false, 'positive : zéro doit échouer');

$result = Validator::create('field')->positive()->validate(-5);
$tester->assertEqual($result[0]['valid'], false, 'positive : nombre négatif doit échouer');

$result = Validator::create('field')->positive()->validate(10.5);
$tester->assertEqual(count($result), 0, 'positive : float positif doit passer');

$result = Validator::create('field')->positive()->validate("15");
$tester->assertEqual(count($result), 0, 'positive : chaîne numérique positive doit passer');

$result = Validator::create('field')->positive()->validate("abc");
$tester->assertEqual($result[0]['valid'], false, 'positive : chaîne non numérique doit échouer');

// Tests pour date()
$tester->header("Test de la méthode date()");

$result = Validator::create('field')->date()->validate("2024-03-13");
$tester->assertEqual(count($result), 0, 'date : format Y-m-d valide doit passer');

$result = Validator::create('field')->date()->validate("2024-13-13");
$tester->assertEqual($result[0]['valid'], false, 'date : mois invalide doit échouer');

$result = Validator::create('field')->date('d/m/Y')->validate("13/03/2024");
$tester->assertEqual(count($result), 0, 'date : format d/m/Y valide doit passer');

$result = Validator::create('field')->date()->validate("2024-03-13 14:30:00");
$tester->assertEqual($result[0]['valid'], false, 'date : datetime dans date doit échouer');

$result = Validator::create('field')->date()->validate("invalid-date");
$tester->assertEqual($result[0]['valid'], false, 'date : format invalide doit échouer');

$result = Validator::create('field')->date()->validate(12345);
$tester->assertEqual($result[0]['valid'], false, 'date : nombre doit échouer');

// Tests pour dateTime()
$tester->header("Test de la méthode dateTime()");

$result = Validator::create('field')->dateTime()->validate("2024-03-13 14:30:00");
$tester->assertEqual(count($result), 0, 'dateTime : format Y-m-d H:i:s valide doit passer');

$result = Validator::create('field')->dateTime()->validate("2024-03-13");
$tester->assertEqual($result[0]['valid'], false, 'dateTime : date sans heure doit échouer');

$result = Validator::create('field')->dateTime('d/m/Y H:i')->validate("13/03/2024 14:30");
$tester->assertEqual(count($result), 0, 'dateTime : format personnalisé valide doit passer');

$result = Validator::create('field')->dateTime()->validate("2024-03-13 25:00:00");
$tester->assertEqual($result[0]['valid'], false, 'dateTime : heure invalide doit échouer');

$result = Validator::create('field')->dateTime()->validate("invalid-datetime");
$tester->assertEqual($result[0]['valid'], false, 'dateTime : format invalide doit échouer');

$result = Validator::create('field')->dateTime()->validate(12345);
$tester->assertEqual($result[0]['valid'], false, 'dateTime : nombre doit échouer');


// Tests pour inArray()
$tester->header("Test de la méthode inArray()");

// Test avec des valeurs simples
$values = [1, 2, 3, 4, 5];
$result = Validator::create('field')->inArray($values)->validate(3);
$tester->assertEqual(count($result), 0, 'inArray : valeur présente doit passer');

$result = Validator::create('field')->inArray($values)->validate(6);
$tester->assertEqual($result[0]['valid'], false, 'inArray : valeur absente doit échouer');
$tester->assertEqual($result[0]['msg'], 'must be one of the following values : 1, 2, 3, 4, 5', 'inArray : message d\'erreur correct');

// Test avec des chaînes
$fruits = ['pomme', 'poire', 'banane'];
$result = Validator::create('field')->inArray($fruits)->validate('poire');
$tester->assertEqual(count($result), 0, 'inArray : chaîne présente doit passer');

$result = Validator::create('field')->inArray($fruits)->validate('orange');
$tester->assertEqual($result[0]['valid'], false, 'inArray : chaîne absente doit échouer');

// Test avec comparaison stricte
$mixedValues = ['1', '2'];
$result = Validator::create('field')->inArray($mixedValues, true)->validate('1');
$tester->assertEqual(count($result), 0, 'inArray : comparaison stricte doit passer pour type identique');

$result = Validator::create('field')->inArray($mixedValues, true)->validate(1);
$tester->assertEqual($result[0]['valid'], false, 'inArray : comparaison stricte doit échouer pour type différent');

// Test avec tableau vide
$result = Validator::create('field')->inArray([])->validate('test');
$tester->assertEqual($result[0]['valid'], false, 'inArray : tableau vide doit échouer');

// Test avec valeur null
$nullableValues = ['test', null, 1];
$result = Validator::create('field')->inArray($nullableValues)->validate(null);
$tester->assertEqual(count($result), 0, 'inArray : valeur null présente doit passer');

// Tests combinés
$result = Validator::create('field')
    ->required()
    ->inArray(['pomme', 'poire', 'banane'])
    ->validate('pomme');
$tester->assertEqual(count($result), 0, 'combinaison : valeur valide doit passer');

$result = Validator::create('field')
    ->required()
    ->inArray(['pomme', 'poire', 'banane'])
    ->validate('');
$tester->assertEqual($result[0]['valid'], false, 'combinaison : valeur vide doit échouer');



// Tests pour optional()
$tester->header("Test de la méthode optional()");

// Test avec une chaîne vide
$result = Validator::create('field')
    ->optional()
    ->required()
    ->validate('');
$tester->assertEqual(count($result), 1, 'optional + required : doit bloquer, required est plus fort');

// Test avec null
$result = Validator::create('field')
    ->optional()
    ->notEmpty()
    ->validate(null);
$tester->assertEqual(count($result), 0, 'optional : null doit passer');

// Test avec une valeur valide
$result = Validator::create('field')
    ->optional()
    ->email()
    ->validate('test@example.com');
$tester->assertEqual(count($result), 0, 'optional : email valide doit passer');

// Test avec valeur invalide (ne doit pas être ignoré)
$result = Validator::create('field')
    ->optional()
    ->email()
    ->validate('invalid-email');
$tester->assertEqual($result[0]['valid'], false, 'optional : email invalide ne doit pas passer');

// Test combiné avec plusieurs validateurs
$result = Validator::create('field')
    ->optional()
    ->notEmpty()
    ->length(3, 10)
    ->validate('');
$tester->assertEqual(count($result), 0, 'optional : champ vide avec multiples validateurs doit passer');

// Test avec une valeur non vide mais invalide
$result = Validator::create('field')
    ->optional()
    ->length(3, 10)
    ->validate('ab');
$tester->assertEqual($result[0]['valid'], false, 'optional : valeur non vide invalide ne doit pas passer');

// Test avec tableau vide
$result = Validator::create('field')
    ->optional()
    ->notEmpty()
    ->validate([]);
$tester->assertEqual(count($result), 0, 'optional : tableau vide doit passer');

// Test avec chaîne contenant uniquement des espaces
$result = Validator::create('field')
    ->optional()
    ->notEmpty()
    ->validate('   ');
$tester->assertEqual(count($result), 1, 'optional : chaîne avec espaces ne doit pas passer');

// Test avec chaîne vide
$result = Validator::create('field')
    ->optional()
    ->notEmpty()
    ->validate('');
$tester->assertEqual(count($result), 0, 'optional : chaîne vide doit passer');

// Test désactivation de optional
$validator = Validator::create('field');
$validator->optional();
$validator->notEmpty();
$result = $validator->validate('');
$tester->assertEqual(count($result), 0, 'optional : doit rester optionnel même après ajout de règles');


$tester->footer(exit: false);