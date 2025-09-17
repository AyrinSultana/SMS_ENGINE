<?php

return [
    /*
    |--------------------------------------------------------------------------
    | SMS Gateway Configuration
    |--------------------------------------------------------------------------
    |
    | This file contains the configuration settings for SMS gateways.
    |
    */
    
    // Default SMS gateway to use
    'default' => env('SMS_GATEWAY', 'log'),
    
    // Available SMS gateways
    'gateways' => [
        'log' => [
            'driver' => 'log',
            'channel' => env('SMS_LOG_CHANNEL', 'stack'),
        ],
        
        // Add more gateway configurations as needed
        // For example:
        // 'twilio' => [
        //     'driver' => 'twilio',
        //     'account_sid' => env('TWILIO_ACCOUNT_SID'),
        //     'auth_token' => env('TWILIO_AUTH_TOKEN'),
        //     'from' => env('TWILIO_FROM_NUMBER'),
        // ],
    ],
    
    /*
    |--------------------------------------------------------------------------
    | SMS Templates Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for SMS templates.
    |
    */
    
    'templates' => [
        // Whether to require approval for templates before they can be used
        'require_approval' => env('SMS_REQUIRE_TEMPLATE_APPROVAL', true),
        
        // Storage disk to use for template files
        'storage_disk' => env('SMS_TEMPLATE_STORAGE_DISK', 'public'),
        
        // Path for template files within the storage disk
        'storage_path' => env('SMS_TEMPLATE_STORAGE_PATH', 'templates'),
    ],
    
    /*
    |--------------------------------------------------------------------------
    | SMS Queue Configuration
    |--------------------------------------------------------------------------
    |
    | Configuration settings for SMS queue processing.
    |
    */
    
    'queue' => [
        // Whether to use Laravel's queue system for sending SMS
        'enabled' => env('SMS_USE_QUEUE', true),
        
        // The queue to use for SMS processing
        'queue_name' => env('SMS_QUEUE_NAME', 'default'),
        
        // Batch size for processing SMS in bulk
        'batch_size' => env('SMS_BATCH_SIZE', 100),
    ],
];
