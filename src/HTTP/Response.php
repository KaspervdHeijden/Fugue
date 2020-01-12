<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use function preg_replace_callback;
use function mb_strtoupper;
use function mb_strtolower;
use function preg_replace;
use function ucfirst;
use function gmdate;
use function floor;
use function time;
use function trim;

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
    public const HTTP_CONFLICT = 408;

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
    public const CONTENT_TYPE_HTML       = 'text/html';

    /** @var string */
    public const CONTENT_TYPE_CSS        = 'text/css';

    /** @var string */
    public const CONTENT_TYPE_CSV        = 'text/csv';

    /** @var string */
    public const CONTENT_TYPE_PDF        = 'application/pdf';

    /** @var int */
    private $statusCode;

    /** @var string[] */
    private $headers = [];

    /** @var string */
    private $content;

    /**
     * Creates a new Response object.
     *
     * @param string $content     The response content.
     * @param string $contentType The content-type to use.
     * @param int    $statusCode  The status code.
     */
    public function __construct(
        string $content     = '',
        string $contentType = self::CONTENT_TYPE_HTML,
        int $statusCode     = self::HTTP_OK
    ) {
        $this->setContentType($contentType);
        $this->setStatusCode($statusCode);
        $this->setContent($content);
    }

    /**
     * Gets a date format suitable for a header.
     *
     * @param int $timestamp The timestamp to convert.
     * @return string        The date in header format.
     */
    private function headerDate(int $timestamp): string
    {
        return gmdate('D, d M Y H:i:s', $timestamp) . ' GMT';
    }

    /**
     * Attempts tp prevent client-side caching of this response.
     */
    public function disableCache(): void
    {
        $this->headers['Expires']       = $this->headerDate(time() - 10800);
        $this->headers['Cache-Control'] = 'no-store,no-cache';
        $this->headers['Pragma']        = 'no-cache';

        unset($this->headers['Last-Modified']);
    }

    /**
     * Makes this response catchable.
     *
     * @param int $lastModified The last modified for this response.
     * @param int $maxAge       The max age for this response.
     */
    public function enableCache(int $lastModified, int $maxAge): void
    {
        unset($this->headers['Expires'], $this->headers['Pragma']);
        if ($lastModified > 0) {
            $this->headers['Last-Modified'] = $this->headerDate($lastModified);
        }

        $this->headers['Cache-Control'] = 'private' . ($maxAge > 0 ? ",max-age={$maxAge}" : '');
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
     * Gets all active headers
     *
     * @return string[] In the form [name => value].
     */
    public function getHeaders(): array
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

    /**
     * Is this a Informational response?
     *
     * @return bool TRUE if the status code is between 100 and 200, FALSE otherwise.
     */
    public function isInformational(): bool
    {
        return ((int)floor($this->getStatusCode() / 100) === 1);
    }

    /**
     * Is this a successful response?
     *
     * @return bool TRUE if the status code is between 200 and 300, FALSE otherwise.
     */
    public function isSuccessful(): bool
    {
        return ((int)floor($this->getStatusCode() / 100) === 2);
    }

    /**
     * Is this a redirect?
     *
     * @return bool TRUE if the status code is between 300 and 400, FALSE otherwise.
     */
    public function isRedirect(): bool
    {
        return ((int)floor($this->getStatusCode() / 100) === 3);
    }

    /**
     * Is this a client error response?
     *
     * @return bool TRUE if the status code is between 400 and 500, FALSE otherwise.
     */
    public function isClientError(): bool
    {
        return ((int)floor($this->getStatusCode() / 100) === 4);
    }

    /**
     * Is this a server error response?
     *
     * @return bool TRUE if the status code is between 500 and 600, FALSE otherwise.
     */
    public function isServerError(): bool
    {
        return ((int)floor($this->getStatusCode() / 100) === 5);
    }

    /**
     * Gets a slugged version of the name.
     *
     * @param string $name The name to be slugged.
     * @return string      The slugged version of the input.
     */
    private function getSluggedKey(string $name): string
    {
        return trim(preg_replace_callback(
            '/\-[a-z]/u',
            static function (array $match): string {
                return mb_strtoupper($match[0]);
            },
            ucfirst(mb_strtolower(preg_replace('/_+/u', '-', $name)))
        ));
    }

    /**
     * Sets the Content type for the response.
     *
     * @param string $contentType The new contentType.
     */
    public function setContentType(string $contentType): void
    {
        $this->headers['Content-Type'] = $contentType;
    }

    /**
     * Sets the Content type for the response.
     *
     * @return string The contentType of this Response.
     */
    public function getContentType(): string
    {
        return $this->headers['Content-Type'] ?? '';
    }

    /**
     * Sets a header value.
     *
     * @param string $name  The name of the header.
     * @param string $value The value to assign.
     */
    public function setHeader(string $name, string $value): void
    {
        $sluggedName = $this->getSluggedKey($name);
        if ($sluggedName !== '') {
            $this->headers[$sluggedName] = $value;
        }
    }

    /**
     * Unset a header.
     *
     * @param string $name The name of the header to unset.
     */
    public function unsetHeader(string $name): void
    {
        $sluggedName = $this->getSluggedKey($name);
        if ($sluggedName !== '') {
            unset($this->headers[$sluggedName]);
        }
    }

    /**
     * Gets a header value
     *
     * @param string $name The name of the header.
     * @return string|null The header value, or null if not set.
     */
    public function getHeader(string $name): ?string
    {
        $sluggedName = $this->getSluggedKey($name);
        return $this->headers[$sluggedName] ?? null;
    }

    /**
     * Gets the text from a status code.
     *
     * @return string The status text.
     */
    public function getStatusCodeText(): string
    {
        switch ($this->getStatusCode()) {
            /* 100 range (informational) */
            case self::HTTP_CONTINUE:
                return 'Continue';
            case self::HTTP_SWITCHING_PROTOCOLS:
                return 'Switching Protocols';

            /* 200 range (success) */
            case self::HTTP_OK:
                return 'OK';
            case self::HTTP_CREATED:
                return 'Created';
            case self::HTTP_ACCEPTED:
                return 'Accepted';
            case self::HTTP_NONAUTHORITATIVE_INFORMATION:
                return 'Non-Authoritative Information';
            case self::HTTP_RESET_CONTENT: // fall through
            case self::HTTP_NO_CONTENT:
                return 'Reset Content';
            case self::HTTP_PARTIAL_CONTENT:
                return 'Partial Content';

            /** 300 range (redirects) */
            case self::HTTP_MULTIPLE_CHOICES:
                return 'Multiple Choices';
            case self::HTTP_MOVED_PERMANENTLY:
                return 'Moved Permanently';
            case self::HTTP_MOVED_TEMPORARILY:
                return 'Moved Temporarily';
            case self::HTTP_SEE_OTHER:
                return 'See Other';
            case self::HTTP_NOT_MODIFIED:
                return 'Not Modified';
            case self::HTTP_USE_PROXY:
                return 'Use Proxy';

            /** 400 range (clients errors) */
            case self::HTTP_BAD_REQUEST:
                return 'Bad Request';
            case self::HTTP_UNAUTHORIZED:
                return 'Unauthorized';
            case self::HTTP_PAYMENT_REQUIRED:
                return 'Payment Required';
            case self::HTTP_FORBIDDEN:
                return 'Forbidden';
            case self::HTTP_NOT_FOUND:
                return 'Not Found';
            case self::HTTP_METHOD_NOT_ALLOWED:
                return 'Method Not Allowed';
            case self::HTTP_NOT_ACCEPTABLE:
                return 'Not Acceptable';
            case self::HTTP_PROXY_AUTHENTICATION_REQUIRED:
                return 'Proxy Authentication Required';
            case self::HTTP_REQUEST_TIMEOUT:
                return 'Request Time-out';
            case self::HTTP_CONFLICT:
                return 'Conflict';
            case self::HTTP_GONE:
                return 'Gone';
            case self::HTTP_LENGTH_REQUIRED:
                return 'Length Required';
            case self::HTTP_PRECONDITION_FAILED:
                return 'Precondition Failed';
            case self::HTTP_REQUEST_ENTITY_TOO_LARGE:
                return 'Request Entity Too Large';
            case self::HTTP_REQUESTURI_TOO_LARGE:
                return 'Request-URI Too Large';
            case self::HTTP_UNSUPPORTED_MEDIA_TYPE:
                return 'Unsupported Media Type';
            case self::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE:
                return 'Range Not Satisfiable';
            case self::HTTP_EXPECTATION_FAILED:
                return 'Expectation Failed';
            case self::HTTP_IM_A_TEAPOT:
                return 'I\'m a teapot';
            case self::HTTP_ENHANCE_YOUR_CALM:
                return 'Enhance Your Calm';

            /** 500 range (server errors) */
            case self::HTTP_INTERNAL_SERVER_ERROR:
                return 'Internal Server Error';
            case self::HTTP_NOT_IMPLEMENTED:
                return 'Not Implemented';
            case self::HTTP_BAD_GATEWAY:
                return 'Bad Gateway';
            case self::HTTP_SERVICE_UNAVAILABLE:
                return 'Service Unavailable';
            case self::HTTP_GATEWAY_TIMEOUT:
                return 'Gateway Time-out';
            case self::HTTP_HTTP_VERSION_NOT_SUPPORTED:
                return 'HTTP Version Not supported';

            /** Default (unknown) */
            default:
                return 'Unknown';
        }
    }
}
