<?php

declare(strict_types=1);

namespace Fugue\HTTP;

final class Response
{
    /*
     * Informative
     */
    public const HTTP_CONTINUE            = 100;
    public const HTTP_SWITCHING_PROTOCOLS = 101;

    /*
     * Success
     */
    public const HTTP_OK                           = 200;
    public const HTTP_CREATED                      = 201;
    public const HTTP_ACCEPTED                     = 202;
    public const HTTP_NONAUTHORITATIVE_INFORMATION = 203;
    public const HTTP_NO_CONTENT                   = 204;
    public const HTTP_RESET_CONTENT                = 205;
    public const HTTP_PARTIAL_CONTENT              = 206;

    /*
     * Redirects
     */
    public const HTTP_MULTIPLE_CHOICES  = 300;
    public const HTTP_MOVED_PERMANENTLY = 301;
    public const HTTP_MOVED_TEMPORARILY = 302;
    public const HTTP_FOUND             = 302;
    public const HTTP_SEE_OTHER         = 303;
    public const HTTP_NOT_MODIFIED      = 304;
    public const HTTP_USE_PROXY         = 305;

    /*
     * Client errors
     */
    public const HTTP_BAD_REQUEST                     = 400;
    public const HTTP_UNAUTHORIZED                    = 401;
    public const HTTP_PAYMENT_REQUIRED                = 402;
    public const HTTP_FORBIDDEN                       = 403;
    public const HTTP_NOT_FOUND                       = 404;
    public const HTTP_METHOD_NOT_ALLOWED              = 405;
    public const HTTP_NOT_ACCEPTABLE                  = 406;
    public const HTTP_PROXY_AUTHENTICATION_REQUIRED   = 407;
    public const HTTP_REQUEST_TIMEOUT                 = 408;
    public const HTTP_CONFLICT                        = 409;
    public const HTTP_GONE                            = 410;
    public const HTTP_LENGTH_REQUIRED                 = 411;
    public const HTTP_PRECONDITION_FAILED             = 412;
    public const HTTP_REQUEST_ENTITY_TOO_LARGE        = 413;
    public const HTTP_REQUESTURI_TOO_LARGE            = 414;
    public const HTTP_UNSUPPORTED_MEDIA_TYPE          = 415;
    public const HTTP_REQUESTED_RANGE_NOT_SATISFIABLE = 416;
    public const HTTP_EXPECTATION_FAILED              = 417;
    public const HTTP_IM_A_TEAPOT                     = 418;
    public const HTTP_ENHANCE_YOUR_CALM               = 420;

    /*
     * Server errors
     */
    public const HTTP_INTERNAL_SERVER_ERROR      = 500;
    public const HTTP_NOT_IMPLEMENTED            = 501;
    public const HTTP_BAD_GATEWAY                = 502;
    public const HTTP_SERVICE_UNAVAILABLE        = 503;
    public const HTTP_GATEWAY_TIMEOUT            = 504;
    public const HTTP_HTTP_VERSION_NOT_SUPPORTED = 505;

    /*
     * Constants for the content-type
     */
    public const CONTENT_TYPE_JAVASCRIPT = 'text/javascript';
    public const CONTENT_TYPE_PLAINTEXT  = 'text/plain';
    public const CONTENT_TYPE_HTML       = 'text/html';
    public const CONTENT_TYPE_CSS        = 'text/css';
    public const CONTENT_TYPE_CSV        = 'text/csv';
    public const CONTENT_TYPE_PDF        = 'application/pdf';
    
