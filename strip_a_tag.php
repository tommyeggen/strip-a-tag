<?php

function strip_a_tag(string $input, string $tag, bool $removeTag): string {

    $document = new DOMDocument();

    // Load the given html without modifying with any doctype or html/body tags
    $loadFlags = LIBXML_HTML_NODEFDTD|LIBXML_HTML_NOIMPLIED;
    $document->loadHTML($input, $loadFlags);

    // Should we keep the doctype?
    $keepDoctype = $document->doctype !== null;

    // Should we keep the html-tag?
    $keepHtmlTag = $document->getElementsByTagName('html')->item(0) !== null;

    // Should we keep the body-tag?
    $keepBodyTag = $document->getElementsByTagName('body')->item(0) !== null;

    // Load the html again, this time with implied doctype and html/body-tag
    $document->loadHTML($input);

    // Find all related elements
    $elements = $document->getElementsByTagName($tag);

    // This works because we remove or replace the element in the loop.
    while($element = $elements->item(0)) {

        if($removeTag) {
            // Remove the element
            $element->parentNode->removeChild($element);
        } else {
            // Create a replacement node containing the textcontent of the element
            $replacementNode = $document->createTextNode($element->textContent);

            // Replace the element with our new textNode
            $element->parentNode->replaceChild($replacementNode, $element);
        }
    }


    // @see https://itecnote.com/tecnote/php-remove-parent-element-keep-all-inner-children-in-domdocument-with-savehtml/
    // Remove the generated doctype if it wasn't provided in $input
    if(!$keepDoctype) {
        $document->doctype->parentNode->removeChild($document->doctype);
    }


    // Remove the generated html-tag if it wasn't provided in $input
    if(!$keepHtmlTag) {
        $html = $document->getElementsByTagName("html")->item(0);
        $fragment = $document->createDocumentFragment();
        while ($html->childNodes->length > 0) {
            $fragment->appendChild($html->childNodes->item(0));
        }
        $html->parentNode->replaceChild($fragment, $html);
    }

    // Remove the generated body-tag if it wasn't provided in $input
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