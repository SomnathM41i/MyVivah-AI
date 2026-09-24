<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\Service.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Service
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-008e8d957d8a669ce3d3840cda78490dffdff91ffc1e41a5ee301d142e2297d5',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Service',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/Service.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Service',
    'shortName' => 'Service',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 10,
    'endLine' => 79,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Service',
        'implementingClassName' => 'App\\Models\\Service',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'services\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 53,
            'startFilePos' => 633,
            'endTokenPos' => 53,
            'endFilePos' => 642,
          ),
        ),
        'docComment' => '/**
 * MyVivahAI product-catalog service (T4), e.g. `realtime_chat`.
 *
 * Global catalog — not platform-owned (no `platform_id`; see platform-isolation.md §catalog).
 * Internal-only: no `public_id` (plan §6 ULID coverage). Soft delete for deactivate-not-delete.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 34,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Service',
        'implementingClassName' => 'App\\Models\\Service',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'key\', \'name\', \'description\', \'is_active\', \'sort_order\']',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 35,
            'startTokenPos' => 64,
            'startFilePos' => 768,
            'endTokenPos' => 81,
            'endFilePos' => 871,
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
        'startLine' => 29,
        'endLine' => 35,
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
        'startLine' => 42,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Service',
        'implementingClassName' => 'App\\Models\\Service',
        'currentClassName' => 'App\\Models\\Service',
        'aliasName' => NULL,
      ),
      'plans' => 
      array (
        'name' => 'plans',
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
 * Plans offered by this service.
 *
 * @return HasMany<Plan, $this>
 */',
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Service',
        'implementingClassName' => 'App\\Models\\Service',
        'currentClassName' => 'App\\Models\\Service',
        'aliasName' => NULL,
      ),
      'subscriptions' => 
      array (
        'name' => 'subscriptions',
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
 * Subscriptions for this service across platforms.
 *
 * @return HasMany<Subscription, $this>
 */',
        'startLine' => 65,
        'endLine' => 68,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Service',
        'implementingClassName' => 'App\\Models\\Service',
        'currentClassName' => 'App\\Models\\Service',
        'aliasName' => NULL,
      ),
      'platformServiceAccess' => 
      array (
        'name' => 'platformServiceAccess',
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
 * Entitlement rows granting platforms access to this service.
 *
 * @return HasMany<PlatformServiceAccess, $this>
 */',
        'startLine' => 75,
        'endLine' => 78,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Service',
        'implementingClassName' => 'App\\Models\\Service',
        'currentClassName' => 'App\\Models\\Service',
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