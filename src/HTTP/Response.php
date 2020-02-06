<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use function floor;

final class Response
{
    /*
     * Informative
     */
    /** @var int */
    public const HTTP_CONTINUE = 100;

    /** @var int */
    public const HTTP_SWITCHING_PROTOCOLS = 101;

    /*
     * Success
     */
    /** @var int */
    public const HTTP_OK = 200;

    /** @var int */
    public const HTTP_CREATED = 201;

    /** @var int */
    public const HTTP_ACCEPTED = 202;

    /** @var int */
    public const HTTP_NONAUTHORITATIVE_INFORMATION = 203;

    /** @var int */
    public const HTTP_NO_CONTENT = 204;

    /** @var int */
    public const HTTP_RESET_CONTENT = 205;

    /** @var int */
    public const HTTP_PARTIAL_CONTENT = 206;

    /*
     * Redirects
     */
    /** @var int */
    public const HTTP_MULTIPLE_CHOICES = 300;

    /** @var int */
    public const HTTP_MOVED_PERMANENTLY = 301;

    /** @var int */
    public const HTTP_MOVED_TEMPORARILY = 302;

    /** @var int */
    public const HTTP_FOUND = 302;

    /** @var int */
    public const HTTP_SEE_OTHER = 303;

    /** @var int */
    public const HTTP_NOT_MODIFIED = 304;

    /** @var int */
    public const HTTP_USE_PROXY = 305;

    /*
     * Client errors
     */
    /** @var int */
    public const HTTP_BAD_REQUEST = 400;

    /** @var int */
    public const HTTP_UNAUTHORIZED = 401;

    /** @var int */
    public const HTTP_PAYMENT_REQUIRED = 402;

    /** @var int */
    public const HTTP_FORBIDDEN = 403;

    /** @var int */
    public const HTTP_NOT_FOUND = 404;

    /** @var int */
    public const HTTP_METHOD_NOT_ALLOWED = 405;

    /** @var int */
    public const HTTP_NOT_ACCEPTABLE = 406;

    /** @var int */
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED = 407;

    /** @var int */
    public const HTTP_REQUEST_TIMEOUT = 408;

    /** @var int */
    public const HTTP_CONFLICT = 409;

    /** @var int */
    public const HTTP_GONE = 410;

    /** @var int */
    public const HTTP_LENGTH_REQUIRED = 411;

    /** @var int */
    public const HTTP_PRECONDITION_FAILED = 412;

    /** @var int */
    public const HTTP_REQUEST_ENTITY_TOO_LARGE = 413;

    /** @var int */
    public const HTTP_REQUESTURI_TOO_LARGE = 414;

    /** @var int */
    public const HTTP_UNSUPPORTED_MEDIA_TYPE = 415;

    /** @var int */
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;

    /** @var int */
    public const HTTP_EXPECTATION_FAILED = 417;

    /** @var int */
    public const HTTP_IM_A_TEAPOT = 418;

    /** @var int */
    public const HTTP_ENHANCE_YOUR_CALM = 420;

    /*
     * Server errors
     */
    /** @var int */
    public const HTTP_INTERNAL_SERVER_ERROR = 500;

    /** @var int */
    public const HTTP_NOT_IMPLEMENTED = 501;

    /** @var int */
    public const HTTP_BAD_GATEWAY = 502;

    /** @var int */
    public const HTTP_SERVICE_UNAVAILABLE = 503;

    /** @var int */
    public const HTTP_GATEWAY_TIMEOUT = 504;

    /** @var int */
    public const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;

    /*
     * Constants for the content-type
     */
    /** @var string */
    public const CONTENT_TYPE_JAVASCRIPT = 'text/javascript';

    /** @var string */
    public const CONTENT_TYPE_PLAINTEXT  = 'text/plain';

    /** @var string */
    public const CONTENT_TYPE_HTML = 'text/html';

    /** @var string */
    public const CONTENT_TYPE_CSS = 'text/css';

    /** @var string */
    public const CONTENT_TYPE_CSV = 'text/csv';

    /** @var string */
    public const CONTENT_TYPE_PDF = 'application/pdf';
    
