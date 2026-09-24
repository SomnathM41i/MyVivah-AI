<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Events\ConversationUpdated.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Events\ConversationUpdated
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-3646e512767f63a94b1908a3b4ced1d02d261f1e48808fd5eed94904c0085606',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Events\\ConversationUpdated',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Events/ConversationUpdated.php',
      ),
    ),
    'namespace' => 'App\\Events',
    'name' => 'App\\Events\\ConversationUpdated',
    'shortName' => 'ConversationUpdated',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Fired when a conversation is created or its denormalized summary changes
 * (post-commit) — Phase 3E. Clients use it to refresh conversation-list rows;
 * message sends ALSO produce this after the last-message metadata updates.
 *
 * `reason` is a client hint: `created` (new thread) or `updated` (metadata
 * changed). Participants are echoed as the platform\'s external user ids only.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 18,
    'endLine' => 71,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'App\\Events\\RealtimeEvent',
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
      'conversation' => 
      array (
        'declaringClassName' => 'App\\Events\\ConversationUpdated',
        'implementingClassName' => 'App\\Events\\ConversationUpdated',
        'name' => 'conversation',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'App\\Models\\Conversation',
            'isIdentifier' => false,
          ),
        ),
        'default' => NULL,
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 21,
        'endLine' => 21,
        'startColumn' => 9,
        'endColumn' => 50,
        'isPromoted' => true,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'reason' => 
      array (
        'declaringClassName' => 'App\\Events\\ConversationUpdated',
        'implementingClassName' => 'App\\Events\\ConversationUpdated',
        'name' => 'reason',
        'modifiers' => 2177,
        'type' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'string',
            'isIdentifier' => true,
          ),
        ),
        'default' => 
        array (
          'code' => '\'updated\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 65,
            'startFilePos' => 738,
            'endTokenPos' => 65,
            'endFilePos' => 746,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 9,
        'endColumn' => 50,
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 21,
            'endLine' => 21,
            'startColumn' => 9,
            'endColumn' => 50,
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
                'startLine' => 22,
                'endLine' => 22,
                'startTokenPos' => 65,
                'startFilePos' => 738,
                'endTokenPos' => 65,
                'endFilePos' => 746,
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
            'isPromoted' => true,
            'attributes' => 
            array (
            ),
            'startLine' => 22,
            'endLine' => 22,
            'startColumn' => 9,
            'endColumn' => 50,
            'parameterIndex' => 1,
            'isOptional' => true,
          ),
        ),
        'returnsReference' => false,
        'returnType' => NULL,
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 20,
        'endLine' => 23,
        'startColumn' => 5,
        'endColumn' => 8,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Events',
        'declaringClassName' => 'App\\Events\\ConversationUpdated',
        'implementingClassName' => 'App\\Events\\ConversationUpdated',
        'currentClassName' => 'App\\Events\\ConversationUpdated',
        'aliasName' => NULL,
      ),
      'broadcastOn' => 
      array (
        'name' => 'broadcastOn',
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
 * @return array<int, PrivateChannel>
 */',
        'startLine' => 28,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Events',
        'declaringClassName' => 'App\\Events\\ConversationUpdated',
        'implementingClassName' => 'App\\Events\\ConversationUpdated',
        'currentClassName' => 'App\\Events\\ConversationUpdated',
        'aliasName' => NULL,
      ),
      'broadcastAs' => 
      array (
        'name' => 'broadcastAs',
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
        'startLine' => 35,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Events',
        'declaringClassName' => 'App\\Events\\ConversationUpdated',
        'implementingClassName' => 'App\\Events\\ConversationUpdated',
        'currentClassName' => 'App\\Events\\ConversationUpdated',
        'aliasName' => NULL,
      ),
      'broadcastWith' => 
      array (
        'name' => 'broadcastWith',
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
        'docComment' => NULL,
        'startLine' => 40,
        'endLine' => 70,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Events',
        'declaringClassName' => 'App\\Events\\ConversationUpdated',
        'implementingClassName' => 'App\\Events\\ConversationUpdated',
        'currentClassName' => 'App\\Events\\ConversationUpdated',
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