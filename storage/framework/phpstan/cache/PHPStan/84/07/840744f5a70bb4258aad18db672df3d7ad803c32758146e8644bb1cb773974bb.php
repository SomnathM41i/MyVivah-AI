<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\PlatformAdmin.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PlatformAdmin
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-96f8cff7a6eca0614fc98b1779e7ee565fb73fb91bb0bb1d8d622c1fbce5d3df',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PlatformAdmin',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/PlatformAdmin.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PlatformAdmin',
    'shortName' => 'PlatformAdmin',
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
    'endLine' => 70,
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
        'declaringClassName' => 'App\\Models\\PlatformAdmin',
        'implementingClassName' => 'App\\Models\\PlatformAdmin',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'platform_admins\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 45,
            'startFilePos' => 588,
            'endTokenPos' => 45,
            'endFilePos' => 604,
          ),
        ),
        'docComment' => '/**
 * Platform↔user admin/owner/developer assignment (T3).
 *
 * Internal-only table — no `public_id` (plan §6 ULID coverage list excludes it).
 * No soft delete (per database.md soft-delete matrix). UNIQUE(platform_id, user_id)
 * guaranteed at the schema level.
 *
 * @var string
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 41,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformAdmin',
        'implementingClassName' => 'App\\Models\\PlatformAdmin',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'platform_id\', \'user_id\', \'role\', \'invited_at\', \'accepted_at\']',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 35,
            'startTokenPos' => 56,
            'startFilePos' => 730,
            'endTokenPos' => 73,
            'endFilePos' => 839,
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
        'endLine' => 49,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformAdmin',
        'implementingClassName' => 'App\\Models\\PlatformAdmin',
        'currentClassName' => 'App\\Models\\PlatformAdmin',
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
 * The platform this admin belongs to (scoping root).
 *
 * @return BelongsTo<Platform, $this>
 */',
        'startLine' => 56,
        'endLine' => 59,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformAdmin',
        'implementingClassName' => 'App\\Models\\PlatformAdmin',
        'currentClassName' => 'App\\Models\\PlatformAdmin',
        'aliasName' => NULL,
      ),
      'user' => 
      array (
        'name' => 'user',
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
 * The user holding this admin role.
 *
 * @return BelongsTo<User, $this>
 */',
        'startLine' => 66,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformAdmin',
        'implementingClassName' => 'App\\Models\\PlatformAdmin',
        'currentClassName' => 'App\\Models\\PlatformAdmin',
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