<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Http\Requests\IntegrationConfigRequest.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Requests\IntegrationConfigRequest
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-d14330d2d67ddaed3717eeb34e308267af1ff14d48ea9ffc1d61dbe2653b2b13',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Requests\\IntegrationConfigRequest',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Http/Requests/IntegrationConfigRequest.php',
      ),
    ),
    'namespace' => 'App\\Http\\Requests',
    'name' => 'App\\Http\\Requests\\IntegrationConfigRequest',
    'shortName' => 'IntegrationConfigRequest',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Self-service integration configuration (Phase 3C).
 *
 * Only the operator-tunable connection fields are mutable via the API:
 *   - base_domain   — the external platform\'s own origin (single value).
 *   - allowed_origins — CORS allowlist of origins (never `*` with credentials).
 * Fixed policy fields (paseto_version, token_ttl_seconds, rate_limit_per_minute,
 * status) stay out of reach until the admin surface exists.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 39,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Http\\Requests\\ApiFormRequest',
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
      'authorize' => 
      array (
        'name' => 'authorize',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'bool',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Determine if the user is authorized to make this request.
 *
 * Authorization is fully handled by route middleware
 * (ValidatePlatformToken + EnsurePlatformAccess:authentication); scope checks
 * derive from the verified token, never from the body.
 */',
        'startLine' => 23,
        'endLine' => 26,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\IntegrationConfigRequest',
        'implementingClassName' => 'App\\Http\\Requests\\IntegrationConfigRequest',
        'currentClassName' => 'App\\Http\\Requests\\IntegrationConfigRequest',
        'aliasName' => NULL,
      ),
      'rules' => 
      array (
        'name' => 'rules',
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
 * @return array<string, mixed>
 */',
        'startLine' => 31,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Requests',
        'declaringClassName' => 'App\\Http\\Requests\\IntegrationConfigRequest',
        'implementingClassName' => 'App\\Http\\Requests\\IntegrationConfigRequest',
        'currentClassName' => 'App\\Http\\Requests\\IntegrationConfigRequest',
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