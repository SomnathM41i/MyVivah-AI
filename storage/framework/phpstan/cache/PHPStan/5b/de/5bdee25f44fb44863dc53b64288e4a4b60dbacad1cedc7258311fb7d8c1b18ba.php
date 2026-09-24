<?php declare(strict_types = 1);

// osfsl-C:/Users/Somnath Mali/Desktop/MyVivah-AI/database/factories/ConversationParticipantFactory.php-PHPStan\BetterReflection\Reflection\ReflectionClass-Database\Factories\ConversationParticipantFactory
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-28b2323ec634329b821010e095d29199c64d93261d475c5b0baad9ffcb46def4-8.2.12-6.70.0.6',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'Database\\Factories\\ConversationParticipantFactory',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/database/factories/ConversationParticipantFactory.php',
      ),
    ),
    'namespace' => 'Database\\Factories',
    'name' => 'Database\\Factories\\ConversationParticipantFactory',
    'shortName' => 'ConversationParticipantFactory',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * @extends Factory<ConversationParticipant>
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 14,
    'endLine' => 49,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Database\\Eloquent\\Factories\\Factory',
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
      'definition' => 
      array (
        'name' => 'definition',
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
 * Standalone default; prefer forSeat() so conversation/platform/map are
 * guaranteed to sit on the same tenant (isolation invariant).
 *
 * @return array<string, mixed>
 */',
        'startLine' => 22,
        'endLine' => 34,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\ConversationParticipantFactory',
        'implementingClassName' => 'Database\\Factories\\ConversationParticipantFactory',
        'currentClassName' => 'Database\\Factories\\ConversationParticipantFactory',
        'aliasName' => NULL,
      ),
      'forSeat' => 
      array (
        'name' => 'forSeat',
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
            'startColumn' => 29,
            'endColumn' => 54,
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
            'startLine' => 41,
            'endLine' => 41,
            'startColumn' => 57,
            'endColumn' => 76,
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
            'name' => 'static',
            'isIdentifier' => false,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => '/**
 * Seat a specific external user in a specific conversation. The map MUST
 * belong to the conversation\'s platform (callers derive it from the same
 * verified token context), keeping every FK and the isolation scope aligned.
 */',
        'startLine' => 41,
        'endLine' => 48,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'Database\\Factories',
        'declaringClassName' => 'Database\\Factories\\ConversationParticipantFactory',
        'implementingClassName' => 'Database\\Factories\\ConversationParticipantFactory',
        'currentClassName' => 'Database\\Factories\\ConversationParticipantFactory',
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