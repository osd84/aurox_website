<?php

namespace OsdAurox;

use OsdAurox\I18n;

class Forms
{
    public string $unique_id;
    public ?FormValidator $validator;
    public mixed $entity;
    public array $errors = [];
    public bool $ajax = false;
    public string $action = '';

    public function __construct($action, $validator = null, $entity = null, $ajax = false, $unique_id = null)
    {
        $this->action = $action;
        $this->unique_id = Sec::h($unique_id ?? uniqid('Form'));
        $this->validator = $validator;
        $this->entity = $entity;
        $this->ajax = $ajax;
    }

    // Formatters
    public static function action(array $elem, $edit = true, $detail = true, $delete = true, $delete_confirm = true): string
    {
        $html = '';
        $html .= '<a href="?action=edit&id=' . $elem['id'] . '" class="btn btn-primary mr-1"><span class="fa fa-pencil"></span></a>';
        // detail
        $html .= '<a href="?action=detail&id=' . $elem['id'] . '" class="btn btn-info mr-1" ><span class="fa fa-eye"></span></a>';
        $html .= '<form action="?action=delete" method="post" style="display:inline">';
        $html .= ' <input type="hidden" name="id" value="' . $elem['id'] . '">';
        $html .= '<button type="submit"';
        if ($delete_confirm) {
            $html .= ' onclick="return confirm(\'' . I18n::t('Are you sure you want to delete this item?') . '\')"';
        }
        $html .= 'class="btn btn-danger">';
        $html .= '<span class="fa fa-trash"></span>';
        $html .= '</button>';
        $html .= '</form>';
        return $html;
    }

