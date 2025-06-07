<?php

// Scan tous les fichiers *Test.php
foreach (glob(__DIR__ . '/*Test.php') as $file) {
    echo "\n================================\n";
    echo "Exécution de : $file\n";

    // Exécute chaque fichier comme un processus PHP indépendant
    passthru("php $file");

    // Une séparation pour différencier les résultats des tests
    echo "\n================================\n";
}
