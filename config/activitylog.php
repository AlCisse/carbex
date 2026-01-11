<?php

return [
    /*
     * If set to false, no activities will be saved to the database.
     */
    'enabled' => env('ACTIVITY_LOGGER_ENABLED', true),

    /*
     * When the clean-command is executed, all recording activities older than
     * the number of days specified here will be deleted.
     */
    'delete_records_older_than_days' => 365,

    /*
     * If no driver is set, the default driver will be used.
     */
    'default_log_name' => 'default',

    /*
     * You can specify an auth driver here that gets user models.
     * When this is null we'll use the default Laravel auth driver.
     */
    'default_auth_driver' => null,

    /*
     * If set to true, the subject returns soft deleted models.
     */
    'subject_returns_soft_deleted_models' => false,

    /*
     * This model will be used to log activity.
     * It should implement the Spatie\Activitylog\Contracts\Activity interface.
     */
    'activity_model' => \Spatie\Activitylog\Models\Activity::class,

    /*
     * This is the name of the table that will be created by the migration and
     * used by the Activity model shipped with this package.
     */
    'table_name' => 'activity_log',

    /*
     * This is the database connection that will be used by the migration and
     * the Activity model shipped with this package.
     */
    'database_connection' => env('ACTIVITY_LOGGER_DB_CONNECTION'),

    /*
     * Carbex-specific activity log settings
     */
    'carbex' => [
        /*
         * Log names for different types of activities
         */
        'log_names' => [
            'auth' => 'auth',
            'organization' => 'organization',
            'banking' => 'banking',
            'transactions' => 'transactions',
            'emissions' => 'emissions',
            'reports' => 'reports',
            'settings' => 'settings',
        ],

        /*
         * Events to log for each model
         */
        'logged_events' => [
            'Transaction' => ['created', 'updated', 'deleted'],
            'EmissionRecord' => ['created', 'updated', 'deleted'],
            'BankConnection' => ['created', 'deleted'],
            'Organization' => ['updated'],
            'User' => ['created', 'updated', 'deleted'],
            'Report' => ['created', 'deleted'],
        ],

        /*
         * Attributes to exclude from logging (sensitive data)
         */
        'excluded_attributes' => [
            'password',
            'remember_token',
            'access_token',
            'refresh_token',
            'two_factor_secret',
            'two_factor_recovery_codes',
        ],
    ],
];
