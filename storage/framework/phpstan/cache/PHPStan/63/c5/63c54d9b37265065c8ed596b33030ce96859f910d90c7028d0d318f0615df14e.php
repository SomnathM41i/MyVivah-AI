<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\ConversationParticipant.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\ConversationParticipant
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-f83dfa7adc742e2d7090d47293281c8b0b64d34b1d006bff7bc1bb20619c61f2',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\ConversationParticipant',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/ConversationParticipant.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\ConversationParticipant',
    'shortName' => 'ConversationParticipant',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Conversation membership + per-user read state (phase-3d).
 *
 * One seat per (conversation, external user map) — UNIQUE constraint backed.
 * Read state lives here so unread listing is O(1) and is updated transactionally
 * with message send (increment) and read (reset to 0) — it never drifts.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 77,
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
        'declaringClassName' => 'App\\Models\\ConversationParticipant',
        'implementingClassName' => 'App\\Models\\ConversationParticipant',
        'name' => 'table',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'conversation_participants\'',
          'attributes' => 
          array (
            'startLine' => 22,
            'endLine' => 22,
            'startTokenPos' => 52,
            'startFilePos' => 694,
            'endTokenPos' => 52,
            'endFilePos' => 720,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 22,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 51,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\ConversationParticipant',
        'implementingClassName' => 'App\\Models\\ConversationParticipant',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'conversation_id\', \'platform_id\', \'external_user_map_id\', \'last_read_message_id\', \'last_read_at\', \'unread_count\', \'joined_at\', \'left_at\']',
          'attributes' => 
          array (
            'startLine' => 27,
            'endLine' => 36,
            'startTokenPos' => 63,
            'startFilePos' => 791,
            'endTokenPos' => 89,
            'endFilePos' => 999,
          ),
        ),
        'docComment' => '/**
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 27,
        'endLine' => 36,
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
 * @return array<string, string>
 */',
        'startLine' => 41,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConversationParticipant',
        'implementingClassName' => 'App\\Models\\ConversationParticipant',
        'currentClassName' => 'App\\Models\\ConversationParticipant',
        'aliasName' => NULL,
      ),
      'conversation' => 
      array (
        'name' => 'conversation',
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
 * @return BelongsTo<Conversation, $this>
 */',
        'startLine' => 55,
        'endLine' => 58,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConversationParticipant',
        'implementingClassName' => 'App\\Models\\ConversationParticipant',
        'currentClassName' => 'App\\Models\\ConversationParticipant',
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
 * @return BelongsTo<Platform, $this>
 */',
        'startLine' => 63,
        'endLine' => 66,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConversationParticipant',
        'implementingClassName' => 'App\\Models\\ConversationParticipant',
        'currentClassName' => 'App\\Models\\ConversationParticipant',
        'aliasName' => NULL,
      ),
      'externalUserMap' => 
      array (
        'name' => 'externalUserMap',
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
 * The external user occupying this seat (platform-scoped identity map).
 *
 * @return BelongsTo<ExternalUserMap, $this>
 */',
        'startLine' => 73,
        'endLine' => 76,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\ConversationParticipant',
        'implementingClassName' => 'App\\Models\\ConversationParticipant',
        'currentClassName' => 'App\\Models\\ConversationParticipant',
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