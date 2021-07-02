<?php
require_once 'utilities/Exceptions.php';

class Database
{
    private static string $host = 'localhost';
    private static string $user = 'root';
    private static string $pass = 'root';
    private static string $name = 'demo_project';

    private static ?Database $singleton = null;

    private ?PDO $connection;

    /**
     * @throws PDOException
     */
    private function __construct()
    {
        $options = [
            PDO::ATTR_PERSISTENT => true,
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
        ];
        $this->connection = new PDO(
            'mysql:host=' . self::$host . ';dbname=' . self::$name,
            self::$user,
            self::$pass,
            $options
        );

        $this->connection->exec('SET CHARACTER SET UTF8');
    }

    public static function getInstance(): Database
    {
        return self::$singleton;
    }

    /**
     * @throws SingletonException
     */
    public static function createInstance(): void
    {
        if (self::$singleton) {
            throw new SingletonException('Database');
        }
        self::$singleton = new Database();
    }

    public static function destroyInstance(): void
    {
        if (self::$singleton) {
            self::$singleton = null;
        }
    }

    /**
     * @throws SqlPDOException
     */
    public function query(string $sql_query, array $variables = []): ?PDOStatement
    {
        try {
            $statement = $this->connection->prepare($sql_query);
            assert($statement instanceof PDOStatement);
            $statement->execute($variables);
            return $statement->rowCount() ? $statement : null;
        } catch (PDOException $e) {
            throw new SqlPDOException($sql_query, $variables, $e);
        }
    }

    public function begin(): void
    {
        $this->connection->beginTransaction();
    }

    public function commit(): void
    {
        $this->connection->commit();
    }

    public function rollback(): void
    {
        $this->connection->rollBack();
    }

    public function __destruct()
    {
        unset($this->connection);
        $this->connection = null;
    }
}