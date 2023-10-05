<?php

function strip_a_tag(string $input, string $tag, bool $removeTag): string {

    $document = new DOMDocument();

    $loadFlags = LIBXML_HTML_NODEFDTD|LIBXML_HTML_NOIMPLIED;
    $document->loadHTML($input, $loadFlags);
    $keepDoctype = $document->doctype !== null;
    $keepHtmlTag = $document->getElementsByTagName('html')->item(0) !== null;
    $keepBodyTag = $document->getElementsByTagName('body')->item(0) !== null;

    $document->loadHTML($input);

    $elements = $document->getElementsByTagName($tag);

    while($elements->item(0)) {

        $element = $elements->item(0);
        if($removeTag) {
            $element->parentNode->removeChild($elements->item(0));
        } else {
            $replacementNode = $document->createTextNode($element->textContent);
            $element->parentNode->replaceChild($replacementNode, $element);
        }
    }

    if(!$keepDoctype) {
        $document->doctype->parentNode->removeChild($document->doctype);
    }

    if(!$keepHtmlTag) {
        $html = $document->getElementsByTagName("html")->item(0);
        $fragment = $document->createDocumentFragment();
        while ($html->childNodes->length > 0) {
            $fragment->appendChild($html->childNodes->item(0));
        }
        $html->parentNode->replaceChild($fragment, $html);
    }

    if(!$keepBodyTag) {
        $body = $document->getElementsByTagName("body")->item(0);
        $fragment = $document->createDocumentFragment();
        while ($body->childNodes->length > 0) {
            $fragment->appendChild($body->childNodes->item(0));
        }
        $body->parentNode->replaceChild($fragment, $body);
    }


    return $document->saveHTML();

}


$strContent = file_get_contents('content.html');

$newContent = strip_a_tag($strContent, 'a', false);

echo $newContent;