    public function select(
        array $l_object, string $name,
        string $id = null,
        string $value_field = 'id',
        string $name_field = 'name',
        string $label = '',
        string $class_select = 'form-control',
        string $class_option = '',
        bool $div = true,
        bool $show_label = true,
        string $div_class = 'mb-3',
        string $selected = null,
    ): string {
        $id = Sec::h($id);
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));
        $value_field = Sec::h($value_field);
        if (!$selected && $this->entity) {
            $selected = Sec::h($this->entity[$name]);
        }
        $name_field = Sec::h($name_field);
        $class_select = Sec::h($class_select);
        $class_option = Sec::h($class_option);

        if (!$id) {
            $id = $name;
        }

        $html = '';
        if ($div) {
            $html = '<div class="form-group ' . $div_class . '">';
        }
        if ($show_label) {
            $html .= '<label for="' . $id . '" class="form-label">' . $label . ' <span class="text-danger">*</span></label>';
        }
        $html .= "<select id=\"{$id}\" name=\"{$name}\" class=\"{$class_select}\">";
        foreach ($l_object as $object) {
            if (is_array($object)) {
                $options_values = Sec::h($object[$value_field]);
                $options_names = I18n::t($object[$name_field]);
            } else {
                $options_values = Sec::h($object->$value_field);
                $options_names = I18n::t($object->$name_field);
            }
            $html .= "<option value=\"{$options_values}\" class=\"{$class_option}\" ";
            if ($selected == $options_values) {
                $html .= ' selected ';
            }
            $html .= ">{$options_names}</option>";
        }
        $html .= "</select>";
        if ($div) {
            $html .= '</div>';
        }
        $html .= $this->validator_div($name);
        return $html;
    }

    public function select2Ajax(
        string $ajax_url,
        string $name,
        string $id = null,
        string $value_field = 'id',
        string $name_field = 'name',
        string $label = '',
        string $class_select = 'form-control',
        string $class_option = '',
        bool $div = true,
        bool $show_label = true,
        string $div_class = 'mb-3',
        string $selected = null,
        string $selectedLabel = null,
        int $minimumInputLength = 1,
        bool $required = false,
    ) {
        // Échapper les valeurs pour empêcher les injections XSS
        $id = Sec::h($id);
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));
        $value_field = Sec::h($value_field);
        if (!$selected && $this->entity) {
            $selected = Sec::h($this->entity[$name]);
        }
        $name_field = Sec::h($name_field);
        $class_select = Sec::h($class_select);
        $class_option = Sec::h($class_option);

        if (!$id) {
            $id = $name;
        }

        // Début de la construction du HTML
        $html = '';
        if ($div) {
            $html = '<div class="form-group ' . $div_class . '">';
        }

        if ($show_label) {
            $html .= '<label for="' . $id . '" class="form-label" >' . $label;
            if ($required) {
                $html .= ' <span class="text-danger">*</span> ';
            }
            $html .= '</label>';
        }

        // Construction de la balise <select>
        $html .= '<select id="' . Sec::hNoHtml($id) . '" name="' .  Sec::hNoHtml($name)  . '" class="' . $class_select . '" ';
        if ($required) {
            $html .= ' required ';
        }
        $html .= '>';

        // Ajout d'une option sélectionnée par défaut si nécessaire
        if (!empty($selected)) {
            $html .= '<option value="{' . Sec::hNoHtml($selected) . '}" selected>' . Sec::hNoHtml($selectedLabel) . '</option>';
        }

        // Fermeture de la balise <select>
        $html .= "</select>";

        if ($div) {
            $html .= '</div>';
        }

        // Génération du JS pour initialiser le Select2 avec AJAX
        $html .= "<script>
        $(document).ready(function() {
            $('#{$id}').select2({
                ajax: {
                    url: '{$ajax_url}',
                    dataType: 'json',
                    delay: 250,
                },
                minimumInputLength: $minimumInputLength // Nombre minimum de caractères pour déclencher la recherche
            });
        });
    </script>";

        // Ajouter la validation si nécessaire
        $html .= $this->validator_div($name);

        return $html;
    }

    public function select2(
        array $l_object, // Liste des objets à afficher dans le select
        string $name,
        string $id = null,
        string $value_field = 'id',
        string $name_field = 'name',
        string $label = '',
        string $class_select = 'form-control',
        string $class_option = '',
        bool $div = true,
        bool $show_label = true,
        string $div_class = 'mb-3',
        string $selected = null
    ) {
        // Échapper les valeurs & sécuriser
        $id = Sec::h($id);
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));
        $value_field = Sec::h($value_field);
        if (!$selected && $this->entity) {
            $selected = Sec::h($this->entity[$name]);
        }
        $name_field = Sec::h($name_field);
        $class_select = Sec::h($class_select);
        $class_option = Sec::h($class_option);

        if (!$id) {
            $id = $name;
        }

        // Début de la construction HTML
        $html = '';
        if ($div) {
            $html = '<div class="form-group ' . $div_class . '">';
        }

        // Ajout du label (si requis)
        if ($show_label) {
            $html .= '<label for="' . $id . '" class="form-label">' . $label . '</label>';
        }

        // Création de la balise <select>
        $html .= "<select id=\"{$id}\" name=\"{$name}\" class=\"{$class_select}\">";

        // Parcours de la liste pour générer les options
        foreach ($l_object as $object) {
            if (is_array($object)) {
                $options_values = Sec::h($object[$value_field]);
                $options_names = I18n::t($object[$name_field]);
            } else {
                $options_values = Sec::h($object->$value_field);
                $options_names = I18n::t($object->$name_field);
            }

            // Ajout des options
            $html .= "<option value=\"{$options_values}\" class=\"{$class_option}\" ";
            if ($selected == $options_values) {
                $html .= ' selected ';
            }
            $html .= ">{$options_names}</option>";
        }

        $html .= "</select>";

        // Fermeture de la div
        if ($div) {
            $html .= '</div>';
        }

        // Inclusion de la validation (si nécessaire)
        $html .= $this->validator_div($name);

        // Script JS pour initialiser Select2
        $html .= "<script>
        $(document).ready(function() {
            $('#{$id}').select2();
        });
    </script>";

        return $html;
    }

    /**
     * Generates an HTML input field with optional configurations such as labels, classes, placeholders, and div wrappers.
     *
     * @param string $name The name attribute of the input field. Also used for its ID if no ID is provided.
     * @param string $label The text for the associated label of the input field. Defaults to the name.
     * @param string|null $id A custom ID for the input field. If not provided, the name will be used.
     * @param string $type The type attribute of the input field (e.g., text, password). Defaults to 'text'.
     * @param string $placeholder The placeholder attribute for the input field. Defaults to an empty string.
     * @param string $class The CSS class applied to the input field. Defaults to 'form-control'.
     * @param mixed|string $value The value attribute for the input field. Defaults to an empty string unless entity values are available.
     * @param bool $required Determines if the input field is required. Adds a 'required' attribute if true. Defaults to false.
     * @param bool $autocomplete Toggles the autocomplete attribute. Adds `autocomplete="off"` or password-specific behavior when false. Defaults to false.
     * @param bool $div Whether or not the input field should be wrapped in a div. Defaults to true.
     * @param bool $show_label Determines if a label should be displayed with the input field. Defaults to true.
     * @param bool $row Adds a row class to the wrapping div if true. Defaults to true.
     * @param int $label_width The width of the label column when using a row layout. Defaults to 2.
     * @param int $input_width The width of the input field column when using a row layout. Defaults to 10.
     * @param string $div_class Additional CSS classes to be applied to the wrapping div. Defaults to 'mb-3'.
     * @param string $fa_icon Font Awesome icon class to prepend inside the input group. Defaults to an empty string.
     * @param bool $checked Determines whether the input is checked. For use with specific input types only. Defaults to false.
     * @param mixed|string $layout The layout type, with support for inline or break styles. Defaults to 'inline'.
     *
     * @return string The generated HTML for the input field.
     * @throws \Exception If the input type is 'checkbox', as a specific method should handle it.
     */
    public function input(
        string       $name,
        string       $label = '',
        string       $id = null,
        string       $type = 'text',
        string       $placeholder = '',
        string       $class = 'form-control',
        mixed        $value = '',
        bool         $required = false,
        bool         $autocomplete = false,
        bool         $div = true,
        bool         $show_label = true,
        bool         $row = true,
        int          $label_width = 2,
        int          $input_width = 10,
        string       $div_class = 'mb-3',
        string       $fa_icon = '',
        bool         $checked = false,
        string $layout = 'inline'
    ): string {
        $id = Sec::h($id);
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));
        $name = Sec::h($name);
        $type = Sec::h($type);
        $placeholder = Sec::h($placeholder);
        $class = Sec::h($class);
        $value = Sec::h($value);
        if($layout == 'break') {
            $label_width = 12;
            $input_width = 12;
        }
        if (!$value && $this->entity) {
            $value = Sec::h($this->entity[$name]);
        }
        $required = $required ? 'required' : '';
        $div_class = Sec::h($div_class);

        if ($type == 'checkbox') {
            throw new \Exception('Use checkbox method for checkbox type');
        }
        if (!$id) {
            $id = $name;
        }
        $html = '';
        if ($div) {
            $html .= '<div class="form-group ' . $div_class;
            if ($row) {
                $html .= ' row';
            }
            $html .= '">';

        }
        if ($show_label) {
            $html .= '<label for="' . $id . '" ' ;
            if ($row && $label_width) {
                $html .= ' class="col-sm-' . $label_width . ' col-form-label"';
            } else {
                $html .= '" class="form-label"';
            }
            $html .= '>' . $label;
            if($required) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</label>';

        }

        if($fa_icon) {
            $html .= '<div class="input-group';
            if ($row && $label_width) {
                $html .= ' col-sm-' . $input_width . '"';
            }
            $html .='">';
            $html .= '    <div class="input-group-prepend">';
            $html .= '        <span class="input-group-text"><i class="fa ' . $fa_icon . '"></i></span>';
            $html .= '    </div>';
        }

        $html .= '<input type="' . $type . '" id="' . $id . '" name="' . $name . '"';
        if ($row && $label_width && !$fa_icon) {
            $html .= ' class="col-sm-' . $input_width . ' ' . $class . '"';
        } else {
            $html .= ' class="' . $class . '"';
        }
        if ($placeholder) {
            $html .= ' placeholder="' . $placeholder . '"';
        }
        if ($required) {
            $html .= ' required';
        }
        if (!$autocomplete) {
            if ($type == 'password') {
                $html .= ' autocomplete="new-password" ';
            } else {
                $html .= ' autocomplete="off" ';
            }
        }
        if ($required) {
            $html .= ' required';
        }
        if ($value !== false) {
            $html .= ' value="' . $value . '"';
        }
        $html .= '>';
        if($fa_icon) {
            $html .= '</div>';
        }

        if ($div) {
            $html .= '</div>';
        }
        $html .= $this->validator_div($name);

        return $html;
    }

    public function date(
        string $name,
        string $label = '',
        string $id = null,
        string $placeholder = '',
        string $class = 'form-control',
               $value = '',
        bool $required = false,
        bool $autocomplete = false,
        bool $div = true,
        bool $show_label = true,
        bool $row = true,
        int $label_width = 2,
        int $input_width = 10,
        string $div_class = 'mb-3',
        string $fa_icon = ''
    ): string {
        $id = Sec::h($id);
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));
        $name = Sec::h($name);
        $type = 'date';
        $placeholder = Sec::h($placeholder);
        $class = Sec::h($class);
        $value = Sec::h($value);
        if (!$value && $this->entity) {
            $value = Sec::h($this->entity[$name]);
        }
        $required = $required ? 'required' : '';
        $div_class = Sec::h($div_class);

        if (!$id) {
            $id = $name;
        }
        $html = '';
        if ($div) {
            $html .= '<div class="form-group ' . $div_class;
            if ($row) {
                $html .= ' row';
            }
            $html .= '">';
        }
        if ($show_label) {
            $html .= '<label for="' . $id . '"';
            if ($row && $label_width) {
                $html .= ' class="col-sm-' . $label_width . ' col-form-label"';
            } else {
                $html .= ' class="form-label"';
            }
            $html .= '>' . $label;
            if ($required) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</label>';
        }
        if ($fa_icon) {
            $html .= '<div class="input-group';
            if ($row && $label_width) {
                $html .= ' col-sm-' . $input_width . '"';
            }
            $html .= '">';
            $html .= '    <div class="input-group-prepend">';
            $html .= '        <span class="input-group-text"><i class="fa ' . $fa_icon . '"></i></span>';
            $html .= '    </div>';
        }

        $html .= '<input type="' . $type . '" id="' . $id . '" name="' . $name . '"';
        if ($row && $label_width && !$fa_icon) {
            $html .= ' class="col-sm-' . $input_width . ' ' . $class . '"';
        } else {
            $html .= ' class="' . $class . '"';
        }
        if ($placeholder) {
            $html .= ' placeholder="' . $placeholder . '"';
        }
        if ($required) {
            $html .= ' required';
        }
        if (!$autocomplete) {
            $html .= ' autocomplete="off"';
        }
        if ($value !== false) {
            $html .= ' value="' . $value . '"';
        }
        $html .= '>';
        if ($fa_icon) {
            $html .= '</div>';
        }

        if ($div) {
            $html .= '</div>';
        }
        $html .= $this->validator_div($name);

        return $html;
    }

    public function checkbox(
        string $name,
        string $label = '',
        string $id = null,
        string $class = 'form-check-input',
        bool $checked = false,
        bool $required = false,
        bool $div = true,
        string $div_class = 'mb-3',
        bool $show_label = true,
    ): string {
        // Échappement des valeurs
        $id = Sec::h($id);
        $name = Sec::h($name);
        $class = Sec::h($class);
        $div_class = Sec::h($div_class);

        if($this->entity){
            if($this->entity[$name]){
                $checked = true;
            }else {
                $checked = false;
            }
        }

        // Gestion du label (si vide, prend le nom par défaut)
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));

        // Si l'ID n'est pas défini, on utilise le nom comme ID
        if (!$id) {
            $id = $name;
        }

        // Génération de l'HTML
        $html = '';
        if ($div) {
            $html .= '<div class="form-check ' . $div_class . '">';
        }

        // Ajout du champ checkbox
        $html .= '<input type="checkbox" id="' . $id . '" name="' . $name . '" class="' . $class . '"';
        if ($checked) {
            $html .= ' checked';
        }
        if ($required) {
            $html .= ' required';
        }
        $html .= '>';

        // Ajout d'un label si demandé
        if ($show_label) {
            $html .= '<label class="form-check-label" for="' . $id . '">' . $label . '</label>';
        }

        if ($div) {
            $html .= '</div>';
        }

        // Gestion des erreurs via le validator
        $html .= $this->validator_div($name);

        return $html;
    }

    public function textarea(
        string $name,
        string $label = '',
        string $id = null,
        string $placeholder = '',
        string $class = 'form-control',
               $value = '',
        bool $required = false,
        bool $autocomplete = false,
        bool $div = true,
        bool $show_label = true,
        int $rows = 5,
        int $cols = 50,
        string $div_class = 'mb-3'
    ): string {
        $id = Sec::h($id);
        if (!$label) {
            $label = $name;
        }
        $label = ucfirst(I18n::t($label));
        $name = Sec::h($name);
        $placeholder = Sec::h($placeholder);
        $class = Sec::h($class);
        $value = Sec::h($value);
        if (!$value && $this->entity) {
            $value = Sec::h($this->entity[$name]);
        }
        $required = $required ? 'required' : '';
        $div_class = Sec::h($div_class);

        if (!$id) {
            $id = $name;
        }

        $html = '';
        if ($div) {
            $html .= '<div class="form-group ' . $div_class . '">';
        }

        if ($show_label) {
            $html .= '<label for="' . $id . '" class="form-label">' . $label;
            if($required) {
                $html .= ' <span class="text-danger">*</span>';
            }
            $html .= '</label>';
        }

        $html .= '<textarea id="' . $id . '" name="' . $name . '" class="' . $class . '"';
        if ($placeholder) {
            $html .= ' placeholder="' . $placeholder . '"';
        }
        if ($required) {
            $html .= ' required';
        }
        if (!$autocomplete) {
            $html .= ' autocomplete="off"';
        }
        $html .= ' rows="' . $rows . '" cols="' . $cols . '">';
        $html .= $value;
        $html .='</textarea>';

        if($div) {
            $html .= '</div>';
        }

        return $html;
    }

    public function submit(
        string $value,
        string $class = 'form-control btn btn-primary',
        bool $div = true,
        string $div_class = 'mb-3'
    ): string
    {
        $value = I18n::t($value);
        $class = Sec::h($class);
        $div_class = Sec::h($div_class);

        $html = '';
        if ($div) {
            $html .= '<div class="' . $div_class . '">';
        }

        if($this->ajax){
            $html .= '<a href="javascript:void(0)" onclick="submitAjax' . $this->unique_id .'()" type="submit" class=" ajax_submit ' . $class . '">' . $value . '</a>';
        } else {
            $html .= '<button type="submit" class="' . $class . '">' . $value . '</button>';
        }

        if ($div) {
            $html .= '</div>';
        }

        return $html;
    }


    private function validator_div(string $name): string
    {
        if (!$this->validator) {
            return '';
        }
        $html = '';
        if ($this->validator->hasError($name)) {
            $html .= '<div class="text-danger p-0 m-0">';
            foreach ($this->validator->popError($name) as $error) {
                $html .= '<p>' . I18n::t($error) . '</p>';
            }
            $html .= '</div>';
        }
        return $html;
    }

    public function formStart(
        string $method = 'post',
        bool $multipart = false,
        bool $autocomplete=true,
        bool $error_summary = true,
    ): string
    {
        $action = Sec::h($this->action);
        $method = Sec::h($method);

        $html = '';
        $html .= '<form action="' . $this->action . '" method="' . $method . '"';
        $html .= ' id="' . $this->unique_id . '"';
        if($this->ajax){
            $html .= ' data-ajax="true"';
        }
        if ($multipart) {
            $html .= ' enctype="multipart/form-data"';
        }
        if (!$autocomplete) {
            $html .= ' autocomplete="off"';
        }
        $html .= '>';
        if ($error_summary) {
            $html .= $this->errorSummary();
        }

        // si c'est un model et qu'il a un attr id on l'ajoute automatiquement
        if($this->entity && $this->entity['id']){
            $html .= '<input type="hidden" name="id" value="' . $this->entity['id'] . '">';
        }

        return $html;
    }


    public function formEnd(bool $div = true, bool $error_summary = true): string
    {
        $html = '';
        if ($error_summary) {
            $html .= $this->errorSummary();
        }
        $html .= '</form>';

        if ($div) {
            $html .= '</div>';
        }

        return $html;
    }

    public function ajaxSubmit($fn_succcess_name = null, $fn_error_name = null): string
    {
        $html = '<script nonce="' . Sec::noneCsp() . '">'. "\r";
        $html .= 'function submitAjax' . $this->unique_id . '() {' . "\r";
        $html .= 'let form = document.getElementById("' . $this->unique_id . '");'. "\r";
        $html .= 'let formData = new FormData(form);'. "\r";
        $html .= 'let formDataDict = Object.fromEntries(formData.entries());'. "\r";
        $html .= 'api.post("'. $this->action .'", formDataDict, {' . "\r";
        $html .='loaderMessage: "Traitement en cours...",'  . "\r";
        if($fn_succcess_name){
            $html .= "success: $fn_succcess_name"  . "\r";
        }
        if($fn_error_name){
            $html .= "error: $fn_succcess_name"  . "\r";
        }
        $html .=        '})'  . "\r";
        $html .= '}'  . "\r";
        $html .= '</script>'  . "\r";
        return $html;
    }

    public function errorSummary(): string
    {
        $html = '';
        if ($this->validator && $this->validator->getErrors()) {
            $html .= '<div class="text-danger">';
            $html .= '<p>' . I18n::t('Please correct the following errors:') . '</p>';
            $html .= '<ul>';
            foreach ($this->validator->getErrors() as $field => $errors) {
                foreach ($errors as $error) {
                    $html .= '<li>' . I18n::t($error) . '</li>';
                }
            }
            $html .= '</ul>';
            $html .= '</div>';
        }
        return $html;
    }

    public static function searchFormRow($searchText = null): string
    {
        $placeholder = I18n::t('Search');
        $searchText = Sec::h($searchText) ?? '';
        $active = '';
        if($searchText) {
            $active = 'is-valid';
        }
        return  <<<HTML
                <div class='row'>
                    <div class='col-md-11'>
                        <input type="text" class="form-control $active" name="search_text"
                               id="inputSuccess" placeholder="$placeholder"
                                    value="$searchText"
                        >
                    </div>
                    <div class='col-md-1'>
                        <button type="submit" class="form-control btn btn-default" name="search"
                                value="search"
                        >
                            <i class="fas fa-search"></i>
                        </button>
                    </div>
                </div>
                HTML;


    }

    /**
     * Utilitaire pour générer l'attribut value d'un input HTML
     * Si la clef n'existe pas dans le tableau, retourne value=''
     * Si la clef existe mais est null ou '' retourne value=''
     * Pour les types supportés (int, float, string, bool) retourne value='Sec::hNoHtml($value)'
     *
     * Sécurisé contre XSS
     *
     * @param array $entity
     * @param string $key
     * @param bool $safe Si true, la valeur est considérée comme déjà sécurisée
     * @return string
     * @throws \Exception Si le type n'est pas supporté
     */
    public static function valueAttrOrBlank(?array $entity, string $key, bool $safe = false): string
    {
        if(empty($entity) || !is_array($entity)) {
            return '';
        }
        if (!array_key_exists($key, $entity)) {
            return '';
        }

        $value = $entity[$key];

        if ($value === null || $value === '') {
            return "value=''";
        }

        if (!is_scalar($value)) {
            throw new \Exception('This type of var is not supported by valueAttrOrBlank, use scalar');
        }
        if ($safe) {
            return "value='" . $value . "'";
        }

        return "value='" . Sec::hNoHtml($value) . "'";
    }

    public function errorDiv(string $fieldName): string
    {
        $errors = $this->validator->getError($fieldName);
        if (empty($errors)) {
            return '';
        }

        $html = '';
        foreach ($errors as $err) {
            $html .= '<div class="text-danger">* ' . I18n::t($err) . '</div>';
        }
        return $html;
    }
}