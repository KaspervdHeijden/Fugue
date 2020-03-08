<?php

declare(strict_types=1);

namespace Fugue\Mailing\MailPart;

use Fugue\HTTP\Response;
use DOMDocument;
use DOMNode;

use const XML_ELEMENT_NODE;
use const XML_TEXT_NODE;

use function libxml_use_internal_errors;
use function mb_strtolower;
use function str_repeat;
use function mb_strlen;
use function in_array;
use function trim;

final class HtmlTextMessage extends TextMessage
{
    /** @var string[] */
    private const BLOCK_ELEMENTS = ['blockquote', 'footer', 'header', 'aside', 'code', 'div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr'];

    /** @var string[] */
    private const HEADER_ELEMENTS = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    /** @var string[] */
    private const INLINE_ELEMENTS = ['font', 'span', 'b', 'i', 'u', 's'];

    /** @var string[] */
    private const BREAK_ELEMENTS = ['br', 'wbr'];

    /** @var string[] */
    private const SKIP_ELEMENTS = ['head'];

    /** @var string[] */
    private const LINE_ELEMENTS = ['hr'];

    public function __construct(
        string $body,
        string $transferEncoding = self::TRANSFER_ENCODING_QUOTED_PRINTABLE
    ) {
        parent::__construct(
            $body,
            Response::CONTENT_TYPE_HTML,
            $transferEncoding
        );
    }

    /**
     * Finds text content within HTML.
     *
     * @param DOMNode $node      The node to get the textContent from.
     * @param string  $content   The textContent found as an out param.
     * @param string  $activeTag The activeTag. Ths is for an internal out param.
     */
    private function findTextNodesFromNode(
        DOMNode $node,
        string &$content,
        string $activeTag
    ): void {
        $activeTag = mb_strtolower($activeTag);

        foreach ($node->childNodes as $childNode) {
            switch ($childNode->nodeType) {
                case XML_TEXT_NODE:
                    $textContent = (string)$childNode->nodeValue;

                    if ($textContent !== '') {
                        if (in_array($activeTag, self::HEADER_ELEMENTS, true)) {
                            $line     = str_repeat('-', mb_strlen($textContent));
                            $content .= $textContent . MailPart::NEWLINE . $line . MailPart::NEWLINE;
                        } elseif (in_array($activeTag, self::BLOCK_ELEMENTS, true)) {
                            $content .= $textContent;
                        } elseif (in_array($activeTag, self::INLINE_ELEMENTS, true)) {
                            $content .= $textContent;
                        }
                    }

                    if (in_array($activeTag, self::BREAK_ELEMENTS, true)) {
                        $content .= MailPart::NEWLINE;
                    }

                    if (in_array($activeTag, self::LINE_ELEMENTS, true)) {
                        $content .= str_repeat('-', self::DEFAULT_LINE_LENGTH) . MailPart::NEWLINE;
                    }

                    break;
                case XML_ELEMENT_NODE:
                    $newActiveTag = mb_strtolower($childNode->nodeName);

                    if (
                        in_array($activeTag, self::BLOCK_ELEMENTS, true) &&
                        in_array($newActiveTag, self::BLOCK_ELEMENTS, true)
                    ) {
                        $content .= MailPart::NEWLINE;
                    }

                    $activeTag = $newActiveTag;
                    if (! in_array($activeTag, self::SKIP_ELEMENTS, true)) {
                        $this->findTextNodesFromNode($childNode, $content, $activeTag);
                    }

                    break;
            }
        }
    }

    public function generatePlainTextMessage(): PlainTextMessage
    {
        $doc       = new DOMDocument('1.0');
        $activeTag = '';
        $content   = '';

        // Disable libxml's error message to prevent throwing on 'invalid' html or on tags it does not recognize.
        libxml_use_internal_errors(true);
        $doc->loadHTML("<?xml>{$this->getBody()}");

        $this->findTextNodesFromNode($doc, $content, $activeTag);
        return new PlainTextMessage(
            trim($content),
            $this->getTransferEncoding()
        );
    }
}
