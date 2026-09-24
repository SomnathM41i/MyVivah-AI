<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Models\Message.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Models\Message
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-8277cdf73d3c6d399928aa00a2bb6d70e8633a0b9a9c4bc66b26faaced1ecbf3',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Models\\Message',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Models/Message.php',
      ),
    ),
    'namespace' => 'App\\Models',
    'name' => 'App\\Models\\Message',
    'shortName' => 'Message',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * A single chat entry (phase-3d; docs/database.md messages).
 *
 * STRICTLY platform-scoped; idempotent via UNIQUE
 * (platform_id, conversation_id, client_message_id). `client_message_id` is the
 * widget-generated dedup key: retrying a failed send returns the original row.
 * Soft delete reserved for a future recall scenario (nothing deletes at MVP).
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 20,
    'endLine' => 98,
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
      2 => 'Illuminate\\Database\\Eloquent\\SoftDeletes',
    ),
    'immediateConstants' => 
    array (
      'TYPE_TEXT' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'TYPE_TEXT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'text\'',
          'attributes' => 
          array (
            'startLine' => 35,
            'endLine' => 35,
            'startTokenPos' => 94,
            'startFilePos' => 1024,
            'endTokenPos' => 94,
            'endFilePos' => 1029,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 35,
        'endLine' => 35,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'TYPE_IMAGE' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'TYPE_IMAGE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'image\'',
          'attributes' => 
          array (
            'startLine' => 37,
            'endLine' => 37,
            'startTokenPos' => 105,
            'startFilePos' => 1063,
            'endTokenPos' => 105,
            'endFilePos' => 1069,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 37,
        'endLine' => 37,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'TYPE_FILE' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'TYPE_FILE',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'file\'',
          'attributes' => 
          array (
            'startLine' => 39,
            'endLine' => 39,
            'startTokenPos' => 116,
            'startFilePos' => 1102,
            'endTokenPos' => 116,
            'endFilePos' => 1107,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 39,
        'endLine' => 39,
        'startColumn' => 5,
        'endColumn' => 36,
      ),
      'TYPE_SYSTEM' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'TYPE_SYSTEM',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'system\'',
          'attributes' => 
          array (
            'startLine' => 41,
            'endLine' => 41,
            'startTokenPos' => 127,
            'startFilePos' => 1142,
            'endTokenPos' => 127,
            'endFilePos' => 1149,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 41,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 40,
      ),
      'STATUS_SENT' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'STATUS_SENT',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'sent\'',
          'attributes' => 
          array (
            'startLine' => 43,
            'endLine' => 43,
            'startTokenPos' => 138,
            'startFilePos' => 1184,
            'endTokenPos' => 138,
            'endFilePos' => 1189,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 43,
        'endLine' => 43,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
      'STATUS_DELIVERED' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'STATUS_DELIVERED',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'delivered\'',
          'attributes' => 
          array (
            'startLine' => 45,
            'endLine' => 45,
            'startTokenPos' => 149,
            'startFilePos' => 1229,
            'endTokenPos' => 149,
            'endFilePos' => 1239,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 45,
        'endLine' => 45,
        'startColumn' => 5,
        'endColumn' => 48,
      ),
      'STATUS_READ' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'STATUS_READ',
        'modifiers' => 1,
        'type' => NULL,
        'value' => 
        array (
          'code' => '\'read\'',
          'attributes' => 
          array (
            'startLine' => 47,
            'endLine' => 47,
            'startTokenPos' => 160,
            'startFilePos' => 1274,
            'endTokenPos' => 160,
            'endFilePos' => 1279,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 47,
        'endLine' => 47,
        'startColumn' => 5,
        'endColumn' => 38,
      ),
    ),
    'immediateProperties' => 
    array (
      'fillable' => 
      array (
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'name' => 'fillable',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '[\'platform_id\', \'conversation_id\', \'sender_external_user_map_id\', \'type\', \'status\', \'content\', \'client_message_id\']',
          'attributes' => 
          array (
            'startLine' => 52,
            'endLine' => 60,
            'startTokenPos' => 171,
            'startFilePos' => 1350,
            'endTokenPos' => 194,
            'endFilePos' => 1527,
          ),
        ),
        'docComment' => '/**
 * @var list<string>
 */',
        'attributes' => 
        array (
        ),
        'startLine' => 52,
        'endLine' => 60,
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
        'startLine' => 30,
        'endLine' => 33,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'currentClassName' => 'App\\Models\\Message',
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
        'startLine' => 65,
        'endLine' => 71,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 2,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'currentClassName' => 'App\\Models\\Message',
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
        'startLine' => 76,
        'endLine' => 79,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'currentClassName' => 'App\\Models\\Message',
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
        'startLine' => 84,
        'endLine' => 87,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'currentClassName' => 'App\\Models\\Message',
        'aliasName' => NULL,
      ),
      'sender' => 
      array (
        'name' => 'sender',
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
 * The external user who sent this message.
 *
 * @return BelongsTo<ExternalUserMap, $this>
 */',
        'startLine' => 94,
        'endLine' => 97,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Models',
        'declaringClassName' => 'App\\Models\\Message',
        'implementingClassName' => 'App\\Models\\Message',
        'currentClassName' => 'App\\Models\\Message',
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