    private const STATUS_CODE_MAPPING = [
        self::HTTP_CONTINUE => 'Continue',
        self::HTTP_SWITCHING_PROTOCOLS => 'Switching Protocols',
        self::HTTP_OK => 'OK',
        self::HTTP_CREATED => 'Created',
        self::HTTP_ACCEPTED => 'Accepted',
        self::HTTP_NONAUTHORITATIVE_INFORMATION => 'Non-Authoritative Information',
        self::HTTP_RESET_CONTENT => 'Reset Content',
        self::HTTP_NO_CONTENT => 'Reset Content',
        self::HTTP_PARTIAL_CONTENT => 'Partial Content',
        self::HTTP_MULTIPLE_CHOICES => 'Multiple Choices',
        self::HTTP_MOVED_PERMANENTLY => 'Moved Permanently',
        self::HTTP_MOVED_TEMPORARILY => 'Moved Temporarily',
        self::HTTP_SEE_OTHER => 'See Other',
        self::HTTP_NOT_MODIFIED => 'Not Modified',
        self::HTTP_USE_PROXY => 'Use Proxy',
        self::HTTP_BAD_REQUEST => 'Bad Request',
        self::HTTP_UNAUTHORIZED => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED => 'Payment Required',
        self::HTTP_FORBIDDEN => 'Forbidden',
        self::HTTP_NOT_FOUND => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE => 'Not Acceptable',
        self::HTTP_PROXY_AUTHENTICATION_REQUIRED => 'Proxy Authentication Required',
        self::HTTP_REQUEST_TIMEOUT => 'Request Time-out',
        self::HTTP_CONFLICT => 'Conflict',
        self::HTTP_GONE => 'Gone',
        self::HTTP_LENGTH_REQUIRED => 'Length Required',
        self::HTTP_PRECONDITION_FAILED => 'Precondition Failed',
        self::HTTP_REQUEST_ENTITY_TOO_LARGE => 'Request Entity Too Large',
        self::HTTP_REQUESTURI_TOO_LARGE => 'Request-URI Too Large',
        self::HTTP_UNSUPPORTED_MEDIA_TYPE => 'Unsupported Media Type',
        self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
        self::HTTP_EXPECTATION_FAILED => 'Expectation Failed',
        self::HTTP_IM_A_TEAPOT => 'I\'m a teapot',
        self::HTTP_ENHANCE_YOUR_CALM => 'Enhance Your Calm',
        self::HTTP_INTERNAL_SERVER_ERROR => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED => 'Not Implemented',
        self::HTTP_BAD_GATEWAY => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT => 'Gateway Time-out',
        self::HTTP_HTTP_VERSION_NOT_SUPPORTED => 'HTTP Version Not supported',
    ];

    /** @var int */
    private $statusCode;

    /** @var HeaderBag */
    private $headers;

    /** @var string */
    private $content;

    /**
     * Creates a new Response object.
     *
     * @param string    $content    The response content.
     * @param int       $statusCode The status code.
     * @param HeaderBag $headerBag  The header bag.
     */
    public function __construct(
        string $content,
        int $statusCode,
        HeaderBag $headerBag
    ) {
        $this->statusCode = $statusCode;
        $this->headers    = $headerBag;
        $this->content    = $content;
    }

    /**
     * Gets the content.
     *
     * @return string The content.
     */
    public function getContent(): string
    {
        return $this->content;
    }

    /**
     * Sets the content.
     *
     * @param string $content The content for the request.
     */
    public function setContent(string $content): void
    {
        $this->content = $content;
    }

    /**
     * Sets the content.
     *
     * @param string $content The content for the request.
     */
    public function appendContent(string $content): void
    {
        $this->content .= $content;
    }

    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

    /**
     * Gets the status code.
     *
     * @return int The HTTP status code.
     */
    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    /**
     * Sets the status code for this response.
     *
     * @param int $statusCode The HTTP status code.
     */
    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    private function getStatusCodeClass(): int
    {
        return (int)floor($this->getStatusCode() / 100);
    }

    /**
     * Is this a informational response?
     *
     * @return bool TRUE if the status code is between 100 and 200, FALSE otherwise.
     */
    public function isInformational(): bool
    {
        return ($this->getStatusCodeClass() === 1);
    }

    /**
     * Is this a successful response?
     *
     * @return bool TRUE if the status code is between 200 and 300, FALSE otherwise.
     */
    public function isSuccessful(): bool
    {
        return ($this->getStatusCodeClass() === 2);
    }

    /**
     * Is this a redirect?
     *
     * @return bool TRUE if the status code is between 300 and 400, FALSE otherwise.
     */
    public function isRedirect(): bool
    {
        return ($this->getStatusCodeClass() === 3);
    }

    /**
     * Is this a client error response?
     *
     * @return bool TRUE if the status code is between 400 and 500, FALSE otherwise.
     */
    public function isClientError(): bool
    {
        return ($this->getStatusCodeClass() === 4);
    }

    /**
     * Is this a server error response?
     *
     * @return bool TRUE if the status code is between 500 and 600, FALSE otherwise.
     */
    public function isServerError(): bool
    {
        return ($this->getStatusCodeClass() === 5);
    }

    /**
     * Gets the text from a status code.
     *
     * @return string The status text.
     */
    public function getStatusCodeText(): string
    {
        return self::STATUS_CODE_MAPPING[$this->getStatusCode()] ?? 'Unknown';
    }
}
