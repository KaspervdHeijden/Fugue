<?php

declare(strict_types=1);

namespace Fugue\Controller;

use Fugue\View\Templating\TemplateAdapterFactory;
use Fugue\Collection\PropertyBag;
use Fugue\HTTP\StringBuffer;
use Fugue\HTTP\HeaderBag;
use Fugue\HTTP\Response;
use Fugue\HTTP\Header;

use function array_merge;
use function json_encode;

abstract class Controller
{
    public const DEFAULT_CHARSET = 'utf-8';

    private TemplateAdapterFactory $templateFactory;

    public function __construct(TemplateAdapterFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    private function getTemplateVariables(
        string $title,
        array $variables,
        string $charset
    ): PropertyBag {
        $defaults = [
            'charset'    => $charset,
            'pageTitle'  => $title,
            'message'    => '',
            'content'    => '',
        ];

        return new PropertyBag(array_merge($defaults, $variables));
    }

    final protected function getTemplateFactory(): TemplateAdapterFactory
    {
        return $this->templateFactory;
    }

    /**
     * Generates a response view in the document context.
     *
     * @param string $title            The page title.
     * @param string $contentTemplate  The content template file.
     * @param string $documentTemplate The document outline template file.
     * @param array  $variables        HashMap of variables to pass to the template.
     * @param int    $statusCode       The status code for the response.
     * @param string $contentKey       The key in variables for the content.
     *
     * @return Response                The generated response.
     */
    protected function createDocumentResponse(
        string $title,
        string $contentTemplate,
        string $documentTemplate,
        array $variables       = [],
        int $statusCode        = Response::HTTP_OK,
        string $charset        = self::DEFAULT_CHARSET,
        string $contentVarName = 'content'
    ): Response {
        $view                       = $this->templateFactory->getForTemplate($contentTemplate);
        $variables                  = $this->getTemplateVariables($title, $variables, $charset);
        $variables[$contentVarName] = $view->render($contentTemplate, new PropertyBag($variables));

        return $this->createResponse(
            $view->render($documentTemplate, new PropertyBag($variables)),
            Response::CONTENT_TYPE_HTML,
            $statusCode
        );
    }

    protected function createJSONResponse(
        array $data,
        int $statusCode = Response::HTTP_OK
    ): Response {
        return $this->createResponse(
            json_encode($data),
            Response::CONTENT_TYPE_JAVASCRIPT,
            $statusCode
        );
    }

    protected function createPlainTextResponse(
        string $text,
        int $statusCode = Response::HTTP_OK
    ): Response {
        return $this->createResponse(
            $text,
            Response::CONTENT_TYPE_PLAINTEXT,
            $statusCode
        );
    }

    protected function createCSVResponse(
        string $csv,
        string $fileName = '',
        int $statusCode  = Response::HTTP_OK
    ): Response {
        $headers = [];
        if ($fileName !== '') {
            $headers[] = Header::contentDisposition("attachment;filename=\"{$fileName}\"");
        }

        return $this->createResponse(
            $csv,
            Response::CONTENT_TYPE_CSV,
            $statusCode,
            $headers
        );
    }

    protected function createRedirectResponse(
        string $url,
        string $message = '',
        int $statusCode = Response::HTTP_MOVED_TEMPORARILY
    ): Response {
        if ($message === '') {
            $message = "The resource you are trying to access can be found here: '{$url}'.";
        }

        return $this->createResponse(
            $message,
            Response::CONTENT_TYPE_PLAINTEXT,
            $statusCode,
            [Header::location($url)]
        );
    }

    protected function renderView(
        string $contentTemplate,
        array $variables    = [],
        string $contentType = Response::CONTENT_TYPE_HTML,
        int $statusCode     = Response::HTTP_OK
    ): Response {
        $content = $this->templateFactory
                        ->getForTemplate($contentTemplate)
                        ->render($contentTemplate, new PropertyBag($variables));

        return $this->createResponse(
            $content,
            $contentType,
            $statusCode
        );
    }

    protected function createResponse(
        string $content,
        string $contentType,
        int $statusCode,
        array $headers = []
    ): Response {
        $headerBag = new HeaderBag();
        foreach ($headers as $key => $value) {
            $headerBag->set($value, $key);
        }

        if ($contentType !== '') {
            $headerBag->set(Header::contentType($contentType));
        }

        return new Response(
            new StringBuffer($content),
            $statusCode,
            $headerBag
        );
    }
}
