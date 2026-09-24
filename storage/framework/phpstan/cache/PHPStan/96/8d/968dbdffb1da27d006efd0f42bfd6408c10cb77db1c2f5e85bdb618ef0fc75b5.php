<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\Payment.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Payment
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-0606171269cf63cb4dbea2734565457beec16099fcafb3a0d18e12aace11676a',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Payment',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/Payment.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Payment',
    'shortName' => 'Payment',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => NULL,
    'attributes' => 
    array (
    ),
    'startLine' => 9,
    'endLine' => 76,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\Payment',
        'implementingClassName' => 'App\\Models\\Payment',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'payments\'',
          'attributes' => 
          array (
            'startLine' => 21,
            'endLine' => 21,
            'startTokenPos' => 45,
            'startFilePos' => 564,
            'endTokenPos' => 45,
            'endFilePos' => 573,
          ),
        ),
        'docComment' => '/**
 * Payment & Billing (T7) — manual MVP (ADR-011), financial records retained.
 *
 * No soft delete (financial records must never be lost; database.md soft-delete matrix).
 * Internal-only: no `public_id` (plan §6 ULID coverage excludes `payments`).
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
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
        'declaringClassName' => 'App\\Models\\Payment',
        'implementingClassName' => 'App\\Models\\Payment',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'subscription_id\', \'platform_id\', \'gateway\', \'gateway_transaction_id\', \'amount\', \'currency\', \'status\', \'paid_at\', \'metadata\']',
          'attributes' => 
          array (
            'startLine' => 28,
            'endLine' => 38,
            'startTokenPos' => 56,
            'startFilePos' => 699,
            'endTokenPos' => 85,
            'endFilePos' => 903,
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
        'startLine' => 28,
        'endLine' => 38,
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
        'startLine' => 45,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Payment',
        'implementingClassName' => 'App\\Models\\Payment',
        'currentClassName' => 'App\\Models\\Payment',
        'aliasName' => NULL,
      ),
      'subscription' => 
      array (
        'name' => 'subscription',
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
 * The subscription this payment settles.
 *
 * @return BelongsTo<Subscription, $this>
 */',
        'startLine' => 62,
        'endLine' => 65,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Payment',
        'implementingClassName' => 'App\\Models\\Payment',
        'currentClassName' => 'App\\Models\\Payment',
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
 * The platform that owns this payment (scoping root).
 *
 * @return BelongsTo<Platform, $this>
 */',
        'startLine' => 72,
        'endLine' => 75,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Payment',
        'implementingClassName' => 'App\\Models\\Payment',
        'currentClassName' => 'App\\Models\\Payment',
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