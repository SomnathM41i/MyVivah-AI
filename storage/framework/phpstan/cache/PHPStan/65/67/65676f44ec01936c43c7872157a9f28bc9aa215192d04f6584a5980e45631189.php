<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\Conversation.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Conversation
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-f04438e492e0241bec54ceae2e1b74cdfbf82ec2e94dbd9e73186219a77d99cd',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Conversation',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/Conversation.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Conversation',
    'shortName' => 'Conversation',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Platform-scoped message thread (phase-3d; docs/database.md).
 *
 * Identity: ULID `public_id` (sent to clients); dedup: UNIQUE
 * (platform_id, participant_key) where participant_key is the normalized sorted
 * pair of map ids — so resolving the same pair always returns the same
 * conversation, regardless of argument order. Lifecycle is status-driven
 * (active/archived/closed); NO soft delete (keeps the dedup key authoritative).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 21,
    'endLine' => 115,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Model',
    'implementsClassNames' => 
    array (
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Database\\Eloquent\\Factories\\HasFactory',
      1 => 'Illuminate\\Database\\Eloquent\\Concerns\\HasUlids',
    ),
    'immediateConstants' => 
    array (
      'STATUS_ACTIVE' => 
      array (
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'name' => 'STATUS_ACTIVE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'active\'',
          'attributes' => 
          array (
            'startLine' => 36,
            'endLine' => 36,
            'startTokenPos' => 91,
            'startFilePos' => 1120,
            'endTokenPos' => 91,
            'endFilePos' => 1127,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 36,
        'endLine' => 36,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
      'STATUS_ARCHIVED' => 
      array (
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'name' => 'STATUS_ARCHIVED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'archived\'',
          'attributes' => 
          array (
            'startLine' => 38,
            'endLine' => 38,
            'startTokenPos' => 102,
            'startFilePos' => 1166,
            'endTokenPos' => 102,
            'endFilePos' => 1175,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 38,
        'endLine' => 38,
        'startColumn' => 5,
        'endColumn' => 46,
      ),
      'STATUS_CLOSED' => 
      array (
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'name' => 'STATUS_CLOSED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'closed\'',
          'attributes' => 
          array (
            'startLine' => 40,
            'endLine' => 40,
            'startTokenPos' => 113,
            'startFilePos' => 1212,
            'endTokenPos' => 113,
            'endFilePos' => 1219,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 40,
        'endLine' => 40,
        'startColumn' => 5,
        'endColumn' => 42,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'platform_id\', \'participant_key\', \'status\', \'last_message_id\', \'last_message_at\', \'last_message_excerpt\', \'last_message_sender_external_user_map_id\']',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 53,
            'startTokenPos' => 124,
            'startFilePos' => 1290,
            'endTokenPos' => 147,
            'endFilePos' => 1502,
          ),
        ),
        'docComment' => '/**
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 53,
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
      'uniqueIds' => 
      array (
        'name' => 'uniqueIds',
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
 * ULID columns — only `public_id` is exposed to clients.
 *
 * @return list<string>
 */',
        'startLine' => 31,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
        'aliasName' => NULL,
      ),
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
 * @return array<string, string>
 */',
        'startLine' => 58,
        'endLine' => 64,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
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
        'startLine' => 71,
        'endLine' => 74,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
        'aliasName' => NULL,
      ),
      'participants' => 
      array (
        'name' => 'participants',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * The participants (two rows at MVP; membership gate for authorization).
 *
 * @return HasMany<ConversationParticipant, $this>
 */',
        'startLine' => 81,
        'endLine' => 84,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
        'aliasName' => NULL,
      ),
      'messages' => 
      array (
        'name' => 'messages',
        'parameters' => 
        array (
        ),
        'returnsReference' => false,
        'returnType' => 
        array (
          'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
          'data' => 
          array (
            'name' => 'Illuminate\\Database\\Eloquent\\Relations\\HasMany',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * All messages in the conversation (history/cursor pagination).
 *
 * @return HasMany<Message, $this>
 */',
        'startLine' => 91,
        'endLine' => 94,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
        'aliasName' => NULL,
      ),
      'lastMessage' => 
      array (
        'name' => 'lastMessage',
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
 * The most recent message (denormalized reference kept in sync on send).
 *
 * @return BelongsTo<Message, $this>
 */',
        'startLine' => 101,
        'endLine' => 104,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
        'aliasName' => NULL,
      ),
      'lastMessageSender' => 
      array (
        'name' => 'lastMessageSender',
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
 * The external user who sent the last message (denormalized reference).
 *
 * @return BelongsTo<ExternalUserMap, $this>
 */',
        'startLine' => 111,
        'endLine' => 114,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Conversation',
        'implementingClassName' => 'App\\Models\\Conversation',
        'currentClassName' => 'App\\Models\\Conversation',
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