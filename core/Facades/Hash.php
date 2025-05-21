<?php

namespace Core\Facades;

class Hash {
    /**
     * Hashes a password so it can be stored securely.
     * Uses BCRYPT, which is currently considered to be the most secure.
     * @param string $password the password to hash
     * @return string the hashed password
     */
    public static function password_create($password) {
        return password_hash($password, PASSWORD_BCRYPT);
    }

    /**
     * Checks a given password against a hashed password to see if they match.
     * @param string $password the password to check
     * @param string $hashed the hashed password to check against
     * @return bool true if the password matches the hashed password, false otherwise
     */
    public static function password_check($password, $hashed) {
        return password_verify($password, $hashed);
    }
}
