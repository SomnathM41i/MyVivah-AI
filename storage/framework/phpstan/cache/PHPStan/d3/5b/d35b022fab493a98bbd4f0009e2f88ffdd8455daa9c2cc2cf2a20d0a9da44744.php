<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Services\WidgetSessionService.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\WidgetSessionService
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-879ce54d1646b0ba0a05b65c4632707f547700403b8dac651223d0cb9010265b',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\WidgetSessionService',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Services/WidgetSessionService.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\WidgetSessionService',
    'shortName' => 'WidgetSessionService',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Widget session PASETO issuance + validation (Phase 4).
 *
 * A widget session token is v4.local (same symmetric keys as platform tokens —
 * the CLIENT backend calls this through /api/v1/widget/session, so its secret is
 * still only ever held server-side), but its claims are DIFFERENT and narrower:
 *
 *   aud = "widget:{slug}"       (never `platform:{slug}` — see WidgetChannels)
 *   sub = external_user_id      (the browser user, not the platform)
 *   platform_id / external_user_id claims
 *   scope = [realtime_chat:read, realtime_chat:write]
 *
 * Because the `aud`/`platform_id` claim checks are audience-prefix-specific, a
 * widget token can never be replayed on platform-token routes (and vice versa).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 32,
    'endLine' => 232,
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
      'CHAT_SCOPES' => 
      array (
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'name' => 'CHAT_SCOPES',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '[\'realtime_chat:read\', \'realtime_chat:write\']',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 80,
            'startFilePos' => 1248,
            'endTokenPos' => 85,
            'endFilePos' => 1292,
          ),
        ),
        'docComment' => '/** Exactly the two chat scopes the widget needs (and nothing else). */',
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 77,
      ),
    ),
    'immediateProperties' => 
    array (
      'tokens' => 
      array (
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'name' => 'tokens',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\PasetoTokenService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 9,
        'endColumn' => 51,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'users' => 
      array (
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'name' => 'users',
        'modifiers' => 132,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\ExternalUserService',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 9,
        'endColumn' => 51,
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
          'tokens' => 
          array (
            'name' => 'tokens',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\PasetoTokenService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 38,
            'endLine' => 38,
            'startColumn' => 9,
            'endColumn' => 51,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'users' => 
          array (
            'name' => 'users',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\ExternalUserService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 39,
            'endLine' => 39,
            'startColumn' => 9,
            'endColumn' => 51,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 37,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'audiencePrefix' => 
      array (
        'name' => 'audiencePrefix',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 42,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'audienceFor' => 
      array (
        'name' => 'audienceFor',
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
            'startLine' => 47,
            'endLine' => 47,
            'startColumn' => 40,
            'endColumn' => 57,
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
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 17,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'issue' => 
      array (
        'name' => 'issue',
        'parameters' => 
        array (
          'integration' => 
          array (
            'name' => 'integration',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\PlatformIntegration',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 59,
            'endLine' => 59,
            'startColumn' => 9,
            'endColumn' => 40,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
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
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 9,
            'endColumn' => 27,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'externalUserId' => 
          array (
            'name' => 'externalUserId',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 61,
            'endLine' => 61,
            'startColumn' => 9,
            'endColumn' => 30,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'now' => 
          array (
            'name' => 'now',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 62,
                'endLine' => 62,
                'startTokenPos' => 212,
                'startFilePos' => 2110,
                'endTokenPos' => 212,
                'endFilePos' => 2113,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'DateTimeInterface',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 62,
            'endLine' => 62,
            'startColumn' => 9,
            'endColumn' => 39,
            'parameterIndex' => 3,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\IssuedWidgetSession',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Mint a short-lived widget session for one external user of one platform,
 * signed with one of the platform\'s symmetric keys.
 *
 * @throws ApiException INVALID_TOKEN (unsupported PASETO version)
 */',
        'startLine' => 58,
        'endLine' => 101,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'validate' => 
      array (
        'name' => 'validate',
        'parameters' => 
        array (
          'token' => 
          array (
            'name' => 'token',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'string',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 118,
            'endLine' => 118,
            'startColumn' => 30,
            'endColumn' => 42,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'now' => 
          array (
            'name' => 'now',
            'default' => 
            array (
              'code' => 'null',
              'attributes' => 
              array (
                'startLine' => 118,
                'endLine' => 118,
                'startTokenPos' => 531,
                'startFilePos' => 4431,
                'endTokenPos' => 531,
                'endFilePos' => 4434,
              ),
            ),
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionUnionType',
              'data' => 
              array (
                'types' => 
                array (
                  0 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'DateTimeInterface',
                      'isIdentifier' => false,
                    ),
                  ),
                  1 => 
                  array (
                    'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
                    'data' => 
                    array (
                      'name' => 'null',
                      'isIdentifier' => true,
                    ),
                  ),
                ),
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 118,
            'endLine' => 118,
            'startColumn' => 45,
            'endColumn' => 75,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Services\\ValidatedWidgetSession',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Validate a presented widget session token end-to-end:
 *
 *   1. cryptographically verify (v4.local only) with the key from the footer kid;
 *   2. enforce `exp`;
 *   3. enforce widget-claim isolation — `aud`/`subject`/`platform_id` MUST
 *      match the platform owning the key AND `subject` === `external_user_id`
 *      claim (the browser can never pick its own identity);
 *   4. resolve the ACTING user strictly inside that platform\'s identity map
 *      (a token for an un-mapped user is rejected — no implicit upsert);
 *   5. reject revoked keys/integrations/platforms and revoked jtis.
 *
 * @throws ApiException INVALID_TOKEN / TOKEN_EXPIRED / TOKEN_REVOKED /
 *                      PLATFORM_MISMATCH / PLATFORM_SUSPENDED
 */',
        'startLine' => 118,
        'endLine' => 161,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'revoke' => 
      array (
        'name' => 'revoke',
        'parameters' => 
        array (
          'session' => 
          array (
            'name' => 'session',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\ValidatedWidgetSession',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 166,
            'endLine' => 166,
            'startColumn' => 28,
            'endColumn' => 58,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Blacklist a validated widget session\'s `jti` for the remainder of its life.
 */',
        'startLine' => 166,
        'endLine' => 175,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'present' => 
      array (
        'name' => 'present',
        'parameters' => 
        array (
          'issued' => 
          array (
            'name' => 'issued',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\IssuedWidgetSession',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 184,
            'endLine' => 184,
            'startColumn' => 29,
            'endColumn' => 55,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'map' => 
          array (
            'name' => 'map',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\ExternalUserMap',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 184,
            'endLine' => 184,
            'startColumn' => 58,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'realtime' => 
          array (
            'name' => 'realtime',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'array',
                'isIdentifier' => true,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 184,
            'endLine' => 184,
            'startColumn' => 80,
            'endColumn' => 94,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
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
 * The `data` block returned to the browser for a freshly issued session
 * (shared by the widget/session endpoint and the local demo).
 *
 * @param  array<string, mixed>  $realtime
 * @return array<string, mixed>
 */',
        'startLine' => 184,
        'endLine' => 203,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'assertWidgetClaims' => 
      array (
        'name' => 'assertWidgetClaims',
        'parameters' => 
        array (
          'decoded' => 
          array (
            'name' => 'decoded',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'ParagonIE\\Paseto\\JsonToken',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 205,
            'endLine' => 205,
            'startColumn' => 41,
            'endColumn' => 58,
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
            'startLine' => 205,
            'endLine' => 205,
            'startColumn' => 61,
            'endColumn' => 78,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 205,
        'endLine' => 223,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => true,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
        'aliasName' => NULL,
      ),
      'ttlSeconds' => 
      array (
        'name' => 'ttlSeconds',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 225,
        'endLine' => 231,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 4,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\WidgetSessionService',
        'implementingClassName' => 'App\\Services\\WidgetSessionService',
        'currentClassName' => 'App\\Services\\WidgetSessionService',
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