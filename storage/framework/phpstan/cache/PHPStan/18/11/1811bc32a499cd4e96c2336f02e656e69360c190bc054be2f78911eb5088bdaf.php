<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/database/factories/PlatformApiKeyFactory.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Factories\PlatformApiKeyFactory
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-b2effb66eefa8303c3ee82ce6f362aa710fa542f4f34866a37967d8bf67ef34e-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Factories\\PlatformApiKeyFactory',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/database/factories/PlatformApiKeyFactory.php',
      ),
    ),
    'namespace' => 'Database\\Factories',
    'name' => 'Database\\Factories\\PlatformApiKeyFactory',
    'shortName' => 'PlatformApiKeyFactory',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @extends Factory<PlatformApiKey>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 98,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'definition' => 
      array (
        'name' => 'definition',
        'parameters' => 
        array (
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
 * Define the model\'s default state.
 *
 * 1:1 with phase-3a-api-integration-plan.md §5.2 — T10 `platform_api_keys`.
 * The raw 32-byte v4 key is generated here, its fingerprint computed, then
 * stored ONLY as encrypted ciphertext (`key_encrypted`, AES-256-CBC via
 * APP_KEY). The plaintext is never persisted.
 *
 * @return array<string, mixed>
 */',
        'startLine' => 26,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'implementingClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'currentClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'aliasName' => NULL,
      ),
      'forIntegration' => 
      array (
        'name' => 'forIntegration',
        'parameters' => 
        array (
          'integration' => 
          array (
            'name' => 'integration',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\PlatformIntegration',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 44,
            'endLine' => 44,
            'startColumn' => 36,
            'endColumn' => 67,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Attach a specific integration instead of factory-created.
 */',
        'startLine' => 44,
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'implementingClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'currentClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'aliasName' => NULL,
      ),
      'primary' => 
      array (
        'name' => 'primary',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark the key as the active primary for its integration.
 */',
        'startLine' => 54,
        'endLine' => 60,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'implementingClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'currentClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'aliasName' => NULL,
      ),
      'backup' => 
      array (
        'name' => 'backup',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mark the key as a rotated/backup (non-primary) key inside its grace window.
 */',
        'startLine' => 65,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'implementingClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'currentClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'aliasName' => NULL,
      ),
      'revoked' => 
      array (
        'name' => 'revoked',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Hard-revoke the key.
 */',
        'startLine' => 78,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'implementingClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'currentClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'aliasName' => NULL,
      ),
      'withRawSecret' => 
      array (
        'name' => 'withRawSecret',
        'parameters' => 
        array (
          'base64url' => 
          array (
            'name' => 'base64url',
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
            'startLine' => 91,
            'endLine' => 91,
            'startColumn' => 35,
            'endColumn' => 51,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Use a known raw secret (base64url-encoded 32-byte v4 key). Enables tests
 * to reproduce the exact key that should be sent as `client_secret` or used
 * to mint tokens directly.
 */',
        'startLine' => 91,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'implementingClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
        'currentClassName' => 'Database\\Factories\\PlatformApiKeyFactory',
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