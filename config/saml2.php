<?php

/**
 * SAML2 / SSO Configuration for Carbex
 *
 * Supports multiple Identity Providers (IdP) per organization.
 * Compatible with Azure AD, Okta, Google Workspace, OneLogin, etc.
 *
 * Package: aacotroneo/laravel-saml2 or similar
 */

return [

    /*
    |--------------------------------------------------------------------------
    | SAML2 Enabled
    |--------------------------------------------------------------------------
    */

    'enabled' => env('SAML2_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Service Provider (SP) Settings
    |--------------------------------------------------------------------------
    |
    | These are the settings for Carbex as a Service Provider
    |
    */

    'sp' => [
        'entity_id' => env('SAML2_SP_ENTITY_ID', env('APP_URL') . '/saml2/metadata'),

        'assertion_consumer_service' => [
            'url' => env('APP_URL') . '/saml2/acs',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-POST',
        ],

        'single_logout_service' => [
            'url' => env('APP_URL') . '/saml2/sls',
            'binding' => 'urn:oasis:names:tc:SAML:2.0:bindings:HTTP-Redirect',
        ],

        'name_id_format' => 'urn:oasis:names:tc:SAML:1.1:nameid-format:emailAddress',

        // X.509 certificate and private key for SP (optional but recommended)
        'x509cert' => env('SAML2_SP_CERT', ''),
        'private_key' => env('SAML2_SP_KEY', ''),
    ],

    /*
    |--------------------------------------------------------------------------
    | Security Settings
    |--------------------------------------------------------------------------
    */

    'security' => [
        'name_id_encrypted' => false,
        'authn_requests_signed' => env('SAML2_SIGN_REQUESTS', false),
        'logout_requests_signed' => env('SAML2_SIGN_REQUESTS', false),
        'logout_responses_signed' => env('SAML2_SIGN_REQUESTS', false),
        'want_messages_signed' => env('SAML2_WANT_SIGNED', true),
        'want_assertions_encrypted' => false,
        'want_assertions_signed' => env('SAML2_WANT_SIGNED', true),
        'want_name_id' => true,
        'want_name_id_encrypted' => false,
        'requested_authn_context' => false,
        'sign_metadata' => env('SAML2_SIGN_METADATA', false),
        'signature_algorithm' => 'http://www.w3.org/2001/04/xmldsig-more#rsa-sha256',
        'digest_algorithm' => 'http://www.w3.org/2001/04/xmlenc#sha256',
    ],

    /*
    |--------------------------------------------------------------------------
    | Attribute Mapping
    |--------------------------------------------------------------------------
    |
    | Map SAML attributes to user fields
    |
    */

    'attribute_mapping' => [
        'email' => [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/emailaddress',
            'email',
            'Email',
            'mail',
        ],
        'name' => [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/name',
            'http://schemas.microsoft.com/identity/claims/displayname',
            'displayName',
            'name',
        ],
        'first_name' => [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/givenname',
            'givenName',
            'firstName',
        ],
        'last_name' => [
            'http://schemas.xmlsoap.org/ws/2005/05/identity/claims/surname',
            'surname',
            'lastName',
        ],
        'groups' => [
            'http://schemas.microsoft.com/ws/2008/06/identity/claims/groups',
            'groups',
            'memberOf',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Default IdP Templates
    |--------------------------------------------------------------------------
    |
    | Pre-configured settings for common Identity Providers
    |
    */

    'idp_templates' => [

        'azure_ad' => [
            'name' => 'Microsoft Azure AD',
            'icon' => 'microsoft',
            'entity_id_pattern' => 'https://sts.windows.net/{tenant_id}/',
            'sso_url_pattern' => 'https://login.microsoftonline.com/{tenant_id}/saml2',
            'slo_url_pattern' => 'https://login.microsoftonline.com/{tenant_id}/saml2',
            'certificate_url' => 'https://login.microsoftonline.com/{tenant_id}/federationmetadata/2007-06/federationmetadata.xml',
            'required_fields' => ['tenant_id'],
            'documentation_url' => 'https://docs.microsoft.com/azure/active-directory/saas-apps/tutorial-list',
        ],

        'okta' => [
            'name' => 'Okta',
            'icon' => 'okta',
            'entity_id_pattern' => 'http://www.okta.com/{app_id}',
            'sso_url_pattern' => 'https://{domain}.okta.com/app/{app_id}/sso/saml',
            'slo_url_pattern' => 'https://{domain}.okta.com/app/{app_id}/slo/saml',
            'required_fields' => ['domain', 'app_id'],
            'documentation_url' => 'https://developer.okta.com/docs/guides/build-sso-integration/saml2/main/',
        ],

        'google_workspace' => [
            'name' => 'Google Workspace',
            'icon' => 'google',
            'entity_id_pattern' => 'https://accounts.google.com/o/saml2?idpid={idp_id}',
            'sso_url_pattern' => 'https://accounts.google.com/o/saml2/idp?idpid={idp_id}',
            'required_fields' => ['idp_id'],
            'documentation_url' => 'https://support.google.com/a/answer/6087519',
        ],

        'onelogin' => [
            'name' => 'OneLogin',
            'icon' => 'onelogin',
            'entity_id_pattern' => 'https://app.onelogin.com/saml/metadata/{app_id}',
            'sso_url_pattern' => 'https://{subdomain}.onelogin.com/trust/saml2/http-post/sso/{app_id}',
            'slo_url_pattern' => 'https://{subdomain}.onelogin.com/trust/saml2/http-redirect/slo/{app_id}',
            'required_fields' => ['subdomain', 'app_id'],
            'documentation_url' => 'https://developers.onelogin.com/saml',
        ],

        'adfs' => [
            'name' => 'Microsoft ADFS',
            'icon' => 'microsoft',
            'entity_id_pattern' => 'http://{adfs_host}/adfs/services/trust',
            'sso_url_pattern' => 'https://{adfs_host}/adfs/ls/',
            'slo_url_pattern' => 'https://{adfs_host}/adfs/ls/',
            'required_fields' => ['adfs_host'],
            'documentation_url' => 'https://docs.microsoft.com/windows-server/identity/ad-fs/overview/ad-fs-openid-connect-oauth-flows-scenarios',
        ],

        'custom' => [
            'name' => 'Custom SAML IdP',
            'icon' => 'key',
            'required_fields' => ['entity_id', 'sso_url', 'certificate'],
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Role Mapping
    |--------------------------------------------------------------------------
    |
    | Map IdP groups/roles to Carbex roles
    |
    */

    'role_mapping' => [
        'default_role' => 'member',
        'admin_groups' => [
            'Carbex-Admins',
            'Application-Admins',
            'Admin',
        ],
        'manager_groups' => [
            'Carbex-Managers',
            'Managers',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Session Settings
    |--------------------------------------------------------------------------
    */

    'session' => [
        'strict_mode' => true,
        'reauth_every' => 0, // hours, 0 = never
    ],

    /*
    |--------------------------------------------------------------------------
    | Debug Settings
    |--------------------------------------------------------------------------
    */

    'debug' => env('SAML2_DEBUG', false),

];
