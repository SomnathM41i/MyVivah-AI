<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/database/factories/ApiAuditLogFactory.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Factories\ApiAuditLogFactory
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-313b34f8b52c79068903e8a63e1476f0fa346836b71122b25731a6d036bba750-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Factories\\ApiAuditLogFactory',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/database/factories/ApiAuditLogFactory.php',
      ),
    ),
    'namespace' => 'Database\\Factories',
    'name' => 'Database\\Factories\\ApiAuditLogFactory',
    'shortName' => 'ApiAuditLogFactory',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @extends Factory<ApiAuditLog>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 13,
    'endLine' => 55,
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
 * Append-only audit row — no PII, no secrets; stable event + safe metadata only.
 *
 * @return array<string, mixed>
 */',
        'startLine' => 22,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'implementingClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'currentClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'aliasName' => NULL,
      ),
      'forPlatform' => 
      array (
        'name' => 'forPlatform',
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
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 33,
            'endColumn' => 50,
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
 * Attach the owning platform.
 */',
        'startLine' => 38,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'implementingClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'currentClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'aliasName' => NULL,
      ),
      'forApiKey' => 
      array (
        'name' => 'forApiKey',
        'parameters' => 
        array (
          'key' => 
          array (
            'name' => 'key',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\PlatformApiKey',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 48,
            'endLine' => 48,
            'startColumn' => 31,
            'endColumn' => 49,
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
 * Attach the involved API key.
 */',
        'startLine' => 48,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'implementingClassName' => 'Database\\Factories\\ApiAuditLogFactory',
        'currentClassName' => 'Database\\Factories\\ApiAuditLogFactory',
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