<?php

/**
 * The privacy page's sections (the `blocks` Replicator). `pixaproof:import-content` fills a page's
 * blocks only while they are empty, and puts the page's existing markdown `content` (from before
 * the page builder) into the legal text block in place of this copy.
 */
return [
    [
        'id' => 'privacy-legal-text',
        'type' => 'legal_text',
        'enabled' => true,
        'content' => <<<'MARKDOWN'
            ## Introduction

            PixaProof ("we", "our", or "us") is committed to protecting your privacy. This Privacy Policy explains how we collect, use, and safeguard your information when you use our mobile application.

            ## Information We Collect

            When you use PixaProof, we may collect:

            - Images you submit for verification (processed in real-time, not stored)
            - Device information for app functionality
            - Usage analytics to improve our service

            ## How We Use Your Information

            We use the information we collect to:

            - Provide photo authenticity verification services
            - Improve and optimize our application
            - Respond to your inquiries and support requests

            ## Data Security

            We implement appropriate security measures to protect your information. Images submitted for verification are processed in real-time and are not stored on our servers after analysis is complete.

            ## Contact Us

            If you have questions about this Privacy Policy, please [contact us](/contact).
            MARKDOWN,
        'anchor' => null,
    ],
];
