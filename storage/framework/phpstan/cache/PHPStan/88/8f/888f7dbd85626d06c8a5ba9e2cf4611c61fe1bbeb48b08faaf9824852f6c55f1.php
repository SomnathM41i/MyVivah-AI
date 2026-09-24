<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\PlatformIntegration.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PlatformIntegration
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-e821087e4cece6a1fd457ed1e5cd03450e48a38df8893e2043881fd50e2ee5dc',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PlatformIntegration',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/PlatformIntegration.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PlatformIntegration',
    'shortName' => 'PlatformIntegration',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 12,
    'endLine' => 104,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasUlids',
    ),
    'immediateConstants' => 
    array (
      'STATUS_PENDING' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'name' => 'STATUS_PENDING',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'pending\'',
          'attributes' => 
          array (
            'startLine' => 61,
            'endLine' => 61,
            'startTokenPos' => 177,
            'startFilePos' => 1554,
            'endTokenPos' => 177,
            'endFilePos' => 1562,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 61,
        'endLine' => 61,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'STATUS_ACTIVE' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'name' => 'STATUS_ACTIVE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'active\'',
          'attributes' => 
          array (
            'startLine' => 63,
            'endLine' => 63,
            'startTokenPos' => 188,
            'startFilePos' => 1599,
            'endTokenPos' => 188,
            'endFilePos' => 1606,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 63,
        'endLine' => 63,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'STATUS_SUSPENDED' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'name' => 'STATUS_SUSPENDED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'suspended\'',
          'attributes' => 
          array (
            'startLine' => 65,
            'endLine' => 65,
            'startTokenPos' => 199,
            'startFilePos' => 1646,
            'endTokenPos' => 199,
            'endFilePos' => 1656,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 65,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'STATUS_DEACTIVATED' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'name' => 'STATUS_DEACTIVATED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'deactivated\'',
          'attributes' => 
          array (
            'startLine' => 67,
            'endLine' => 67,
            'startTokenPos' => 210,
            'startFilePos' => 1698,
            'endTokenPos' => 210,
            'endFilePos' => 1710,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 67,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 52,
      ),
      'PASETO_V4_LOCAL' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'name' => 'PASETO_V4_LOCAL',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'v4.local\'',
          'attributes' => 
          array (
            'startLine' => 70,
            'endLine' => 70,
            'startTokenPos' => 223,
            'startFilePos' => 1815,
            'endTokenPos' => 223,
            'endFilePos' => 1824,
          ),
        ),
        'docComment' => '/** PASETO version shared lock (Phase 3A §20/1; ADR-013). */',
        'attributes' => 
        array (
        ),
        'startLine' => 70,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'public_id\', \'platform_id\', \'status\', \'paseto_version\', \'token_ttl_seconds\', \'rate_limit_per_minute\', \'base_domain\', \'allowed_origins\', \'last_active_at\', \'revoked_at\']',
          'attributes' => 
          array (
            'startLine' => 34,
            'endLine' => 45,
            'startTokenPos' => 89,
            'startFilePos' => 950,
            'endTokenPos' => 121,
            'endFilePos' => 1204,
          ),
        ),
        'docComment' => '/**
 * The attributes that are mass assignable.
 *
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      'uniqueIds' => 
      array (
        'name' => 'uniqueIds',
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
 * ULID columns — `public_id` is the CHAR(26) integration key (Phase 3A §5.2,
 * ADR-002 ULID matrix). Exposed in token `sub`/`platform_id` claims and client
 * integrations; internal `id` stays out of wire payloads.
 *
 * @return list<string>
 */',
        'startLine' => 24,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'currentClassName' => 'App\\Models\\PlatformIntegration',
        'aliasName' => NULL,
      ),
      'casts' => 
      array (
        'name' => 'casts',
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
 * Get the attributes that should be cast.
 *
 * @return array<string, string>
 */',
        'startLine' => 52,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'currentClassName' => 'App\\Models\\PlatformIntegration',
        'aliasName' => NULL,
      ),
      'platform' => 
      array (
        'name' => 'platform',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\BelongsTo',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * The owning platform — isolation root for every integration query.
 *
 * @return BelongsTo<Platform, $this>
 */',
        'startLine' => 77,
        'endLine' => 80,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'currentClassName' => 'App\\Models\\PlatformIntegration',
        'aliasName' => NULL,
      ),
      'apiKeys' => 
      array (
        'name' => 'apiKeys',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * All keys ever issued for this integration (primary, backups, revoked).
 *
 * @return HasMany<PlatformApiKey, $this>
 */',
        'startLine' => 87,
        'endLine' => 90,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'currentClassName' => 'App\\Models\\PlatformIntegration',
        'aliasName' => NULL,
      ),
      'primaryApiKey' => 
      array (
        'name' => 'primaryApiKey',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
          'data' => 
          array (
            'types' => 
            array (
              0 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'App\\Models\\PlatformApiKey',
                  'isIdentifier' => false,
                ),
              ),
              1 => 
              array (
                'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                'data' => 
                array (
                  'name' => 'null',
                  'isIdentifier' => true,
                ),
              ),
            ),
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * The active primary key, or null when none exists.
 */',
        'startLine' => 95,
        'endLine' => 103,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformIntegration',
        'implementingClassName' => 'App\\Models\\PlatformIntegration',
        'currentClassName' => 'App\\Models\\PlatformIntegration',
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