<?php


namespace OsdAurox;


use DateTime;

class Validator
{

    private array $rules = [];
    public string $field = '';
    public bool $optional = false;

    // crée un nouveau Validateur
    public static function create($field): Validator
    {
        $validator = new Validator();
        $validator->field = Sec::hNoHtml($field);
        return $validator;
    }

    public function optional(): Validator
    {
        $this->optional = true;
        return $this;
    }


    public function validate($input)
    {
        $errors = [];
        foreach ($this->rules as $rule) {
            $resultRule = $rule($input);
            if ($resultRule['valid'] === false) {

                if($this->optional && empty($input)) {
                    continue;
                } else {
                    $errors[] = [
                        'field' => $this->field,
                        'valid' => $resultRule['valid'],
                        'msg' => $resultRule['msg']
                    ];
                }
            }
        }
        return $errors;
    }


    public function isValid($input)
    {
        foreach ($this->rules as $rule) {
            $resultRule = $rule($input);
            if ($resultRule['valid'] === false) {
                return false;
            }
        }
        return true;
    }


    public function email(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must be valid email');
            $valid = true;

            if (!is_string($input)) {
                $valid = false;
            }

            if ($valid) {
                $valid = filter_var($input, FILTER_VALIDATE_EMAIL);
            }

            return ['valid' => $valid,
                'msg' => $msg ?? ''];

        };

