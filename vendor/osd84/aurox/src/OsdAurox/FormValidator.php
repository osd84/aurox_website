<?php

namespace OsdAurox;
use OsdAurox\Api;
use Respect\Validation\Exceptions\NestedValidationException;

class FormValidator
{
    // Propriété pour stocker les messages d'erreur
    private array $errors = [];
    private ?bool $is_valid = null;
    public ?Api $o_api_response = null;

    public function isValid()
    {
        if($this->is_valid === null) {
            throw new \Exception("You must call validate() method before calling isValid()");
        }

        if(count($this->errors) > 0) {
            return false;
        }
        return true;
    }

    /**
     * Ajoute un message pour un champ.
     *
     * @param string $field Le nom du champ.
     * @param string $message Le message d'erreur.
     */
    public function addError(string $field, string $message): void
    {
        $this->errors[$field][] = $message;
    }

    /**
     * Vérifie si un champ a des erreurs.
     *
     * @param string $field Le nom du champ.
     * @return bool True si le champ a des erreurs.
     */
    public function hasError(string $field): bool
    {
        return isset($this->errors[$field]) && !empty($this->errors[$field]);
    }

    /**
     * Récupère les erreurs pour un champ.
     *
     * @param string $field Le nom du champ.
     * @return array Un tableau des erreurs pour ce champ.
     */
    public function getError(string $field): array
    {
        return $this->errors[$field] ?? [];
    }

    public function popError(string $field): array
    {
        $out = $this->errors[$field];
        unset($this->errors[$field]);
        return $out;
    }

    /**
     * Récupère toutes les erreurs.
     *
     * @return array Un tableau avec toutes les erreurs.
     */
    public function getErrors(): array
    {
        return $this->errors;
    }

    public function clearErrors(): void
    {
        $this->errors = [];
    }

    public function genApiResult(): ?Api
    {
        $o_api_response = new Api();
        $o_api_response->status = $this->isValid();
        if(!$this->isValid()) {
            $o_api_response->errors[] = I18n::t('Please correct the following errors');
        }
        foreach ($this->errors as $field => $messages) {
            foreach ($messages as $message) {
                $o_api_response->addValidatorField($field, $message);
            }
        }
        $this->o_api_response = $o_api_response;
        return $o_api_response;
    }



    // je veux valider un tableau de données avec OsdAurox\Validator
    public function validate(array $data, array $rules)
    {
        $this->is_valid = true;
        if(count($this->errors) > 0) {
            $this->is_valid = false;
        }
        $alreadyRaised = [];
        foreach ($rules as $field => $rule) {
                if($field != $rule->field) {
                    throw new \Exception("Field name in rule and data must be the same");
                }
                $field_val = $data[$rule->field] ?? null;
                $errors = $rule->validate($field_val);
                foreach ($errors as $error) {
                    $flag = trim($rule->field . '-' . $error['msg']);
                    if (!in_array($flag, $alreadyRaised)) {
                        $this->addError($rule->field, $error['msg']);
                        $alreadyRaised[] = $flag;
                    }
                }
        }
        return $this->isValid();
    }

}