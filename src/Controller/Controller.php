<?php

declare(strict_types=1);

namespace Fugue\Controller;

use Fugue\View\Templating\TemplateAdapterFactory;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\HTTP\HeaderBag;
use Fugue\HTTP\Response;
use Fugue\HTTP\Header;

use function array_merge;
use function json_encode;

abstract class Controller
{
    /**
     * @var string Default html if no content will be rendered.
     */
    private const DEFAULT_HTML =
        '<p class="empty-page">You have encountered an empty page. Please notify your system administrator.</p>';

    /**
     * @var string Redirect page html.
     */
    private const REDIRECT_HTML = 'The resource you are trying to access can be found here: %s';

    /** @var TemplateAdapterFactory */
    private $templateFactory;

    public function __construct(TemplateAdapterFactory $templateFactory)
    {
        $this->templateFactory = $templateFactory;
    }

    private function getTemplateVariables(string $title, array $variables): array
    {
        $defaults = [
            'charset'    => RuntimeInterface::CHARSET,
            'content'    => self::DEFAULT_HTML,
            'pageTitle'  => $title,
            'message'    => '',
        ];

        return array_merge($defaults, $variables);
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
     *
     * @return Response               The generated response.
     */
    protected function createDocumentResponse(
        string $title,
        string $contentTemplate,
        string $documentTemplate,
        array $variables = [],
        int $statusCode  = Response::HTTP_OK
    ): Response {
        $view                 = $this->templateFactory->getForTemplate($contentTemplate);
        $variables            = $this->getTemplateVariables($title, $variables);
        $variables['content'] = $view->render($contentTemplate, $variables);

        return $this->createResponse(
            $view->render($documentTemplate, $variables),
            Response::CONTENT_TYPE_HTML,
            $statusCode
        );
    }

    /**
     * Generates a JSON response.
     *
     * @param array $data       The data to return as a JSON file.
     * @param int   $statusCode The status code for the response.
     *
     * @return Response         The generated response.
     */
    protected function createJSONResponse(array $data, int $statusCode = Response::HTTP_OK): Response
    {
        return $this->createResponse(
            json_encode($data),
            Response::CONTENT_TYPE_JAVASCRIPT,
            $statusCode
        );
    }

    /**
     * Generates a plain text response.
     *
     * @param string $text       The data to return as a JSON file.
     * @param int    $statusCode The status code for the response.
     *
     * @return Response          The generated response.
     */
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

    /**
     * Generates a CSV response.
     *
     * @param string $csv         The CSV content.
     * @param string $fileName    The CSV filename.
     * @param int    $statusCode  The status code for the response.
     *
     * @return Response           The generated response.
     */
    protected function createCSVResponse(
        string $csv,
        string $fileName = '',
        int $statusCode  = Response::HTTP_OK
    ): Response {
        $headers = [];
        if ($fileName !== '') {
            $headers[Header::NAME_CONTENT_DISPOSITION] = "attachment;filename=\"{$fileName}\"";
        }

        return $this->createResponse(
            $csv,
            Response::CONTENT_TYPE_CSV,
            $statusCode,
            $headers
        );
    }

    /**
     * Generates a view for a redirect.
     *
     * @param string $url        The URL to redirect to.
     * @param string $message    The message to show.
     * @param int    $statusCode The status code for the response. Defaults to "302 Moved Temporarily".
     *
     * @return Response          The generated response.
     */
    protected function createRedirectResponse(
        string $url,
        string $message = '',
        $statusCode     = Response::HTTP_MOVED_TEMPORARILY
    ): Response {
        if ($message === '') {
            $message = sprintf(self::REDIRECT_HTML, $url);
        }

        return $this->createResponse(
            $message,
            Response::CONTENT_TYPE_PLAINTEXT,
            $statusCode,
            [Header::NAME_LOCATION => $url]
        );
    }

    /**
     * Renders a view without a document context.
     *
     * @param string $contentTemplate The content template file.
     * @param array  $variables       HashMap of variables to pass to the template.
     * @param string $contentType     The content type of the response.
     * @param int    $statusCode      The status code for the response.
     *
     * @return Response               The generated content.
     */
    protected function renderView(
        string $contentTemplate,
        array $variables    = [],
        string $contentType = Response::CONTENT_TYPE_HTML,
        int $statusCode     = Response::HTTP_OK
    ): Response {
        $content = $this->templateFactory
                        ->getForTemplate($contentTemplate)
                        ->render($contentTemplate, $variables);

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
            $headerBag->setFromString($key, $value);
        }

        if ($contentType !== '') {
            $headerBag->setFromString(
                Header::NAME_CONTENT_TYPE,
                $contentType
            );
        }

        return new Response($content, $statusCode, $headerBag);
    }
}
