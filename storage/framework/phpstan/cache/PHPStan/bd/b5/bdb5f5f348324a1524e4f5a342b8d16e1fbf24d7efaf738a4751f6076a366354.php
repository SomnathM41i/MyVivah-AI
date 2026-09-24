<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Http\Middleware\EnforcePlatformRateLimit.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Http\Middleware\EnforcePlatformRateLimit
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-76980a0abc91f1bf55fad7c3c9af8db205e1d9015688f8a8294088dd37b47854',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Http/Middleware/EnforcePlatformRateLimit.php',
      ),
    ),
    'namespace' => 'App\\Http\\Middleware',
    'name' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
    'shortName' => 'EnforcePlatformRateLimit',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * PER-PLATFORM rate limit for authenticated v1 routes (phase-3a §14/§15, Phase 3C).
 *
 * Why a dedicated middleware instead of the `throttle:integration` alias:
 * Laravel\'s request pipeline priority-sorts the `ThrottleRequests` middleware
 * to run BEFORE custom middleware — meaning it would execute before
 * ValidatePlatformToken has hydrated the `paseto` request attribute. This
 * middleware is a plain class (no priority reordering), so it runs exactly
 * where it is listed: after the platform context is available.
 *
 * Policy:
 *   - keyed by the VERIFIED platform id (never client input) — one platform
 *     can never consume another\'s quota,
 *   - ceiling read from `platform_integrations.rate_limit_per_minute`
 *     (fallback `config(\'api.rate_limit_per_minute\')`, default 60),
 *   - when a request exceeds the ceiling it throws ThrottleRequestsException so
 *     the standardized RATE_LIMITED envelope (+ Retry-After) is rendered and,
 *     because LogApiAudit wraps this middleware, the 429 is still audited.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 31,
    'endLine' => 72,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
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
      'limiter' => 
      array (
        'declaringClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'implementingClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'name' => 'limiter',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Cache\\RateLimiter',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 34,
        'endLine' => 34,
        'startColumn' => 9,
        'endColumn' => 45,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
    ),
    'immediateMethods' => 
    array (
      '__construct' => 
      array (
        'name' => '__construct',
        'parameters' => 
        array (
          'limiter' => 
          array (
            'name' => 'limiter',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Cache\\RateLimiter',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 34,
            'endLine' => 34,
            'startColumn' => 9,
            'endColumn' => 45,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 33,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Middleware',
        'declaringClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'implementingClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'currentClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'aliasName' => NULL,
      ),
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'request' => 
          array (
            'name' => 'request',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Http\\Request',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 28,
            'endColumn' => 43,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'next' => 
          array (
            'name' => 'next',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Closure',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 46,
            'endColumn' => 58,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * @param  Closure(Request): Response  $next
 * @return Response
 */',
        'startLine' => 41,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Http\\Middleware',
        'declaringClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'implementingClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
        'currentClassName' => 'App\\Http\\Middleware\\EnforcePlatformRateLimit',
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