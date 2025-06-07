<?php

namespace OsdAurox;

class ErrorMonitoring
    /*
    * // Exécute le gestionnaire d'arrêt défini
    *    ErrorHandler::initialize();
    *
    *    // Simulation d'une erreur fatale (vous pouvez la supprimer)
    *   echo $undefinedVar; // Pas fatal, juste un warning
    *    nonExistentFunction(); // Provoque une erreur fatale
    *
     */
{
    public static function initialize(): void
    {
        // Enregistre cette fonction à appeler à la fin de l'exécution du script
        register_shutdown_function([self::class, 'handleFatalError']);
    }

    public static function handleFatalError(): void
    {
        $error = error_get_last(); // Récupère la dernière erreur

        // Vérifie si une erreur fatale s'est produite
        if ($error && in_array($error['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR])) {
            // Prépare un message pour Discord
            $message = sprintf(
                "🚨 **Erreur Fatale !** 🚨\nFile: %s\nLine: %d\nMessage: %s",
                $error['file'],
                $error['line'],
                $error['message']
            );

            // Envoie le message via Discord (méthode d'envoi à réaliser)
            Discord::send($message);
        }
    }

}