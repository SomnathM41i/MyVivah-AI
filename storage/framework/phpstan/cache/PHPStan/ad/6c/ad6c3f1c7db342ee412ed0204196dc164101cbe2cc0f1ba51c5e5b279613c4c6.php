<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Services\ExternalUserContext.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\ExternalUserContext
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-fcd38ce144e84ecbaa57ed2f840e9f09ee36fbf4c715f012ed89effb1aa25f85',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\ExternalUserContext',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Services/ExternalUserContext.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\ExternalUserContext',
    'shortName' => 'ExternalUserContext',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Resolves the ACTING external user for a chat API call.
 *
 * Server-to-server chat endpoints are authenticated by the PLATFORM (PASETO);
 * the platform then asserts WHICH of its own users is acting via the
 * `X-External-User-Id` header. The platform is the source of truth for its
 * users, so this header + the verified platform token is the correct trust
 * boundary — it is still strictly platform-scoped: an id only resolves inside
 * the calling platform\'s own `platform_external_user_map`, so a value that
 * belongs to another platform can never be "found".
 *
 * WIDGET calls (Phase 4) are different and safer: the acting user is bound INSIDE
 * the verified widget session token (`sub` + `external_user_id` claim) — the
 * browser can never pick its own identity. When a `widget_session` request
 * attribute is present it takes absolute priority and the `X-External-User-Id`
 * header is IGNORED entirely (a spoofed header cannot override an already-issued
 * session, and never triggers mapping based on attacker-controlled input).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 28,
    'endLine' => 74,
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
      'HEADER' => 
      array (
        'declaringClassName' => 'App\\Services\\ExternalUserContext',
        'implementingClassName' => 'App\\Services\\ExternalUserContext',
        'name' => 'HEADER',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'X-External-User-Id\'',
          'attributes' => 
          array (
            'startLine' => 30,
            'endLine' => 30,
            'startTokenPos' => 43,
            'startFilePos' => 1266,
            'endTokenPos' => 43,
            'endFilePos' => 1285,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 30,
        'endLine' => 30,
        'startColumn' => 5,
        'endColumn' => 47,
      ),
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'resolve' => 
      array (
        'name' => 'resolve',
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
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 29,
            'endColumn' => 44,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startColumn' => 47,
            'endColumn' => 64,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\ExternalUserMap',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Resolve the acting external user strictly inside one platform\'s namespace.
 *
 * @throws ApiException VALIDATION_FAILED (missing/oversized header) or
 *                      NOT_FOUND (id not mapped for this platform)
 */',
        'startLine' => 38,
        'endLine' => 73,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\ExternalUserContext',
        'implementingClassName' => 'App\\Services\\ExternalUserContext',
        'currentClassName' => 'App\\Services\\ExternalUserContext',
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