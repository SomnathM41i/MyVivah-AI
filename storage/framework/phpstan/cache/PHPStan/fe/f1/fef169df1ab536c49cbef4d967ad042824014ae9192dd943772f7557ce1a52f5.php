<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\Subscription.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Subscription
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-d22bd3374f8c84bc7aa5a3f0c87ddee5a00c84df8f0851b8d6653f7bfa221dc1',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Subscription',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/Subscription.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Subscription',
    'shortName' => 'Subscription',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 11,
    'endLine' => 111,
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
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'subscriptions\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 58,
            'startFilePos' => 751,
            'endTokenPos' => 58,
            'endFilePos' => 765,
          ),
        ),
        'docComment' => '/**
 * Service & Subscription lifecycle (T6) — platform_scoped, status-driven.
 *
 * No soft delete (status transitions only, ADR-00x / database.md soft-delete matrix).
 * At-most-one `active` per (platform, service) enforced in service layer (open decision,
 * platform-isolation.md), not via a partial unique index.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 39,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'public_id\', \'platform_id\', \'service_id\', \'plan_id\', \'status\', \'starts_at\', \'ends_at\', \'cancelled_at\', \'next_billing_at\', \'auto_renew\']',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 52,
            'startTokenPos' => 93,
            'startFilePos' => 1113,
            'endTokenPos' => 125,
            'endFilePos' => 1335,
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
        'startLine' => 41,
        'endLine' => 52,
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
 * ULID column — `public_id` (CHAR(26) ULID) is the public subscription reference.
 *
 * @return list<string>
 */',
        'startLine' => 31,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'currentClassName' => 'App\\Models\\Subscription',
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
        'startLine' => 59,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'currentClassName' => 'App\\Models\\Subscription',
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
 * The platform that owns this subscription (scoping root).
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
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'currentClassName' => 'App\\Models\\Subscription',
        'aliasName' => NULL,
      ),
      'service' => 
      array (
        'name' => 'service',
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
 * The service being subscribed to.
 *
 * @return BelongsTo<Service, $this>
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
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'currentClassName' => 'App\\Models\\Subscription',
        'aliasName' => NULL,
      ),
      'plan' => 
      array (
        'name' => 'plan',
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
 * The purchased plan.
 *
 * @return BelongsTo<Plan, $this>
 */',
        'startLine' => 97,
        'endLine' => 100,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'currentClassName' => 'App\\Models\\Subscription',
        'aliasName' => NULL,
      ),
      'payments' => 
      array (
        'name' => 'payments',
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
 * Payments recorded against this subscription.
 *
 * @return HasMany<Payment, $this>
 */',
        'startLine' => 107,
        'endLine' => 110,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Subscription',
        'implementingClassName' => 'App\\Models\\Subscription',
        'currentClassName' => 'App\\Models\\Subscription',
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