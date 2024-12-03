<?php

class ValidationMiddleware {
    private $rules;

    public function __construct($rules = []) {
        $this->rules = $rules;
    }

    public function validate($data) {
        $errors = [];

        foreach ($this->rules as $field => $rule) {
            // Vérification si le champ est requis
            if (isset($rule['required']) && $rule['required']) {
                if (!isset($data[$field]) || empty($data[$field])) {
                    $errors[$field][] = "Le champ $field est requis";
                    continue;
                }
            }

            // Si le champ n'existe pas et n'est pas requis, on passe
            if (!isset($data[$field])) {
                continue;
            }

            // Validation du type
            if (isset($rule['type'])) {
                switch ($rule['type']) {
                    case 'email':
                        if (!filter_var($data[$field], FILTER_VALIDATE_EMAIL)) {
                            $errors[$field][] = "Le format de l'email est invalide";
                        }
                        break;

                    case 'numeric':
                        if (!is_numeric($data[$field])) {
                            $errors[$field][] = "Le champ doit être numérique";
                        }
                        break;

                    case 'string':
                        if (!is_string($data[$field])) {
                            $errors[$field][] = "Le champ doit être une chaîne de caractères";
                        }
                        break;
                }
            }

            // Validation de la longueur
            if (isset($rule['min']) && strlen($data[$field]) < $rule['min']) {
                $errors[$field][] = "Le champ doit contenir au moins {$rule['min']} caractères";
            }

            if (isset($rule['max']) && strlen($data[$field]) > $rule['max']) {
                $errors[$field][] = "Le champ doit contenir au maximum {$rule['max']} caractères";
            }

            // Validation des expressions régulières
            if (isset($rule['pattern']) && !preg_match($rule['pattern'], $data[$field])) {
                $errors[$field][] = "Le format du champ est invalide";
            }
        }

        return $errors;
    }

    public function __invoke($request, $response, $next) {
        $data = $request->getParsedBody();
        $errors = $this->validate($data);

        if (!empty($errors)) {
            return $response->withJson([
                'status' => 'error',
                'message' => 'Erreurs de validation',
                'errors' => $errors
            ], 422);
        }

        return $next($request, $response);
    }
}
