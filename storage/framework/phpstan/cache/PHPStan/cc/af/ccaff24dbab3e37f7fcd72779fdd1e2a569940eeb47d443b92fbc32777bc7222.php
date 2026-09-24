<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Services\PlatformOnboardingService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\PlatformOnboardingService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-0ef5b27a6135cfdf014f8088d6aecee9a80f934948d7f2b8675c0a8a72d1e5c9',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\PlatformOnboardingService',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Services/PlatformOnboardingService.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\PlatformOnboardingService',
    'shortName' => 'PlatformOnboardingService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Programmatic platform onboarding (Phase 3C — no dashboard UI at MVP).
 *
 * Provisions the full integration root in one idempotent call:
 *   platform (active) → integration (active, v4.local) → first primary API key
 *   → live `platform_service_access` entitlement for the requested service.
 *
 * Idempotency contract:
 *   - Re-running on a known slug reuses the existing platform + integration and
 *     only (re)confirms the entitlement.
 *   - A one-time secret is minted ONLY when no primary key exists yet. When a
 *     primary already exists the secret is null — the operator must rotate to
 *     obtain a fresh one (secrets are never re-issued or recoverable).
 *   - The raw secret is returned exactly once and then forgotten: the DB stores
 *     only the fingerprint + encrypted ciphertext (ApiKeyService contract).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 30,
    'endLine' => 108,
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
      'SYSTEM_OWNER_EMAIL' => 
      array (
        'declaringClassName' => 'App\\Services\\PlatformOnboardingService',
        'implementingClassName' => 'App\\Services\\PlatformOnboardingService',
        'name' => 'SYSTEM_OWNER_EMAIL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'system@myvivah.local\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 65,
            'startFilePos' => 1273,
            'endTokenPos' => 65,
            'endFilePos' => 1294,
          ),
        ),
        'docComment' => '/** System owner used for programmatically provisioned platforms. */',
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 61,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'onboard' => 
      array (
        'name' => 'onboard',
        'parameters' => 
        array (
          'slug' => 
          array (
            'name' => 'slug',
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
            'startLine' => 40,
            'endLine' => 40,
            'startColumn' => 9,
            'endColumn' => 20,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'name' => 
          array (
            'name' => 'name',
            'default' => 
            array (
              'code' => '\'\'',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 88,
                'startFilePos' => 1573,
                'endTokenPos' => 88,
                'endFilePos' => 1574,
              ),
            ),
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 9,
            'endColumn' => 25,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
          'serviceKey' => 
          array (
            'name' => 'serviceKey',
            'default' => 
            array (
              'code' => '\'realtime_chat\'',
              'attributes' => 
              array (
                'startLine' => 42,
                'endLine' => 42,
                'startTokenPos' => 97,
                'startFilePos' => 1606,
                'endTokenPos' => 97,
                'endFilePos' => 1620,
              ),
            ),
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
            'startLine' => 42,
            'endLine' => 42,
            'startColumn' => 9,
            'endColumn' => 44,
            'parameterIndex' => 2,
            'isOptional' => true,
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
 * @return array{0: Platform, 1: PlatformIntegration, 2: ?string}
 *                                                                [platform, integration, oneTimeClientSecret]
 */',
        'startLine' => 39,
        'endLine' => 81,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\PlatformOnboardingService',
        'implementingClassName' => 'App\\Services\\PlatformOnboardingService',
        'currentClassName' => 'App\\Services\\PlatformOnboardingService',
        'aliasName' => NULL,
      ),
      'systemOwner' => 
      array (
        'name' => 'systemOwner',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\User',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 83,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\PlatformOnboardingService',
        'implementingClassName' => 'App\\Services\\PlatformOnboardingService',
        'currentClassName' => 'App\\Services\\PlatformOnboardingService',
        'aliasName' => NULL,
      ),
      'ensureEntitlement' => 
      array (
        'name' => 'ensureEntitlement',
        'parameters' => 
        array (
          'platform' => 
          array (
            'name' => 'platform',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Platform',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 40,
            'endColumn' => 57,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'serviceKey' => 
          array (
            'name' => 'serviceKey',
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
            'startLine' => 92,
            'endLine' => 92,
            'startColumn' => 60,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 92,
        'endLine' => 107,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\PlatformOnboardingService',
        'implementingClassName' => 'App\\Services\\PlatformOnboardingService',
        'currentClassName' => 'App\\Services\\PlatformOnboardingService',
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