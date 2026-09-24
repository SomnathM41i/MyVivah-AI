<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\ExternalUserMap.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ExternalUserMap
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-feca7332439dbb9ec7b656049874105de8c511aaa4792694c5bf016a7481f13a',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ExternalUserMap',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/ExternalUserMap.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ExternalUserMap',
    'shortName' => 'ExternalUserMap',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Platform-scoped external→local user identity reference (phase-3a §5.2/§8).
 *
 * Data-minimization: mapping + sync timestamps + integration metadata ONLY.
 * The external platform remains the source of truth for the user\'s profile;
 * MyVivahAI never mirrors it. Identity key: UNIQUE(platform_id, external_user_id).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 78,
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
      'PRESENCE_ONLINE' => 
      array (
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'name' => 'PRESENCE_ONLINE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'online\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 63,
            'startFilePos' => 757,
            'endTokenPos' => 63,
            'endFilePos' => 764,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 44,
      ),
      'PRESENCE_OFFLINE' => 
      array (
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'name' => 'PRESENCE_OFFLINE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'offline\'',
          'attributes' => 
          array (
            'startLine' => 26,
            'endLine' => 26,
            'startTokenPos' => 74,
            'startFilePos' => 804,
            'endTokenPos' => 74,
            'endFilePos' => 812,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 26,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'platform_external_user_map\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 52,
            'startFilePos' => 691,
            'endTokenPos' => 52,
            'endFilePos' => 718,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 52,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'platform_id\', \'external_user_id\', \'local_public_id\', \'metadata\', \'synced_at\', \'last_seen_at\', \'presence_status\', \'presence_seen_at\']',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 42,
            'startTokenPos' => 85,
            'startFilePos' => 938,
            'endTokenPos' => 111,
            'endFilePos' => 1142,
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
        'startLine' => 33,
        'endLine' => 42,
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
        'startLine' => 49,
        'endLine' => 57,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'currentClassName' => 'App\\Models\\ExternalUserMap',
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
 * The owning platform — the isolation root for every lookup.
 *
 * @return BelongsTo<Platform, $this>
 */',
        'startLine' => 64,
        'endLine' => 67,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'currentClassName' => 'App\\Models\\ExternalUserMap',
        'aliasName' => NULL,
      ),
      'localUser' => 
      array (
        'name' => 'localUser',
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
 * The optional linked MyVivahAI user (by public ULID).
 *
 * @return BelongsTo<User, $this>
 */',
        'startLine' => 74,
        'endLine' => 77,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ExternalUserMap',
        'implementingClassName' => 'App\\Models\\ExternalUserMap',
        'currentClassName' => 'App\\Models\\ExternalUserMap',
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