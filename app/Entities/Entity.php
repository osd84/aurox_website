<?php

namespace App\Entities;

/**
 * Classe abstraite représentant une entité de base
 * pour l'accès aux données de manière orientée objet
 */
abstract class Entity
{
    private array $data;
    private \PDO $PDO;

    public function __construct(\PDO $pdo, array $datas = [])
    {
        $this->data = $datas;

        if ($isDirty) {
            $this->dirty = array_keys($data);
        }
    }


}
