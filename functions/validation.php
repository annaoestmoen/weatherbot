<?php
/**
 * Funksjoner for sanitizing og validering av input.
 *
 * @package Validation
 */

 // ===============================
 // SANITISERING (XSS-beskyttelse)
 // ===============================
/**
 * Rydder opp input-strenger for å beskytte mot XSS.
 *
 * @param string $input
 * @return string Sanitized streng
 */
function sanitizeString(string $input): string {
    $input = trim($input);
    $input = strip_tags($input); 
    $input = htmlspecialchars($input, ENT_QUOTES, 'UTF-8'); 
    return $input;
}

 // ===============================
 // CHAT-MELDING VALIDERING
 // ===============================
/**
 * Validerer chatmeldinger.
 *
 * @param string $message
 * @param int $maxLength Maksimal lengde
 * @return bool True hvis gyldig, false ellers
 */
function validateChatMessage(string $message, int $maxLength = 500): bool {
    $sanitized = sanitizeString($message);

    if (empty($sanitized)) return false;
    if (strlen($sanitized) > $maxLength) return false;
    if ($sanitized !== $message) return false;

    return true;
}

// ===============================
 // EPOST VALIDERING
 // ===============================
/**
 * Validerer epostadresse.
 *
 * @param string $email
 * @return bool True hvis gyldig epost
 */
function validateEmail(string $email): bool {
    $email = filter_var(trim($email), FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

 // ===============================
 // PASSORD VALIDERING
 // ===============================
/**
 * Validerer passordlengde.
 *
 * @param string $password
 * @param int $minLength
 * @return bool True hvis passord er langt nok
 */
function validatePassword(string $password, int $minLength = 8): bool {
    return strlen($password) >= $minLength;
}

 // ===============================
 // SANITIZE + VALIDATE LOGIN / REGISTRERING
 // ===============================
/**
 * Sanitizerer og validerer login-input.
 *
 * @param string $email
 * @param string $password
 * @return array ['success' => bool, 'error' => string|null, 'email' => string, 'password' => string]
 */
function sanitizeAndValidateLogin(string $email, string $password): array {
    $email = sanitizeString($email);
    $password = sanitizeString($password);

    if (!validateEmail($email)) {
        return ['success' => false, 'error' => 'Ugyldig epostadresse.'];
    }

    if (!validatePassword($password)) {
        return ['success' => false, 'error' => "Passordet må være minst 8 tegn."];
    }

    return ['success' => true, 'email' => $email, 'password' => $password];
}

 // ===============================
 // SANITIZE + VALIDATE REGISTRERING
 // ===============================
/**
 * Sanitizerer og validerer registreringsinput.
 *
 * @param string $email
 * @param string $password
 * @param string $password2
 * @return array ['success' => bool, 'error' => string|null, 'email' => string, 'password' => string]
 */
function sanitizeAndValidateRegistration(string $email, string $password, string $password2): array {
    $email = sanitizeString($email);
    $password = sanitizeString($password);
    $password2 = sanitizeString($password2);

    if (!validateEmail($email)) {
        return ['success' => false, 'error' => 'Ugyldig epostadresse.'];
    }

    if (!validatePassword($password)) {
        return ['success' => false, 'error' => "Passordet må være minst 8 tegn."];
    }

    if ($password !== $password2) {
        return ['success' => false, 'error' => "Passordene matcher ikke."];
    }

    return ['success' => true, 'email' => $email, 'password' => $password];
}
