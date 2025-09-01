<?php

return [
    'create_link_success' => [
        'id' => 'abc123def456',
        'title' => 'Test Link',
        'slashtag' => 'test',
        'destination' => 'https://example.com',
        'domain' => [
            'id' => 'domain123',
            'fullName' => 'rebrand.ly',
        ],
        'shortUrl' => 'https://rebrand.ly/test',
        'tags' => ['tag1', 'tag2'],
        'createdAt' => '2023-01-01T12:00:00Z',
        'updatedAt' => '2023-01-01T12:00:00Z',
        'clicks' => 0,
        'favourite' => false,
        'description' => 'Test link description',
    ],

    'get_link_success' => [
        'id' => 'abc123def456',
        'title' => 'Existing Link',
        'slashtag' => 'existing',
        'destination' => 'https://existing.com',
        'domain' => [
            'id' => 'domain123',
            'fullName' => 'rebrand.ly',
        ],
        'shortUrl' => 'https://rebrand.ly/existing',
        'tags' => ['existing'],
        'createdAt' => '2023-01-01T10:00:00Z',
        'updatedAt' => '2023-01-02T11:00:00Z',
        'clicks' => 42,
        'favourite' => true,
        'description' => 'Existing link description',
    ],

    'update_link_success' => [
        'id' => 'abc123def456',
        'title' => 'Updated Link Title',
        'slashtag' => 'existing',
        'destination' => 'https://existing.com',
        'domain' => [
            'id' => 'domain123',
            'fullName' => 'rebrand.ly',
        ],
        'shortUrl' => 'https://rebrand.ly/existing',
        'tags' => ['updated', 'link'],
        'createdAt' => '2023-01-01T10:00:00Z',
        'updatedAt' => '2023-01-03T14:00:00Z',
        'clicks' => 42,
        'favourite' => false,
        'description' => 'Updated link description',
    ],

    'list_links_success' => [
        [
            'id' => 'abc123def456',
            'title' => 'First Link',
            'slashtag' => 'first',
            'destination' => 'https://first.com',
            'domain' => [
                'id' => 'domain123',
                'fullName' => 'rebrand.ly',
            ],
            'shortUrl' => 'https://rebrand.ly/first',
            'tags' => ['first'],
            'createdAt' => '2023-01-01T10:00:00Z',
            'updatedAt' => '2023-01-01T10:00:00Z',
            'clicks' => 10,
            'favourite' => false,
        ],
        [
            'id' => 'def456ghi789',
            'title' => 'Second Link',
            'slashtag' => 'second',
            'destination' => 'https://second.com',
            'domain' => [
                'id' => 'domain123',
                'fullName' => 'rebrand.ly',
            ],
            'shortUrl' => 'https://rebrand.ly/second',
            'tags' => ['second'],
            'createdAt' => '2023-01-02T10:00:00Z',
            'updatedAt' => '2023-01-02T10:00:00Z',
            'clicks' => 5,
            'favourite' => true,
        ],
    ],

    'error_unauthorized' => [
        'message' => 'Unauthorized',
        'code' => 401,
        'errors' => [
            'apikey' => 'Invalid API key provided',
        ],
    ],

    'error_not_found' => [
        'message' => 'Link not found',
        'code' => 404,
        'errors' => [
            'id' => 'Link with the specified ID does not exist',
        ],
    ],

    'error_validation' => [
        'message' => 'Validation failed',
        'code' => 422,
        'errors' => [
            'destination' => 'The destination field is required',
            'slashtag' => 'The slashtag has already been taken',
        ],
    ],

    'account_details' => [
        'id' => 'user123abc',
        'username' => 'testuser',
        'email' => 'test@example.com',
        'fullName' => 'Test User',
        'avatarUrl' => 'https://secure.gravatar.com/avatar/abc123',
        'createdAt' => '2023-01-01T10:00:00Z',
        'subscription' => [
            'plan' => 'free',
            'status' => 'active',
            'limits' => [
                'links' => 1000,
                'clicks' => 10000,
            ],
        ],
        'usage' => [
            'links' => 150,
            'clicks' => 2500,
        ],
    ],

    'create_tag_success' => [
        'id' => 'tag123def',
        'name' => 'Marketing',
        'color' => '#ff6b35',
        'createdAt' => '2023-01-01T12:00:00Z',
        'updatedAt' => '2023-01-01T12:00:00Z',
        'linksCount' => 0,
    ],

    'get_tag_success' => [
        'id' => 'tag123def',
        'name' => 'Marketing',
        'color' => '#ff6b35',
        'createdAt' => '2023-01-01T12:00:00Z',
        'updatedAt' => '2023-01-02T14:30:00Z',
        'linksCount' => 25,
    ],

    'update_tag_success' => [
        'id' => 'tag123def',
        'name' => 'Updated Marketing',
        'color' => '#00ff00',
        'createdAt' => '2023-01-01T12:00:00Z',
        'updatedAt' => '2023-01-03T16:00:00Z',
        'linksCount' => 30,
    ],

    'list_tags_success' => [
        [
            'id' => 'tag123def',
            'name' => 'Marketing',
            'color' => '#ff6b35',
            'createdAt' => '2023-01-01T12:00:00Z',
            'updatedAt' => '2023-01-01T12:00:00Z',
            'linksCount' => 25,
        ],
        [
            'id' => 'tag456ghi',
            'name' => 'Campaign',
            'color' => '#4285f4',
            'createdAt' => '2023-01-02T10:00:00Z',
            'updatedAt' => '2023-01-02T10:00:00Z',
            'linksCount' => 15,
        ],
        [
            'id' => 'tag789jkl',
            'name' => 'Social Media',
            'color' => '#34a853',
            'createdAt' => '2023-01-03T08:00:00Z',
            'updatedAt' => '2023-01-03T08:00:00Z',
            'linksCount' => 8,
        ],
    ],

    'link_tags_success' => [
        [
            'id' => 'tag123def',
            'name' => 'Marketing',
            'color' => '#ff6b35',
        ],
        [
            'id' => 'tag456ghi',
            'name' => 'Campaign',
            'color' => '#4285f4',
        ],
    ],

    'tag_links_success' => [
        [
            'id' => 'link123abc',
            'title' => 'Marketing Campaign Link',
            'slashtag' => 'marketing-camp',
            'destination' => 'https://marketing-campaign.com',
            'domain' => [
                'id' => 'domain123',
                'fullName' => 'rebrand.ly',
            ],
            'shortUrl' => 'https://rebrand.ly/marketing-camp',
            'tags' => ['marketing'],
            'createdAt' => '2023-01-01T10:00:00Z',
            'updatedAt' => '2023-01-01T10:00:00Z',
            'clicks' => 150,
            'favourite' => true,
        ],
        [
            'id' => 'link456def',
            'title' => 'Product Launch',
            'slashtag' => 'product-launch',
            'destination' => 'https://product-launch.com',
            'domain' => [
                'id' => 'domain123',
                'fullName' => 'rebrand.ly',
            ],
            'shortUrl' => 'https://rebrand.ly/product-launch',
            'tags' => ['marketing', 'product'],
            'createdAt' => '2023-01-02T14:00:00Z',
            'updatedAt' => '2023-01-02T14:00:00Z',
            'clicks' => 89,
            'favourite' => false,
        ],
        [
            'id' => 'link789ghi',
            'title' => 'Newsletter Signup',
            'slashtag' => 'newsletter',
            'destination' => 'https://newsletter-signup.com',
            'domain' => [
                'id' => 'domain123',
                'fullName' => 'rebrand.ly',
            ],
            'shortUrl' => 'https://rebrand.ly/newsletter',
            'tags' => ['marketing', 'newsletter'],
            'createdAt' => '2023-01-03T09:00:00Z',
            'updatedAt' => '2023-01-03T09:00:00Z',
            'clicks' => 64,
            'favourite' => true,
        ],
    ],
];