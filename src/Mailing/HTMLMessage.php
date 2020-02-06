<?php

declare(strict_types=1);

namespace Fugue\Mailing;

use Fugue\HTTP\Response;
use DOMDocument;
use DOMNode;

use function libxml_use_internal_errors;
use function mb_strtolower;
use function str_repeat;
use function mb_strlen;
use function in_array;
use function trim;

final class HTMLMessage extends MailPart
{
    /**
     * @var int Default line length for HR elements.
     */
    private const DEFAULT_LINE_LENGTH = 32;

    /** @var string[] */
    private $blockElements = ['blockquote', 'footer', 'header', 'aside', 'code', 'div', 'p', 'h1', 'h2', 'h3', 'h4', 'h5', 'h6', 'hr'];

    /** @var string[] */
    private $headerElements = ['h1', 'h2', 'h3', 'h4', 'h5', 'h6'];

    /** @var string[] */
    private $inlineElements = ['font', 'span', 'b', 'i', 'u', 's'];

    /** @var string[] */
    private $breakElements  = ['br', 'wbr'];

    /** @var string[] */
    private $skipElements   = ['head'];

    /** @var string[] */
    private $lineElements   = ['hr'];

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
        foreach ($node->childNodes as $childNode) {
            switch ($childNode->nodeType) {
                case XML_TEXT_NODE:
                    $textContent = (string)$childNode->nodeValue;

                    if ($textContent !== '') {
                        if (in_array($activeTag, $this->headerElements, true)) {
                            $line     = str_repeat('-', mb_strlen($textContent));
                            $content .= $textContent . MailPart::NEWLINE . $line . MailPart::NEWLINE;
                        } elseif (in_array($activeTag, $this->blockElements, true)) {
                            $content .= $textContent;
                        } elseif (in_array($activeTag, $this->inlineElements, true)) {
                            $content .= $textContent;
                        }
                    }

                    if (in_array($activeTag, $this->breakElements, true)) {
                        $content .= MailPart::NEWLINE;
                    }

                    if (in_array($activeTag, $this->lineElements, true)) {
                        $content .= str_repeat('-', self::DEFAULT_LINE_LENGTH) . MailPart::NEWLINE;
                    }

                    break;
                case XML_ELEMENT_NODE:
                    $newActiveTag = mb_strtolower($childNode->nodeName);

                    if (
                        in_array($activeTag, $this->blockElements, true) &&
                        in_array($newActiveTag, $this->blockElements, true)
                    ) {
                        $content .= MailPart::NEWLINE;
                    }

                    $activeTag = $newActiveTag;
                    if (! in_array($activeTag, $this->skipElements, true)) {
                        $this->findTextNodesFromNode($childNode, $content, $activeTag);
                    }

                    break;
            }
        }
    }

    public function getTransferEncoding(): string
    {
        return MailPart::TRANSFER_ENCODING_QUOTED_PRINTABLE;
    }

    public function getContentType(): string
    {
        return Response::CONTENT_TYPE_HTML;
    }

    /**
     * Generates a plain text version of this HTML message part.
     *
     * @return TextMessage The plain text version of this HTML message.
     */
    public function generateTextPart(): TextMessage
    {
        $doc       = new DOMDocument('1.0');
        $activeTag = '';
        $content   = '';

        // Disable libxml's error message to prevent throwing on 'invalid' html or on tags it does not recognize.
        libxml_use_internal_errors(true);
        $doc->loadHTML("<?xml>{$this->getBody()}");

        $this->findTextNodesFromNode($doc, $content, $activeTag);
        return new TextMessage(trim($content));
    }
}
