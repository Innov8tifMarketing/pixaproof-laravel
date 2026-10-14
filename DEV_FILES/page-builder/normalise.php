<?php

use Dom\Element;
use Dom\HTMLDocument;
use Dom\Node;
use Dom\Text;

/**
 * Prints a page's DOM one node per line, for diffing renders of the same page:
 * entities decoded, whitespace collapsed, attributes sorted, and the values that
 * change on every request (CSRF token, CSP nonces) blanked.
 *
 * Usage: php normalise.php page.html > page.txt
 */
$html = (string) file_get_contents($argv[1]);
$document = HTMLDocument::createFromString($html, LIBXML_NOERROR);

$squash = fn (string $text): string => trim((string) preg_replace('/\s+/u', ' ', $text));

$walk = function (Node $node, int $depth) use (&$walk, $squash): void {
    $indent = str_repeat('  ', $depth);

    if ($node instanceof Element) {
        $attributes = [];

        foreach ($node->attributes as $attribute) {
            $value = $squash($attribute->value);

            if (in_array($attribute->name, ['nonce'], true)) {
                $value = '…';
            }

            if ($node->tagName === 'META' && $node->getAttribute('name') === 'csrf-token' && $attribute->name === 'content') {
                $value = '…';
            }

            $attributes[$attribute->name] = $value;
        }

        ksort($attributes);

        echo $indent.'<'.strtolower($node->tagName);

        foreach ($attributes as $name => $value) {
            echo ' '.$name.'="'.$value.'"';
        }

        echo ">\n";

        /*
         * A <template>'s markup sits in its content fragment, which PHP's DOM doesn't expose;
         * it is parsed on its own instead.
         */
        $children = $node->tagName === 'TEMPLATE'
            ? HTMLDocument::createFromString('<!DOCTYPE html><body>'.$node->innerHTML, LIBXML_NOERROR)->body->childNodes
            : $node->childNodes;

        foreach ($children as $child) {
            $walk($child, $depth + 1);
        }

        return;
    }

    if ($node instanceof Text) {
        $text = $squash($node->textContent);

        if ($text !== '') {
            echo $indent.'"'.$text."\"\n";
        }
    }
};

$walk($document->documentElement, 0);
