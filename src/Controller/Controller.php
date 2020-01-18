<?php

declare(strict_types=1);

namespace Fugue\Controller;

use Fugue\View\Templating\TemplateAdapterFactory;
use Fugue\Core\Runtime\RuntimeInterface;
use Fugue\View\Templating\TemplateUtil;
use Fugue\Collection\PropertyBag;
use Fugue\Configuration\Config;
use Fugue\HTTP\Routing\Route;
use Fugue\HTTP\Response;
use Fugue\HTTP\Request;

use function array_merge;
use function json_encode;

abstract class Controller
{
    /**
     * @var string Default html if no content will be rendered.
     */
    private const DEFAULT_HTML =
        '<p class="empty-page">You have encountered an empty page. Please notify your system administrator.</p>';

    /** @var TemplateUtil */
    private $templateUtil;

    /** @var Request */
    private $request;

    /** @var Config */
    private $config;

    /** @var Route */
    private $route;

    /**
     * Creates a Controller instance.
     *
     * @param Request      $request      The request.
     * @param Route|null   $route        The active route running this controller.
     * @param Config       $config       The loaded config.
     * @param TemplateUtil $templateUtil The template render utility helper.
     */
    public function __construct(
        Request $request,
        Route $route,
        Config $config,
        TemplateUtil $templateUtil
    ) {
        $this->templateUtil = $templateUtil;
        $this->request      = $request;
        $this->config       = $config;
        $this->route        = $route;
    }

    /**
     * Gets the Request.
     *
     * @return Request The request currently being handled.
     */
    final protected function getRequest(): Request
    {
        return $this->request;
    }

    /**
     * Gets the Route.
     *
     * @return Route The route that matched the URL and is being executed.
     */
    final protected function getRoute(): Route
    {
        return $this->route;
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

    final protected function getTemplateUtil(): TemplateUtil
    {
        return $this->templateUtil;
    }

    final protected function getTemplateFactory(): TemplateAdapterFactory
    {
        return new TemplateAdapterFactory($this->templateUtil);
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
        $view                 = $this->getTemplateFactory()->getForTemplate($contentTemplate);
        $variables            = $this->getTemplateVariables($title, $variables);
        $variables['content'] = $view->render($contentTemplate, $variables);

        return new Response(
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
        return new Response(
            json_encode($data),
            Response::CONTENT_TYPE_JAVASCRIPT,
            $statusCode
        );
    }

    /**
     * Generates a plaintext response.
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
        return new Response(
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
        $response = new Response(
            $csv,
            Response::CONTENT_TYPE_CSV,
            $statusCode
        );

        if ($fileName !== '') {
            $response->setHeader(
                'Content-Disposition',
                "attachment;filename=\"{$fileName}\""
            );
        }

        return $response;
    }

    /**
     * Generates a view for a redirect.
     *
     * @param string $url        The URL to redirect to.
     * @param string $message    The message to show to the user.
     * @param int    $statusCode The status code for the response. Defaults to "302 Moved Temporarily".
     *
     * @return Response          The generated response.
     */
    protected function createRedirectResponse(
        string $url,
        string $message = '',
        $statusCode     = Response::HTTP_MOVED_TEMPORARILY
    ): Response {
        $response = $this->renderView(
            'redirectPage',
            [
                'message' => $message,
                'url'     => $url,
            ],
            Response::CONTENT_TYPE_HTML,
            $statusCode
        );

        $response->setHeader('Location', $url);
        return $response;
    }

    /**
     * Renders a view without a document context.
     *
     * @param string $contentTemplate    The content template file.
     * @param array  $variables          HashMap of variables to pass to the template.
     * @param string $contentType        The content type of the response.
     * @param int    $statusCode         The status code for the response.
     *
     * @return Response                  The generated content.
     */
    protected function renderView(
        string $contentTemplate,
        array $variables    = [],
        string $contentType = Response::CONTENT_TYPE_HTML,
        int $statusCode     = Response::HTTP_OK
    ): Response {
        $content = $this->getTemplateFactory()
                        ->getForTemplate($contentTemplate)
                        ->render($contentTemplate, $variables);

        return new Response(
            $content,
            $contentType,
            $statusCode
        );
    }

    /**
     * Convenience method for quick access to GET variables.
     *
     * @return PropertyBag The GET values.
     */
    protected function get(): PropertyBag
    {
        return $this->request->get();
    }

    /**
     * Convenience method for quick access to POST variables.
     *
     * @return PropertyBag The POST values.
     */
    protected function post(): PropertyBag
    {
        return $this->request->post();
    }
}