    private const STATUS_CODE_MAPPING    = [
        self::HTTP_CONTINUE                        => 'Continue',
        self::HTTP_SWITCHING_PROTOCOLS             => 'Switching Protocols',
        self::HTTP_OK                              => 'OK',
        self::HTTP_CREATED                         => 'Created',
        self::HTTP_ACCEPTED                        => 'Accepted',
        self::HTTP_NONAUTHORITATIVE_INFORMATION    => 'Non-Authoritative Information',
        self::HTTP_RESET_CONTENT                   => 'Reset Content',
        self::HTTP_NO_CONTENT                      => 'Reset Content',
        self::HTTP_PARTIAL_CONTENT                 => 'Partial Content',
        self::HTTP_MULTIPLE_CHOICES                => 'Multiple Choices',
        self::HTTP_MOVED_PERMANENTLY               => 'Moved Permanently',
        self::HTTP_MOVED_TEMPORARILY               => 'Moved Temporarily',
        self::HTTP_SEE_OTHER                       => 'See Other',
        self::HTTP_NOT_MODIFIED                    => 'Not Modified',
        self::HTTP_USE_PROXY                       => 'Use Proxy',
        self::HTTP_BAD_REQUEST                     => 'Bad Request',
        self::HTTP_UNAUTHORIZED                    => 'Unauthorized',
        self::HTTP_PAYMENT_REQUIRED                => 'Payment Required',
        self::HTTP_FORBIDDEN                       => 'Forbidden',
        self::HTTP_NOT_FOUND                       => 'Not Found',
        self::HTTP_METHOD_NOT_ALLOWED              => 'Method Not Allowed',
        self::HTTP_NOT_ACCEPTABLE                  => 'Not Acceptable',
        self::HTTP_PROXY_AUTHENTICATION_REQUIRED   => 'Proxy Authentication Required',
        self::HTTP_REQUEST_TIMEOUT                 => 'Request Time-out',
        self::HTTP_CONFLICT                        => 'Conflict',
        self::HTTP_GONE                            => 'Gone',
        self::HTTP_LENGTH_REQUIRED                 => 'Length Required',
        self::HTTP_PRECONDITION_FAILED             => 'Precondition Failed',
        self::HTTP_REQUEST_ENTITY_TOO_LARGE        => 'Request Entity Too Large',
        self::HTTP_REQUESTURI_TOO_LARGE            => 'Request-URI Too Large',
        self::HTTP_UNSUPPORTED_MEDIA_TYPE          => 'Unsupported Media Type',
        self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE => 'Range Not Satisfiable',
        self::HTTP_EXPECTATION_FAILED              => 'Expectation Failed',
        self::HTTP_IM_A_TEAPOT                     => 'I\'m a teapot',
        self::HTTP_ENHANCE_YOUR_CALM               => 'Enhance Your Calm',
        self::HTTP_INTERNAL_SERVER_ERROR           => 'Internal Server Error',
        self::HTTP_NOT_IMPLEMENTED                 => 'Not Implemented',
        self::HTTP_BAD_GATEWAY                     => 'Bad Gateway',
        self::HTTP_SERVICE_UNAVAILABLE             => 'Service Unavailable',
        self::HTTP_GATEWAY_TIMEOUT                 => 'Gateway Time-out',
        self::HTTP_HTTP_VERSION_NOT_SUPPORTED      => 'HTTP Version Not supported',
    ];

    private StringBuffer $content;
    private HeaderBag $headers;
    private int $statusCode;

    public function __construct(
        StringBuffer $content,
        HeaderBag $headers,
        int $statusCode
    ) {
        $this->statusCode = $statusCode;
        $this->headers    = $headers;
        $this->content    = $content;
    }

    public function getContent(): StringBuffer
    {
        return $this->content;
    }

    public function getHeaders(): HeaderBag
    {
        return $this->headers;
    }

    public function getStatusCode(): int
    {
        return $this->statusCode;
    }

    public function setStatusCode(int $statusCode): void
    {
        $this->statusCode = $statusCode;
    }

    private function getStatusCodeClass(): int
    {
        return (int)($this->getStatusCode() / 100);
    }

    public function isInformational(): bool
    {
        return ($this->getStatusCodeClass() === 1);
    }

    public function isSuccessful(): bool
    {
        return ($this->getStatusCodeClass() === 2);
    }

    public function isRedirect(): bool
    {
        return ($this->getStatusCodeClass() === 3);
    }

    public function isClientError(): bool
    {
        return ($this->getStatusCodeClass() === 4);
    }

    public function isServerError(): bool
    {
        return ($this->getStatusCodeClass() === 5);
    }

    public function getStatusText(): string
    {
        return self::STATUS_CODE_MAPPING[$this->getStatusCode()] ?? 'Unknown';
    }
}
