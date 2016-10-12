<?php

return [
        'directories' => [
            'scanned' => [
                app_path(),
            ],
            'backup' => storage_path('sentinel/backup'),
            'logs' => storage_path('sentinel/logs'),
            'quarantine' => storage_path('sentinel/quarantine'),
        ],
        'ignored' => [
            'directories' => [
                storage_path(),
            ],
            'files' => [
                '.svn',
                '.git',
                '.gitignore'
            ]
        ],
        'compression' => [
            'type' => 'zip',
            'include_subfolders' => true,
            'params' => []
        ],
        'inspection' => [
            'checksum_storage' => [
                'type' => 'file',
                'directory' => storage_path('sentinel/inspection'),
            ],
            'self_inspection' => true,
            'on_detection' => null,
            'caching'      => false,
            'cache_life'   => 1209600,
        ],
        'quarantine' => [
            'maxlife' => 604800,
            'gc' => 500
        ],
        'autoresponder' => [
            'driver' => 'email',
            'enabled' => true,
            'project_name' => 'Laravel APP',
            'email' => [
                'sender'    => 'sender@domain',
                'recipient' => 'recipient@domain',
                'mime_type' => 'text/html',
            ],
            'sms' => [
                'recipient' => 'your phone number',
            ],
        ]
];
