<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\ApiAuditLog.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ApiAuditLog
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-f2cd277a4401b1f55405ce4dc3fac2a0b23e04b61d32d010c78c3551d91fcfec',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ApiAuditLog',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/ApiAuditLog.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ApiAuditLog',
    'shortName' => 'ApiAuditLog',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Append-only security/integration audit row (phase-3a §5.2/§13).
 *
 * Immutable by design: no `updated_at`, no soft deletes. Privacy contract:
 * never stores raw secrets, bodies, or PII — only fingerprints/checksums and
 * minimal metadata.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 97,
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
      'UPDATED_AT' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'UPDATED_AT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => 'null',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 63,
            'startFilePos' => 654,
            'endTokenPos' => 63,
            'endFilePos' => 657,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 35,
      ),
      'EVENT_REQUEST' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_REQUEST',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'request\'',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 27,
            'startTokenPos' => 76,
            'startFilePos' => 761,
            'endTokenPos' => 76,
            'endFilePos' => 769,
          ),
        ),
        'docComment' => '/** Stable event identifiers (phase-3a §13 + security.md). */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 27,
        'startColumn' => 5,
        'endColumn' => 43,
      ),
      'EVENT_TOKEN_ISSUED' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_TOKEN_ISSUED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'token_issued\'',
          'attributes' => 
          array (
            'startLine' => 29,
            'endLine' => 29,
            'startTokenPos' => 87,
            'startFilePos' => 811,
            'endTokenPos' => 87,
            'endFilePos' => 824,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 29,
        'endLine' => 29,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
      'EVENT_TOKEN_REJECTED' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_TOKEN_REJECTED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'token_rejected\'',
          'attributes' => 
          array (
            'startLine' => 31,
            'endLine' => 31,
            'startTokenPos' => 98,
            'startFilePos' => 868,
            'endTokenPos' => 98,
            'endFilePos' => 883,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 31,
        'endLine' => 31,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'EVENT_TOKEN_EXPIRED' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_TOKEN_EXPIRED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'token_expired\'',
          'attributes' => 
          array (
            'startLine' => 33,
            'endLine' => 33,
            'startTokenPos' => 109,
            'startFilePos' => 926,
            'endTokenPos' => 109,
            'endFilePos' => 940,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 33,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'EVENT_INVALID_TOKEN' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_INVALID_TOKEN',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'invalid_token\'',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 120,
            'startFilePos' => 983,
            'endTokenPos' => 120,
            'endFilePos' => 997,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 55,
      ),
      'EVENT_WRONG_PLATFORM' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_WRONG_PLATFORM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'wrong_platform\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 131,
            'startFilePos' => 1041,
            'endTokenPos' => 131,
            'endFilePos' => 1056,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 57,
      ),
      'EVENT_INSUFFICIENT_SCOPE' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_INSUFFICIENT_SCOPE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'insufficient_scope\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 142,
            'startFilePos' => 1104,
            'endTokenPos' => 142,
            'endFilePos' => 1123,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 65,
      ),
      'EVENT_KEY_ROTATION' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'EVENT_KEY_ROTATION',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'key_rotation\'',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 153,
            'startFilePos' => 1165,
            'endTokenPos' => 153,
            'endFilePos' => 1178,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 53,
      ),
    ),
    'immediateProperties' => 
    array (
      'table' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'api_audit_logs\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 52,
            'startFilePos' => 605,
            'endTokenPos' => 52,
            'endFilePos' => 620,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 40,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'platform_id\', \'api_key_id\', \'event\', \'ip_hash\', \'endpoint\', \'method\', \'status_code\', \'duration_ms\', \'request_checksum\', \'response_checksum\', \'metadata\', \'created_at\']',
          'attributes' => 
          array (
            'startLine' => 48,
            'endLine' => 61,
            'startTokenPos' => 164,
            'startFilePos' => 1304,
            'endTokenPos' => 202,
            'endFilePos' => 1574,
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
        'startLine' => 48,
        'endLine' => 61,
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
        'startLine' => 68,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'currentClassName' => 'App\\Models\\ApiAuditLog',
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
 * The owning platform (null for pre-auth failures that could not be resolved).
 *
 * @return BelongsTo<Platform, $this>
 */',
        'startLine' => 83,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'currentClassName' => 'App\\Models\\ApiAuditLog',
        'aliasName' => NULL,
      ),
      'apiKey' => 
      array (
        'name' => 'apiKey',
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
 * The API key involved (null when unknown).
 *
 * @return BelongsTo<PlatformApiKey, $this>
 */',
        'startLine' => 93,
        'endLine' => 96,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ApiAuditLog',
        'implementingClassName' => 'App\\Models\\ApiAuditLog',
        'currentClassName' => 'App\\Models\\ApiAuditLog',
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