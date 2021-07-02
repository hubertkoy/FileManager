<?php
require_once 'utilities/Exceptions.php';

/**
 * Class Session
 * start
 * save
 * remove
 * exists
 * logged
 * get instance
 */
class Session implements ArrayAccess
{
    private static ?Session $singleton = null;
    private bool $valid = false;

    /**
     * @throws SessionException
     */
    public function __construct()
    {
        $cookie_options = [
            'lifetime' => time() + 86400,
            #TODO add domain to config
            'domain' => '.site.local',
            'path' => '/',
            'secure' => false,
            'samesite' => 'Lax'
        ];
        session_set_cookie_params($cookie_options);
        $result = session_start();
        if ($result) {
            $this->valid = true;
        } else {
            throw new SessionException();
        }
    }

    /**
     * @throws SingletonException
     */
    public static function createInstance(): void
    {
        if (self::$singleton) {
            throw new SingletonException('Session');
        }
        self::$singleton = new Session();
    }

    public static function destroyInstance(): void
    {
        if (self::$singleton) {
            self::$singleton = null;
        }
    }

    public static function getInstance(): Session
    {
        return self::$singleton;
    }

    public function destroy(): void
    {
        if ($this->valid) {
            $this->valid = false;
        } else {
            return;
        }
        session_destroy();
    }

    public function isValid(): bool
    {
        return $this->valid;
    }

    public function isAuthorized(): bool
    {
        if (!$this->valid) {
            return false;
        }
        if (isset($this['id'])) {
            $id = $this['id'];
            assert($id > 0);
            return true;
        } else {
            return false;
        }
    }

    public function offsetSet($offset, $value): void
    {
        assert($this->valid);
        if (is_null($offset)) {
            $_SESSION[] = $value;
        } else {
            $_SESSION[$offset] = $value;
        }
    }

    public function offsetExists($offset): bool
    {
        assert($this->valid);
        return isset($_SESSION[$offset]);
    }

    public function offsetUnset($offset): void
    {
        assert($this->valid);
        unset($_SESSION[$offset]);
    }

    public function offsetGet($offset): mixed
    {
        assert($this->valid);
        return $_SESSION[$offset] ?? null;
    }

    /**
     * @throws SessionException
     */
    public function __destruct()
    {
        if (!$this->valid) {
            return;
        }
        $result = session_commit();
        if (!$result) {
            throw new SessionException();
        }
    }
}