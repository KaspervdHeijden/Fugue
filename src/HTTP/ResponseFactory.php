<?php

declare(strict_types=1);

namespace Fugue\HTTP;

use Fugue\View\Templating\TemplateAdapterFactory;
use Fugue\Collection\PropertyBag;

use function json_encode;

final class ResponseFactory
{
    private TemplateAdapterFactory $templateFactory;

    public function __construct(TemplateAdapterFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    public function json(
        array $data,
        int $statusCode = Response::HTTP_OK
    ): Response {
        return $this->create(
            json_encode($data),
            Response::CONTENT_TYPE_JAVASCRIPT,
            $statusCode
        );
    }

    public function plainText(
        string $text,
        int $statusCode = Response::HTTP_OK
    ): Response {
        return $this->create(
            $text,
            Response::CONTENT_TYPE_PLAINTEXT,
            $statusCode
        );
    }

    public function csv(
        string $csv,
        string $fileName = '',
        int $statusCode  = Response::HTTP_OK
    ): Response {
        $headers = new HeaderBag();
        if ($fileName !== '') {
            $headers[] = Header::contentDisposition(
                "attachment;filename=\"{$fileName}\""
            );
        }

        return $this->create(
            $csv,
            Response::CONTENT_TYPE_CSV,
            $statusCode,
            $headers
        );
    }

    public function redirect(
        string $url,
        string $message = '',
        int $statusCode = Response::HTTP_MOVED_TEMPORARILY
    ): Response {
        if ($message === '') {
            $message = "The requested resource can be found here: '{$url}'";
        }

        return $this->create(
            $message,
            Response::CONTENT_TYPE_PLAINTEXT,
            $statusCode,
            new HeaderBag([Header::location($url)])
        );
    }

    public function view(
        string $templateName,
        iterable $variables = [],
        string $contentType = Response::CONTENT_TYPE_HTML,
        int $statusCode     = Response::HTTP_OK
    ): Response {
        $content = $this->templateFactory
                        ->getForTemplateName($templateName)
                        ->render($templateName, PropertyBag::forAuto($variables));

        return $this->create(
            $content,
            $contentType,
            $statusCode
        );
    }

    public function create(
        string $content,
        string $contentType,
        int $statusCode   = Response::HTTP_OK,
        iterable $headers = []
    ): Response {
        $headerBag = HeaderBag::forAuto($headers);
        if ($contentType !== '') {
            $headerBag[] = Header::contentType($contentType);
        }

        return new Response(
            new StringBuffer($content),
            $headerBag,
            $statusCode,
        );
    }
}
