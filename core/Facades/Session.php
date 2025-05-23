<?php
namespace Core\Facades;

class Session
{
    protected bool $started = false;

/**
 * Starts a new session if none exists.
 *
 * Checks the current session status and initiates a session
 * if it is not already active. Sets the $started property to true
 * once the session is successfully started.
 *
 * @return void
 */

/**
 * Initializes a session if none is active.
 *
 * Checks the current session status, and starts a new session 
 * if no session is currently active. Sets the $started property 
 * to true after successfully starting the session.
 *
 * @return void
 */

    public function start(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
            $this->started = true;
        }
    }

/**
 * Retrieves a value from the session by key.
 *
 * Starts the session if it is not already active. Returns the value 
 * associated with the given key from the session, or the default 
 * value if the key does not exist.
 *
 * @param string $key The key to retrieve from the session.
 * @param mixed $default The default value to return if the key is not found.
 * @return mixed The value from the session or the default value.
 */

    public function get(string $key, mixed $default = null): mixed
    {
        $this->start();
        return $_SESSION[$key] ?? $default;
    }

/**
 * Stores a value in the session under the specified key.
 *
 * Starts the session if it is not already active. Associates the 
 * given value with the specified key in the session.
 *
 * @param string $key The key under which the value will be stored.
 * @param mixed $value The value to store in the session.
 * @return void
 */

    public function set(string $key, mixed $value): void
    {
        $this->start();
        $_SESSION[$key] = $value;
    }

    /**
     * Deletes a value from the session by key.
     *
     * Starts the session if it is not already active. Removes the 
     * value associated with the given key from the session.
     *
     * @param string $key The key to delete from the session.
     * @return void
     */
    public function forget(string $key): void
    {
        $this->start();
        unset($_SESSION[$key]);
    }

    /**
     * Checks if a given key exists in the session.
     *
     * Starts the session if it is not already active. Checks if the given key
     * exists in the session.
     *
     * @param string $key The key to check for in the session.
     * @return bool True if the key is found, false otherwise.
     */
    public function has(string $key): bool
    {
        $this->start();
        return isset($_SESSION[$key]);
    }

    /**
     * Retrieves all values from the session.
     *
     * Starts the session if it is not already active. Returns all values stored in the session.
     *
     * @return array All values stored in the session.
     */
    public function all(): array
    {
        $this->start();
        return $_SESSION;
    }

    /**
     * Destroys the current session.
     *
     * If the session has been started before, or if a session is currently active, this method
     * will destroy the session and reset the session data to an empty array.
     *
     * @return void
     */
    public function flush(): void
    {
        if ($this->started || session_status() === PHP_SESSION_ACTIVE) {
            session_destroy();
            $_SESSION = [];
        }
    }
}
