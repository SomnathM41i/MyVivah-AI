<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Services\PlatformAccountService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\PlatformAccountService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-99e3b8f9dd6d7a5785a19bfe4c10712cfa0ce570353649e68cf311b6717af0dc',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\PlatformAccountService',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Services/PlatformAccountService.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\PlatformAccountService',
    'shortName' => 'PlatformAccountService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Phase 5A — self-service platform account registration.
 *
 * Creates the full account root atomically: `users` + `platforms` + the
 * platform-owner `platform_admins` row (registration-flow.md §2/§4). Credentials
 * are validated + hashed upstream (FormRequest); this service is the single
 * transactional entry-point so every registration follows the same rules.
 *
 * Account/password emails (verification) are sent by the controller after this
 * service commits — never inside the transaction, so a failed send never rolls
 * back a valid account.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 23,
    'endLine' => 87,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
      'USER_STATUS_ACTIVE' => 
      array (
        'declaringClassName' => 'App\\Services\\PlatformAccountService',
        'implementingClassName' => 'App\\Services\\PlatformAccountService',
        'name' => 'USER_STATUS_ACTIVE',
        'modifiers' => 4,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'active\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 50,
            'startFilePos' => 885,
            'endTokenPos' => 50,
            'endFilePos' => 892,
          ),
        ),
        'docComment' => '/** user.status for newly registered, verified-by-email accounts. */',
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'register' => 
      array (
        'name' => 'register',
        'parameters' => 
        array (
          'data' => 
          array (
            'name' => 'data',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 30,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'array',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Register a new platform owner + their first platform.
 *
 * @param  array{name: string, email: string, password: string, platform_name: string, website_url?: ?string}  $data
 * @return array{0: User, 1: Platform}
 */',
        'startLine' => 34,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\PlatformAccountService',
        'implementingClassName' => 'App\\Services\\PlatformAccountService',
        'currentClassName' => 'App\\Services\\PlatformAccountService',
        'aliasName' => NULL,
      ),
      'uniqueSlug' => 
      array (
        'name' => 'uniqueSlug',
        'parameters' => 
        array (
          'name' => 
          array (
            'name' => 'name',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 73,
            'endLine' => 73,
            'startColumn' => 33,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Human-friendly, guaranteed-unique platform slug (platforms.slug is UNIQUE).
 */',
        'startLine' => 73,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\PlatformAccountService',
        'implementingClassName' => 'App\\Services\\PlatformAccountService',
        'currentClassName' => 'App\\Services\\PlatformAccountService',
        'aliasName' => NULL,
      ),
    ),
    'traitsData' => 
    array (
      'aliases' => 
      array (
      ),
      'modifiers' => 
      array (
      ),
      'precedences' => 
      array (
      ),
      'hashes' => 
      array (
      ),
    ),
  ),
));