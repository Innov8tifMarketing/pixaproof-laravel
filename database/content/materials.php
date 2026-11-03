<?php

/**
 * The downloadable materials (the `downloadable_materials` collection), in order. Read by
 * `pixaproof:import-content`, which adds them only while the collection is empty. They start as
 * drafts without a file: an editor uploads each PDF and publishes it. The IDs are fixed so the
 * homepage's industry tabs (database/content/home.php) can point at the use cases.
 */
return [
    [
        'id' => '9fbbcd43-a0e8-4cfe-b851-64de9513e139',
        'slug' => 'pixaproof-brochure',
        'title' => 'PixaProof Brochure',
        'kind' => 'Brochure',
        'summary' => 'How PixaProof secures photos at the moment of capture, the fraud it stops, and how it fits into your apps.',
        'video_url' => null,
    ],
    [
        'id' => '533b2100-21f1-43de-b3ac-27cac48b6aba',
        'slug' => 'property-inspection-use-case',
        'title' => 'Property Inspection Use Case',
        'kind' => 'Use case',
        'summary' => 'How property owners verify a rental\'s condition remotely with authenticated, live-captured photos.',
        'video_url' => 'https://youtu.be/CA2nlNLwCjM',
    ],
    [
        'id' => 'b3b501ae-385f-4f59-9769-dffc96ca23c7',
        'slug' => 'insurance-claims-use-case',
        'title' => 'Insurance Claims Use Case',
        'kind' => 'Use case',
        'summary' => 'How insurers verify claim photos at capture and flag AI-generated or altered damage evidence.',
        'video_url' => 'https://youtu.be/fDr_xwOcBqU',
    ],
];
