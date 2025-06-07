<?php

namespace OsdAurox;

use PDO;

class Paginator
{
    private PDO $pdo;
    private string $query;       // Requête pour récupérer les données (avec LIMIT et OFFSET)
    private string $countQuery;  // Requête pour le total (sans LIMIT ni OFFSET)
    private array $bindParams;   // Paramètres à binder
    private int $currentPage;    // Page actuelle
    private int $perPage;        // Nombre d'éléments par page
    private int $totalItems;     // Total d'éléments dans la base
    private int $totalPages;     // Total de pages
    private string $url;

    /**
     * Constructeur
     *
     * @param PDO $pdo Instance PDO
     * @param string $query Requête SQL principale (doit inclure `LIMIT :limit OFFSET :offset`)
     * @param string $count_query Requête SQL pour compter le total des éléments
     * @param array $bind_params Paramètres à binder (pour filtrage)
     * @param int $per_page Nombre d'éléments par page
     * @param int $current_page Page actuelle
     */
    public function __construct(
        PDO    $pdo,
        string $query,
        string $count_query,
        string $url,
        array  $bind_params = [],
    )
    {
        $this->pdo = $pdo;
        $this->query = $query;
        $this->countQuery = $count_query;
        $this->bindParams = $bind_params;
        $this->perPage = max(1, Sec::getPerPage());
        $this->currentPage = max(1, Sec::getPage()); // Minimum 1
        $this->totalItems = $this->calculateTotalItems();
        $this->totalPages = (int)ceil($this->totalItems / $this->perPage);
        $this->url = $url;
    }

    /**
     * Calcule le nombre total d'éléments dans la base
     *
     * @return int
     */
    private function calculateTotalItems(): int
    {
        $stmt = $this->pdo->prepare($this->countQuery);

        // Binder les paramètres nécessaires
        foreach ($this->bindParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return (int)$stmt->fetchColumn();
    }

    /**
     * Récupère les éléments pour la page actuelle
     *
     * @return array
     */
    public function getItems(): array
    {

        if (!str_contains($this->query, 'LIMIT') && !str_contains($this->query, 'OFFSET')) {
            $this->query .= ' LIMIT :limit OFFSET :offset';
        }

        $stmt = $this->pdo->prepare($this->query);

        // Calcul des paramètres pour LIMIT et OFFSET
        $offset = ($this->currentPage - 1) * $this->perPage;

        // Ajouter LIMIT et OFFSET
        $stmt->bindValue(':limit', $this->perPage, PDO::PARAM_INT);
        $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);

        // Ajouter les autres paramètres
        foreach ($this->bindParams as $key => $value) {
            $stmt->bindValue($key, $value);
        }

        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


    /**
     * Génère les boutons de pagination
     *
     * @param string $url URL de base (exemple : `company_list.php`)
     * @return string HTML des boutons de pagination
     */
    /**
     * Génère les boutons de pagination
     *
     * @return string HTML des boutons de pagination
     */
    public function renderPagination(): string
    {
        $perPage = Sec::getPerPage() ?? 10;

        // Construire les parties de l'URL et les paramètres existants
        $url = str_contains($this->url, '?') ? '&per_page=' : '?per_page=';

        $url .= $perPage . '&page=';

        $baseUrl = $this->url . $url;

        // Reconstruire l'URL de base

        $html = '<div class="row">';
        $html .= '<div class="col-12 d-flex justify-content-center">';

        $html .= '<nav><ul class="pagination">';

        // Bouton "Début |<"
        if ($this->currentPage > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . '1">&laquo;&laquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo;&laquo;</span></li>';
        }

        // Bouton "Précédent <"
        if ($this->currentPage > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . ($this->currentPage - 1) . '">&laquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }

        // Boutons pour les pages principales
        $startPage = max(1, $this->currentPage - 5);
        $endPage = min($this->totalPages, $this->currentPage + 4);

        for ($page = $startPage; $page <= $endPage; $page++) {
            if ($page == $this->currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $page . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . $page . '">' . $page . '</a></li>';
            }
        }

        // Bouton "Suivant >"
        if ($this->currentPage < $this->totalPages) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . ($this->currentPage + 1) . '">&raquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }

        // Bouton "Fin >|"
        if ($this->currentPage < $this->totalPages) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $baseUrl . $this->totalPages . '">&raquo;&raquo;</a></li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&raquo;&raquo;</span></li>';
        }

        $html .= '</ul></nav>';
        $html .= '</div>'; // Fin de col-12
        $html .= '</div>'; // Fin de row

        return $html;
    }

    /**
     * Récupère le nombre total d'éléments
     *
     * @return int
     */
    public function getTotalItems(): int
    {
        return $this->totalItems;
    }

    /**
     * Récupère le nombre total de pages
     *
     * @return int
     */
    public function getTotalPages(): int
    {
        return $this->totalPages;
    }

    /**
     * Récupère la page actuelle
     *
     * @return int
     */
    public function getCurrentPage(): int
    {
        return $this->currentPage;
    }

    public function renderTotalInfo()
    {
        $totalItems = $this->totalItems;
        $totalPages = $this->totalPages;
        $perPage = $this->perPage;

        $html = '<div class="row">';
        $html .= '<div class="col-12 d-flex justify-content-center ">';
        $html .= '<p>' . I18n::t('Affichage de ') . $perPage . I18n::t(' résultats sur un total de ') . $totalItems . ' - ' . $totalPages . ' ' . I18n::t('pages') . '</h3>';
        $html .= '</div>';
        $html .= '</div>';
        return $html;
    }

    /**
     * Génère un formulaire pour sélectionner le nombre d'éléments par page
     *
     * @return string HTML du sélecteur
     */
    public function renderPerPageSelector(): string
    {
        // Liste des options disponibles pour le menu déroulant
        $options = [10, 20, 50, 100, 500, 1000];

        // Ajouter le HTML pour le sélecteur
        $html = '<form method="get" class="form-inline d-inline-block">';
        $html .= '<label for="perPageSelector" class="mr-2">Afficher par page :</label>';
        $html .= '<input type="hidden" name="page" value="' . Sec::getPage() . '">';
        $html .= '<select id="perPageSelector" name="per_page" class="form-select form-select-sm" onchange="this.form.submit()">';

        foreach ($options as $option) {
            $selected = $option == $this->perPage ? ' selected' : ''; // Rendre l'option sélectionnée
            $html .= '<option value="' . $option . '"' . $selected . '>' . $option . '</option>';
        }

        $html .= '</select>';
        $html .= '</form>';

        return $html;
    }
}