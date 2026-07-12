<?php

use Laravel\Cashier\Console\WebhookCommand;
use Laravel\Cashier\Invoices\DompdfInvoiceRenderer;

return [

    /*
    |--------------------------------------------------------------------------
    | Stripe Keys
    |--------------------------------------------------------------------------
    */

    'key' => env('STRIPE_KEY'),
    'secret' => env('STRIPE_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Cashier Path
    |--------------------------------------------------------------------------
    */

    'path' => env('CASHIER_PATH', 'stripe'),

    /*
    |--------------------------------------------------------------------------
    | Stripe Webhooks
    |--------------------------------------------------------------------------
    */

    'webhook' => [
        'secret' => env('STRIPE_WEBHOOK_SECRET'),
        'tolerance' => env('STRIPE_WEBHOOK_TOLERANCE', 300),
        'events' => array_merge(WebhookCommand::DEFAULT_EVENTS, [
            'customer.subscription.trial_will_end',
            'invoice.payment_failed',
            'invoice.payment_action_required',
        ]),
    ],

    /*
    |--------------------------------------------------------------------------
    | Currency
    |--------------------------------------------------------------------------
    */

    'currency' => env('CASHIER_CURRENCY', 'eur'),
    'currency_locale' => env('CASHIER_CURRENCY_LOCALE', 'fr'),

    /*
    |--------------------------------------------------------------------------
    | Payment Confirmation Notification
    |--------------------------------------------------------------------------
    */

    'payment_notification' => env('CASHIER_PAYMENT_NOTIFICATION'),

    /*
    |--------------------------------------------------------------------------
    | Invoice Settings
    |--------------------------------------------------------------------------
    */

    'invoices' => [
        'renderer' => env('CASHIER_INVOICE_RENDERER', DompdfInvoiceRenderer::class),
        'options' => [
            'paper' => env('CASHIER_PAPER', 'A4'),
            'remote_enabled' => env('CASHIER_REMOTE_ENABLED', false),
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Stripe Logger
    |--------------------------------------------------------------------------
    */

    'logger' => env('CASHIER_LOGGER'),

    /*
    |--------------------------------------------------------------------------
    | LinsCarbon Subscription Plans
    |--------------------------------------------------------------------------
    |
    | Define the available subscription plans with their Stripe price IDs,
    | features, and limits. Each plan can have monthly and yearly variants.
    |
    */

    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'description' => 'Pour les petites entreprises débutant leur bilan carbone',
            'prices' => [
                'monthly' => env('STRIPE_PRICE_STARTER_MONTHLY', 'price_starter_monthly'),
                'yearly' => env('STRIPE_PRICE_STARTER_YEARLY', 'price_starter_yearly'),
            ],
            'amount' => [
                'monthly' => 49_00, // cents
                'yearly' => 490_00, // 2 months free
            ],
            'limits' => [
                'bank_connections' => 1,
                'users' => 3,
                'sites' => 1,
                'reports_monthly' => 5,
            ],
            'features' => [
                'basic_dashboard',
                'manual_entry',
                'bank_sync',
                'pdf_reports',
                'email_support',
            ],
        ],

        'professional' => [
            'name' => 'Professional',
            'description' => 'Pour les PME avec des besoins avancés',
            'prices' => [
                'monthly' => env('STRIPE_PRICE_PRO_MONTHLY', 'price_pro_monthly'),
                'yearly' => env('STRIPE_PRICE_PRO_YEARLY', 'price_pro_yearly'),
            ],
            'amount' => [
                'monthly' => 149_00,
                'yearly' => 1490_00,
            ],
            'limits' => [
                'bank_connections' => 5,
                'users' => 10,
                'sites' => 5,
                'reports_monthly' => 20,
            ],
            'features' => [
                'basic_dashboard',
                'advanced_dashboard',
                'manual_entry',
                'bank_sync',
                'csv_import',
                'fec_import',
                'pdf_reports',
                'detailed_reports',
                'api_access',
                'priority_support',
            ],
        ],

        'enterprise' => [
            'name' => 'Enterprise',
            'description' => 'Pour les grandes entreprises multi-sites',
            'prices' => [
                'monthly' => env('STRIPE_PRICE_ENTERPRISE_MONTHLY', 'price_enterprise_monthly'),
                'yearly' => env('STRIPE_PRICE_ENTERPRISE_YEARLY', 'price_enterprise_yearly'),
            ],
            'amount' => [
                'monthly' => 399_00,
                'yearly' => 3990_00,
            ],
            'limits' => [
                'bank_connections' => null, // unlimited
                'users' => null,
                'sites' => null,
                'reports_monthly' => null,
            ],
            'features' => [
                'basic_dashboard',
                'advanced_dashboard',
                'executive_dashboard',
                'manual_entry',
                'bank_sync',
                'csv_import',
                'excel_import',
                'fec_import',
                'pdf_reports',
                'detailed_reports',
                'methodology_reports',
                'api_access',
                'sso',
                'dedicated_support',
                'custom_integrations',
                'audit_logs',
                'data_export',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Trial Configuration
    |--------------------------------------------------------------------------
    */

    'trial' => [
        'enabled' => true,
        'days' => 14,
        'plan' => 'professional', // Trial gives access to professional features
        'require_card' => false,
    ],

    /*
    |--------------------------------------------------------------------------
    | Grace Period
    |--------------------------------------------------------------------------
    |
    | Number of days after subscription expires before restricting access
    |
    */

    'grace_period_days' => 3,

    /*
    |--------------------------------------------------------------------------
    | Billing Portal Settings
    |--------------------------------------------------------------------------
    */

    'portal' => [
        'return_url' => env('STRIPE_PORTAL_RETURN_URL', '/settings/billing'),
        'features' => [
            'payment_method_update' => true,
            'subscription_cancel' => true,
            'subscription_pause' => false,
            'invoice_history' => true,
        ],
    ],

];
