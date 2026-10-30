<?php

//Incluimos una única vez el archivo de configuración.
require_once __DIR__ . '/../config/config.php';

class Connection
{
    private ?mysqli $connection = null;

    public function connect(): mysqli
    {
        if ($this->connection instanceof mysqli) {
            return $this->connection;
        }

        global $config;

        $host = getenv('DB_HOST') ?: $config['host'];
        $port = (int) (getenv('DB_PORT') ?: $config['port']);
        $database = getenv('DB_NAME') ?: $config['database'];
        $charset = getenv('DB_CHARSET') ?: $config['charset'];
        $user = getenv('DB_USER') ?: $config['user'];
        $password = getenv('DB_PASSWORD');
        if ($password === false) {
            $password = $config['password'];
        }
        $sslEnabled = filter_var(getenv('DB_SSL') ?: 'false', FILTER_VALIDATE_BOOLEAN);
        $caFile = getenv('DB_SSL_CA') ?: '';

        if ($sslEnabled && $caFile === '') {
            throw new RuntimeException('Configura DB_SSL_CA cuando DB_SSL está habilitado');
        }

        $connection = mysqli_init();
        if (!$connection) {
            throw new RuntimeException('No se pudo inicializar MySQLi');
        }

        if ($caFile !== '') {
            if (!is_readable($caFile)) {
                throw new RuntimeException('El certificado DB_SSL_CA no se puede leer');
            }
            mysqli_options($connection, MYSQLI_OPT_SSL_VERIFY_SERVER_CERT, true);
            mysqli_ssl_set($connection, null, null, $caFile, null, null);
            $sslEnabled = true;
        }

        $flags = $sslEnabled ? MYSQLI_CLIENT_SSL : 0;
        if (!mysqli_real_connect($connection, $host, $user, $password, $database, $port, null, $flags)) {
            throw new RuntimeException('No se pudo conectar con MySQL');
        }

        if (!mysqli_set_charset($connection, $charset)) {
            mysqli_close($connection);
            throw new RuntimeException('No se pudo configurar el charset de MySQL');
        }

        $this->connection = $connection;

        return $this->connection;
    }

    public function query(string $query, string $types = '', array $params = [])
    {
        $statement = null;

        try {
            $connection = $this->connect();
            $statement = mysqli_prepare($connection, $query);
            if ($statement === false) {
                error_log(sprintf(
                    'MySQL prepare failed (%d, SQLSTATE %s): %s',
                    $connection->errno,
                    $connection->sqlstate,
                    $connection->error
                ));
                return false;
            }

            if ($params !== []) {
                $bindParams = [$types];
                foreach ($params as $index => $value) {
                    $params[$index] = $value;
                    $bindParams[] = &$params[$index];
                }
                if (!call_user_func_array([$statement, 'bind_param'], $bindParams)) {
                    error_log(sprintf(
                        'MySQL parameter binding failed (%d, SQLSTATE %s): %s',
                        $statement->errno,
                        $statement->sqlstate,
                        $statement->error
                    ));
                    mysqli_stmt_close($statement);
                    return false;
                }
            }

            if (!mysqli_stmt_execute($statement)) {
                error_log(sprintf(
                    'MySQL statement execution failed (%d, SQLSTATE %s): %s',
                    $statement->errno,
                    $statement->sqlstate,
                    $statement->error
                ));
                mysqli_stmt_close($statement);
                return false;
            }

            $result = mysqli_stmt_get_result($statement);
            if ($result instanceof mysqli_result) {
                $rows = mysqli_fetch_all($result, MYSQLI_ASSOC);
                mysqli_free_result($result);
                mysqli_stmt_close($statement);
                return $rows;
            }

            mysqli_stmt_close($statement);
            return true;
        } catch (Throwable $e) {
            $details = $statement instanceof mysqli_stmt
                ? sprintf('SQLSTATE %s: %s', $statement->sqlstate, $statement->error)
                : $e->getMessage();
            error_log(sprintf(
                'MySQL operation failed (%s, code %d): %s',
                get_class($e),
                (int) $e->getCode(),
                $details
            ));
            if ($statement instanceof mysqli_stmt) {
                mysqli_stmt_close($statement);
            }
            return false;
        }
    }
}