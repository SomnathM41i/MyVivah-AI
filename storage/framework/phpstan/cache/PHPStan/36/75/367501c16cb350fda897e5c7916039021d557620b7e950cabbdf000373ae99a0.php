<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Services\RealtimeBroadcaster.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Services\RealtimeBroadcaster
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-7cc4d35d0ed8ff09cf1d9f56a39a2ddd8a411c7d8f2ad595e9cd8f0d4097a67c',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Services\\RealtimeBroadcaster',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Services/RealtimeBroadcaster.php',
      ),
    ),
    'namespace' => 'App\\Services',
    'name' => 'App\\Services\\RealtimeBroadcaster',
    'shortName' => 'RealtimeBroadcaster',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Centralized realtime dispatch (Phase 3E).
 *
 * Every method first honors config(\'chat.realtime.enabled\'): when realtime is
 * disabled (the shared-hosting default) nothing is constructed or dispatched —
 * the REST write already happened and stays fully functional. When enabled,
 * events fire AFTER the service\'s own DB commit (strict call-site ordering) and
 * synchronously (ShouldBroadcastNow); failures are rescued, so a dead realtime
 * transport can never break a REST request.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 26,
    'endLine' => 92,
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
    ),
    'immediateMethods' => 
    array (
      'messageCreated' => 
      array (
        'name' => 'messageCreated',
        'parameters' => 
        array (
          'conversation' => 
          array (
            'name' => 'conversation',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Conversation',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 36,
            'endColumn' => 61,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'message' => 
          array (
            'name' => 'message',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Message',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 64,
            'endColumn' => 79,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'sender' => 
          array (
            'name' => 'sender',
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
            'startLine' => 28,
            'endLine' => 28,
            'startColumn' => 82,
            'endColumn' => 104,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 28,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\RealtimeBroadcaster',
        'implementingClassName' => 'App\\Services\\RealtimeBroadcaster',
        'currentClassName' => 'App\\Services\\RealtimeBroadcaster',
        'aliasName' => NULL,
      ),
      'conversationUpdated' => 
      array (
        'name' => 'conversationUpdated',
        'parameters' => 
        array (
          'conversation' => 
          array (
            'name' => 'conversation',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Conversation',
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
            'startColumn' => 41,
            'endColumn' => 66,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'reason' => 
          array (
            'name' => 'reason',
            'default' => 
            array (
              'code' => '\'updated\'',
              'attributes' => 
              array (
                'startLine' => 41,
                'endLine' => 41,
                'startTokenPos' => 148,
                'startFilePos' => 1368,
                'endTokenPos' => 148,
                'endFilePos' => 1376,
              ),
            ),
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 69,
            'endColumn' => 94,
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
            'name' => 'void',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Broadcast the conversation summary. Loads the (small) participant set when
 * realtime is on so the payload can echo platform external user ids.
 */',
        'startLine' => 41,
        'endLine' => 55,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\RealtimeBroadcaster',
        'implementingClassName' => 'App\\Services\\RealtimeBroadcaster',
        'currentClassName' => 'App\\Services\\RealtimeBroadcaster',
        'aliasName' => NULL,
      ),
      'messageRead' => 
      array (
        'name' => 'messageRead',
        'parameters' => 
        array (
          'conversation' => 
          array (
            'name' => 'conversation',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Models\\Conversation',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 58,
            'endLine' => 58,
            'startColumn' => 9,
            'endColumn' => 34,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'reader' => 
          array (
            'name' => 'reader',
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
            'startLine' => 59,
            'endLine' => 59,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'lastReadMessageId' => 
          array (
            'name' => 'lastReadMessageId',
            'default' => NULL,
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
                      'name' => 'int',
                      'isIdentifier' => true,
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
            'startLine' => 60,
            'endLine' => 60,
            'startColumn' => 9,
            'endColumn' => 31,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'unreadCount' => 
          array (
            'name' => 'unreadCount',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'int',
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
            'endColumn' => 24,
            'parameterIndex' => 3,
            'isOptional' => false,
          ),
          'readAt' => 
          array (
            'name' => 'readAt',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Support\\Carbon',
                'isIdentifier' => false,
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
            'endColumn' => 22,
            'parameterIndex' => 4,
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
        'startLine' => 57,
        'endLine' => 69,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\RealtimeBroadcaster',
        'implementingClassName' => 'App\\Services\\RealtimeBroadcaster',
        'currentClassName' => 'App\\Services\\RealtimeBroadcaster',
        'aliasName' => NULL,
      ),
      'presenceChanged' => 
      array (
        'name' => 'presenceChanged',
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
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 37,
            'endColumn' => 54,
            'parameterIndex' => 0,
            'isOptional' => false,
          ),
          'user' => 
          array (
            'name' => 'user',
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
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 57,
            'endColumn' => 77,
            'parameterIndex' => 1,
            'isOptional' => false,
          ),
          'status' => 
          array (
            'name' => 'status',
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
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 80,
            'endColumn' => 93,
            'parameterIndex' => 2,
            'isOptional' => false,
          ),
          'seenAt' => 
          array (
            'name' => 'seenAt',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'Illuminate\\Support\\Carbon',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 75,
            'endLine' => 75,
            'startColumn' => 96,
            'endColumn' => 109,
            'parameterIndex' => 3,
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
 * Presence transition (online/offline). Callers invoke this ONLY on actual
 * status CHANGES; repeated same-status heartbeats must not re-fire.
 */',
        'startLine' => 75,
        'endLine' => 86,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\RealtimeBroadcaster',
        'implementingClassName' => 'App\\Services\\RealtimeBroadcaster',
        'currentClassName' => 'App\\Services\\RealtimeBroadcaster',
        'aliasName' => NULL,
      ),
      'enabled' => 
      array (
        'name' => 'enabled',
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
        'docComment' => NULL,
        'startLine' => 88,
        'endLine' => 91,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Services',
        'declaringClassName' => 'App\\Services\\RealtimeBroadcaster',
        'implementingClassName' => 'App\\Services\\RealtimeBroadcaster',
        'currentClassName' => 'App\\Services\\RealtimeBroadcaster',
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