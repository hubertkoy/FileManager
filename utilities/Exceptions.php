<?php

class HttpException extends Exception
{
    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}

class SingletonException extends Exception
{
    public function __construct($name, Throwable $previous = null)
    {
        parent::__construct("Singleton ($name) was created more than once.", 0, $previous);
    }
}

class SqlPDOException extends PDOException
{
    public function __construct($sql_query, $variables, Throwable $previous = null)
    {
        $variables = implode(', ', array_map(
            function ($v, $k) {
                return sprintf("%s='%s'", $k, $v);
            },
            $variables,
            array_keys($variables)
        ));
        parent::__construct("#Query# $sql_query #Variables# $variables", 0, $previous);
    }
}

class SessionException extends Exception
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Access to session file failed", 0, $previous);
    }
}

class TemporaryRedirectException extends HttpException
{
    private string $location;

    public function __construct(string $location, Throwable $previous = null)
    {
        parent::__construct("Temporary Redirect", 307, $previous);
        $this->location = $location;
    }

    public function getLocation(): string
    {
        return $this->location;
    }
}

class BadRequestException extends HttpException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Bad Request", 400, $previous);
    }
}

class UnauthorizedException extends HttpException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Unauthorized", 401, $previous);
    }
}

class NotFoundException extends HttpException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Not Found", 404, $previous);
    }
}

class InternalServerErrorException extends HttpException
{
    public function __construct(Throwable $previous = null)
    {
        parent::__construct("Internal Server Error", 500, $previous);
    }
}

class ApiJsonException extends HttpException
{
    public function __construct($message, $code, Throwable $previous = null)
    {
        assert(is_array($message) && isset($message['message']));
        $message = json_encode($message);
        parent::__construct($message, $code, $previous);
    }
}

class BadRequestJsonException extends ApiJsonException
{
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 400, $previous);
    }
}

class UnauthorizedJsonException extends ApiJsonException
{
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 401, $previous);
    }
}

class ForbiddenJsonException extends ApiJsonException
{
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 403, $previous);
    }
}

class NotFoundJsonException extends ApiJsonException
{
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 404, $previous);
    }
}

class ConflictJsonException extends ApiJsonException
{
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 409, $previous);
    }
}

class InternalServerErrorJsonException extends ApiJsonException
{
    public function __construct($message, Throwable $previous = null)
    {
        parent::__construct($message, 500, $previous);
    }
}