        return $this;
    }


    public function notEmpty(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must not be empty');

            if (is_string($input)) {
                $input = trim($input);
            }

            $valid = !empty($input);

            return ['valid' => $valid,
                'msg' => $msg ?? ''];

        };

        return $this;

    }

    public function required(): Validator
    {
        $this->optional = false;

        $this->rules[] = function ($input) {
            $msg = I18n::t('field is required');

            // Vérifie si la valeur est null ou undefined
            if ($input === null) {
                return ['valid' => false, 'msg' => $msg];
            }

            // Pour les chaînes de caractères
            if (is_string($input)) {
                return [
                    'valid' => trim($input) !== '',
                    'msg' => $msg
                ];
            }

            // Pour les tableaux
            if (is_array($input)) {
                return [
                    'valid' => count($input) > 0,
                    'msg' => $msg
                ];
            }

            // Pour les nombres
            if (is_numeric($input)) {
                return [
                    'valid' => true,
                    'msg' => $msg
                ];
            }

            // Pour les booléens
            if (is_bool($input)) {
                return [
                    'valid' => true,
                    'msg' => $msg
                ];
            }

            // Pour tous les autres types
            return [
                'valid' => !empty($input),
                'msg' => $msg
            ];
        };

        return $this;
    }


    public function length(?int $min = null, ?int $max = null): Validator
    {
        $this->rules[] = function ($input) use ($min, $max) {

            $msg = '';
            $valid = false;
            $inputLength = null;
            $minPass = false;
            $maxPass = false;

            // recherche de la taille
            if (is_string($input)) {
                $inputLength = (int)mb_strlen($input);
            }

            if (is_array($input)) {
                $inputLength = count($input);
            }

            if (is_object($input)) {
                return count(get_object_vars($input));
            }

            if (is_int($input)) {
                return mb_strlen((string)$input);
            }

            // validation
            if ($inputLength !== null) {

                // verification du min
                if ($min === null) {
                    $minPass = true;
                } else {
                    $minPass = $inputLength >= $min;
                }

                // verification du max
                if ($max === null) {
                    $maxPass = true;
                } else {
                    $maxPass = $inputLength <= $max;
                }

                $valid = $minPass && $maxPass;
            }

            if (!$valid) {
                if($min && $max) {
                    $msg = I18n::t('must be between {min} and {max} characters', ['min' => $min, 'max' => $max]);
                } elseif ($min) {
                    $msg = I18n::t('must be at least {min} characters', ['min' => $min]);
                } else {
                    $msg = I18n::t('must be at most {max} characters', ['max' => $max]);
                }
            }
            return ['valid' => $valid,
                'msg' => $msg ?? ''];
        };

        return $this;
    }

    public function stringType(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must be a string');

            // Vérifie si la valeur est de type string
            $valid = is_string($input);

            return [
                'valid' => $valid,
                'msg' => $msg
            ];
        };

        return $this;
    }

    public function intType(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must be a int');

            // Vérifie si la valeur est de type string
            $valid = is_int($input);

            return [
                'valid' => $valid,
                'msg' => $msg
            ];
        };

        return $this;
    }

    public function floatType(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = I18n::t('must be a float');

            // Vérifie si la valeur est de type string
            $valid = is_float($input);

            return [
                'valid' => $valid,
                'msg' => $msg
            ];
        };

        return $this;
    }

    public function min(int|float $minimum): Validator
    {
        $this->rules[] = function ($input) use ($minimum) {
            $msg = I18n::t('must be greater than or equal to %s', [$minimum]);

            // Convertir les chaînes numériques en nombres
            if (is_string($input) && is_numeric($input)) {
                if (str_contains($input, '.')) {
                    $input = (float)$input;
                } else {
                    $input = (int)$input;
                }
            }

            // Vérifier si c'est un nombre
            if (!is_numeric($input)) {
                return [
                    'valid' => false,
                    'msg' => I18n::t('must be a number')
                ];
            }

            return [
                'valid' => $input >= $minimum,
                'msg' => $msg
            ];
        };

        return $this;
    }


    public function max(int|float $maximum): Validator
    {
        $this->rules[] = function ($input) use ($maximum) {
            $msg = I18n::t('must be less than or equal to %s', [$maximum]);

            // Convertir les chaînes numériques en nombres
            if (is_string($input) && is_numeric($input)) {
                if (str_contains($input, '.')) {
                    $input = (float)$input;
                } else {
                    $input = (int)$input;
                }
            }

            // Vérifier si c'est un nombre
            if (!is_numeric($input)) {
                return [
                    'valid' => false,
                    'msg' => I18n::t('must be a number')
                ];
            }

            return [
                'valid' => $input <= $maximum,
                'msg' => $msg
            ];
        };

        return $this;
    }


    public function startWith(string $prefix, bool $caseSensitive = true): Validator
    {
        $this->rules[] = function ($input) use ($prefix, $caseSensitive) {
            $msg = I18n::t('must start with "%s"', [$prefix]);

            if (!is_string($input)) {
                return [
                    'valid' => false,
                    'msg' => I18n::t('must be a string')
                ];
            }

            if ($caseSensitive) {
                $valid = str_starts_with($input, $prefix);
            } else {
                $valid = str_starts_with(strtolower($input), strtolower($prefix));
            }

            return [
                'valid' => $valid,
                'msg' => $msg
            ];
        };

        return $this;
    }

    public function positive(): Validator
    {
        $this->rules[] = function ($input) {
            $msg = '';

            // Convertir les chaînes numériques en nombres
            if (is_string($input) && is_numeric($input)) {
                if (str_contains($input, '.')) {
                    $input = (float)$input;
                } else {
                    $input = (int)$input;
                }
            }

            // Vérifier si c'est un nombre
            if (!is_numeric($input)) {
                return [
                    'valid' => false,
                    'msg' => I18n::t('must be a positive number')
                ];
            }

            return [
                'valid' => $input > 0,
                'msg' => I18n::t('must be a positive number')
            ];
        };

        return $this;
    }

    public function date(string $format = 'Y-m-d'): Validator
    {
        $this->rules[] = function ($input) use ($format) {
            $msg = '';

            if (!is_string($input)) {
                return [
                    'valid' => false,
                    'msg' => I18n::t('must be a string date')
                ];
            }

            // Essayer de créer un objet DateTime
            $dateTime = DateTime::createFromFormat($format, $input);

            // Vérifier si la date est valide et si les erreurs de parsing sont présentes
            if(!$dateTime) {
                $valid = false;
            } else {
                $valid = true;
            }
            // Vérifier si la date correspond exactement au format attendu
            if ($valid) {
                $valid = $dateTime->format($format) === $input;
            }

            return [
                'valid' => $valid,
                'msg' => I18n::t('must be a valid date in format: %s', [$format])
            ];
        };

        return $this;
    }

    public function dateTime(string $format = 'Y-m-d H:i:s'): Validator
    {
        $this->rules[] = function ($input) use ($format) {
            $msg = '';

            if (!is_string($input)) {
                return [
                    'valid' => false,
                    'msg' => $msg
                ];
            }

            // Essayer de créer un objet DateTime
            $dateTime = DateTime::createFromFormat($format, $input);

            // Vérifier si la date est valide et si les erreurs de parsing sont présentes
            if(!$dateTime) {
                $valid = false;
            } else {
                $valid = true;
            }

            // Vérifier si la date correspond exactement au format attendu
            if ($valid) {
                $valid = $dateTime->format($format) === $input;
            }

            return [
                'valid' => $valid,
                'msg' => I18n::t('must be a valid datetime in format: %s', [$format])
            ];
        };

        return $this;
    }


    /**
     * Vérifie si la valeur est présente dans un tableau donné
     *
     * @param array $allowedValues Tableau des valeurs autorisées
     * @param bool $strict Utiliser une comparaison stricte (===)
     * @return Validator
     */
    public function inArray(array $allowedValues, bool $strict = false): Validator
    {
        $this->rules[] = function ($input) use ($allowedValues, $strict) {
            $valid = in_array($input, $allowedValues, $strict);

            // Crée un message lisible avec les valeurs autorisées
            $valuesString = implode(', ', array_map(function ($value) {
                if (is_null($value)) return 'null';
                if (is_bool($value)) return $value ? 'true' : 'false';
                return (string)$value;
            }, $allowedValues));

            return [
                'valid' => $valid,
                'msg' => I18n::t("must be one of the following values : $valuesString")
            ];
        };

        return $this;
    }



}