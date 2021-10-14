<?php

return [


    /**
     * Enter the fully-qualified path to the class that contains the scripts used to process incoming ODK submissions
     * See Stats4sd\KoboLink\Services\DatamapService::class for a basic example
     */
    'process_scripts_class' => Stats4sd\KoboLink\Services\DatamapService::class,

    'echo' => false,

    /**
     * Configuration for the link to KoBoToolbox
     */
    'kobo' => [
        /** The root of the server */
        'endpoint' => env('KOBO_ENDPOINT', 'https://kf.kobotoolbox.org'),

        /** The full API endpoint */
        'endpoint_v2' => env('KOBO_ENDPOINT').'/api/v2', 'https://kf.kobotoolbox.org/api/v2',

        /** The url for the legacy API. This is still required for some functionality. */
        'old_endpoint' => env('KOBO_OLD_ENDPOINT', 'https://kc.kobotoolbox.org'),

        /**
         * Username and password for the main platform account
         * The platform requires a 'primary' user account on the KoboToolbox server to manage deployments of ODK forms.
         * This account will *own* every form published by the platform.
         *
         * We recommend not using an account that individuals typically use or have access to, to avoid mismatch between forms deployed and forms in the Laravel database.
         */
        'username' => env('KOBO_USERNAME', ''),
        'password' => env('KOBO_PASSWORD', ''),

        'test_form' => env('KOBO_TEST_FORM', null),
    ],

    'xlsforms' => [
        'storage_disk' => config('filesystems.default'),
    ],

    'media' => [
        'storage_disk' => config('filesystems.default'),
    ]
];
