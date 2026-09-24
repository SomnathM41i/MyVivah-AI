<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\PlatformServiceAccess.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\PlatformServiceAccess
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-a0618a42bddf02be65203dbb856546d8d83cceb9d8197a2f81f1bd4b1a191e04',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\PlatformServiceAccess',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/PlatformServiceAccess.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\PlatformServiceAccess',
    'shortName' => 'PlatformServiceAccess',
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
    'endLine' => 43,
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
        'declaringClassName' => 'App\\Models\\PlatformServiceAccess',
        'implementingClassName' => 'App\\Models\\PlatformServiceAccess',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'platform_service_access\'',
          'attributes' => 
          array (
            'startLine' => 13,
            'endLine' => 13,
            'startTokenPos' => 43,
            'startFilePos' => 268,
            'endTokenPos' => 43,
            'endFilePos' => 292,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 13,
        'endLine' => 13,
        'startColumn' => 5,
        'endColumn' => 49,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformServiceAccess',
        'implementingClassName' => 'App\\Models\\PlatformServiceAccess',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'platform_id\', \'service_id\', \'has_access\', \'effective_until\', \'synced_at\']',
          'attributes' => 
          array (
            'startLine' => 15,
            'endLine' => 21,
            'startTokenPos' => 52,
            'startFilePos' => 322,
            'endTokenPos' => 69,
            'endFilePos' => 443,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 15,
        'endLine' => 21,
        'startColumn' => 5,
        'endColumn' => 6,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'casts' => 
      array (
        'declaringClassName' => 'App\\Models\\PlatformServiceAccess',
        'implementingClassName' => 'App\\Models\\PlatformServiceAccess',
        'name' => 'casts',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'has_access\' => \'boolean\', \'effective_until\' => \'datetime\', \'synced_at\' => \'datetime\']',
          'attributes' => 
          array (
            'startLine' => 23,
            'endLine' => 27,
            'startTokenPos' => 78,
            'startFilePos' => 470,
            'endTokenPos' => 101,
            'endFilePos' => 587,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 23,
        'endLine' => 27,
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
        'docComment' => NULL,
        'startLine' => 29,
        'endLine' => 32,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformServiceAccess',
        'implementingClassName' => 'App\\Models\\PlatformServiceAccess',
        'currentClassName' => 'App\\Models\\PlatformServiceAccess',
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
 * The service this entitlement row grants access to.
 *
 * @return BelongsTo<Service, $this>
 */',
        'startLine' => 39,
        'endLine' => 42,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\PlatformServiceAccess',
        'implementingClassName' => 'App\\Models\\PlatformServiceAccess',
        'currentClassName' => 'App\\Models\\PlatformServiceAccess',
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