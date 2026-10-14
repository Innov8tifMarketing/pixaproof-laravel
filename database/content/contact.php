<?php

/**
 * The contact page's sections (the `blocks` Replicator), as the site shipped them.
 * Read by `pixaproof:import-content`, which fills a page's blocks only while they are empty.
 */
return [
    [
        'id' => 'contact-hero',
        'type' => 'contact_hero',
        'enabled' => true,
        'badge' => 'Contact Sales',
        'heading' => 'Talk to Our Team',
        'text' => 'Book a demo, discuss your use case, or explore integration options with our solutions team.',
        'email' => 'sales@innov8tif.com',
        'hours' => 'Mon–Fri, 9 AM – 5 PM (GMT+8)',
        'response_time' => 'We respond within 3 working days',
        'anchor' => null,
    ],
    [
        'id' => 'contact-offices',
        'type' => 'offices',
        'enabled' => true,
        'heading' => 'Our Offices',
        'intro' => 'Innov8tif Solutions serves 500+ clients across 10+ countries from our regional offices.',
        'offices' => [
            [
                'flag' => '🇲🇾',
                'name' => 'Malaysia (HQ)',
                'address' => "Innov8tif Solutions Sdn Bhd\nL9-2, Wisma Conlay,\n1, Jalan USJ 10/1,\n47620 Subang Jaya, Selangor",
            ],
            [
                'flag' => '🇸🇬',
                'name' => 'Singapore',
                'address' => "Innov8tif Solutions Pte Ltd\n120 Robinson Road, #12-01,\nSingapore 068913",
            ],
            [
                'flag' => '🇮🇩',
                'name' => 'Indonesia',
                'address' => "PT. Innov8tif Karta Solusi\nXin Building, Jl. Kapten Tendean No.52,\nBandung City, West Java 40141",
            ],
            [
                'flag' => '🇰🇭',
                'name' => 'Cambodia',
                'address' => "Innov8tif Solutions Co., Ltd.\nNo. 206D, Street Preah Norodom,\nSangkat Tonle Bassac, Phnom Penh",
            ],
            [
                'flag' => '🇵🇭',
                'name' => 'Philippines',
                'address' => "7F, Finman Centre Building,\n131 Tordesillas, Salcedo Village,\nMakati City",
            ],
        ],
        'footnote' => 'Built by the team behind 10M+ identity verifications across ASEAN — with ISO 27001:2022 certification and 3 granted patents.',
        'anchor' => null,
    ],
];
