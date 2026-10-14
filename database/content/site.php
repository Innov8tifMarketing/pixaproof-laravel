<?php

/**
 * Values for the `site_content` global set (title "Site"): the navbar button, the footer and the
 * 404 page, as the site shipped them. `pixaproof:import-content` fills them only while the set is empty.
 */
return [
    'cta_label' => 'Request Demo',
    'cta_url' => '/contact',
    'footer_blurb' => 'Enterprise-grade image authenticity verification. Detect tampering, validate metadata, and establish chain of custody.',
    'parent_prefix' => 'A product of',
    'parent_name' => 'Innov8tif',
    'parent_url' => 'https://innov8tif.com',
    'navigation_heading' => 'Navigation',
    'contact_heading' => 'Contact',
    'legal_heading' => 'Legal',
    'contact_email' => 'sales@innov8tif.com',
    'website_label' => 'innov8tif.com',
    'website_url' => 'https://innov8tif.com',
    'legal_links' => [
        ['id' => 'legal-privacy', 'label' => 'Privacy Policy', 'url' => '/privacy'],
    ],
    'copyright' => 'Innov8tif Solutions Pte. Ltd. All rights reserved.',
    'bottom_links' => [
        ['id' => 'bottom-privacy', 'label' => 'Privacy', 'url' => '/privacy'],
        ['id' => 'bottom-contact', 'label' => 'Contact', 'url' => '/contact'],
    ],
    'not_found_eyebrow' => '404',
    'not_found_heading' => 'Page not found',
    'not_found_text' => 'The page you\'re looking for doesn\'t exist or has moved.',
    'not_found_button_label' => 'Back to home',
    'not_found_button_url' => '/',
];
