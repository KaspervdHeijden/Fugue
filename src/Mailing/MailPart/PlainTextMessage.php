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

final class PlainTextMessage extends TextMessage
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
            Response::CONTENT_TYPE_PLAINTEXT,
            $transferEncoding
        );
    }

    public static function fromHtml(
        string $html,
        string $transferEncoding = self::TRANSFER_ENCODING_QUOTED_PRINTABLE
    ): self {
        $document = new DOMDocument('1.0');
        $former   = libxml_use_internal_errors(true);
        $content  = '';

        // Disable the error message from libxml to prevent throwing
        // on 'invalid' html or on tags it does not recognize.
        $document->loadHTML("<?xml>{$html}");
        libxml_use_internal_errors($former);

        ($generateContent = function (
            DOMNode $node,
            string &$content,
            string $activeTag
        ) use (&$generateContent): void {
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
                            $generateContent($childNode, $content, $activeTag);
                        }

                        break;
                }
            }
        })($document, $content, '');

        return new static(trim($content), $transferEncoding);
    }
}
