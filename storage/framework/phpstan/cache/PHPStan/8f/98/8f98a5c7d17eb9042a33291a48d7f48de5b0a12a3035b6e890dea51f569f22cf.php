<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Events\RealtimeEvent.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Events\RealtimeEvent
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-574021d661a2417db51fe9cb92c611d0bc9f2b902e37ead9edbcde48cda7eabb',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Events\\RealtimeEvent',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Events/RealtimeEvent.php',
      ),
    ),
    'namespace' => 'App\\Events',
    'name' => 'App\\Events\\RealtimeEvent',
    'shortName' => 'RealtimeEvent',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 64,
    'docComment' => '/**
 * Base for every MyVivahAI realtime chat event (Phase 3E).
 *
 * Behaviour guarantees:
 *   - ShouldBroadcastNow   — dispatched INLINE (no queue worker required), right
 *                            after the REST request that caused it commits.
 *   - Commit ordering      — every call site (ChatMessageService,
 *                            ChatConversationService, PresenceService) fires via
 *                            RealtimeBroadcaster AFTER its own DB transaction
 *                            has closed, so MySQL (source of truth) always holds
 *                            the row BEFORE any broadcast. No queue worker, no
 *                            transaction-manager indirection.
 *   - ShouldRescue         — a dead/unreachable realtime transport throws a
 *                            BroadcastException that is swallowed + logged; the
 *                            REST request never fails because of realtime.
 *   - broadcastWhen        — global kill-switch shared by every event
 *                            (config(\'chat.realtime.enabled\')), so a fully
 *                            REST-only deployment dispatches nothing at all.
 *
 * Payload rule (AGENTS.md §15 / security.md): events carry ONLY public ids and
 * minimal display data — never API secrets, PASETO tokens, private keys,
 * internal primary keys, full profiles or audit material.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 34,
    'endLine' => 51,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => NULL,
    'implementsClassNames' => 
    array (
      0 => 'Illuminate\\Contracts\\Broadcasting\\ShouldBroadcastNow',
      1 => 'Illuminate\\Contracts\\Broadcasting\\ShouldRescue',
    ),
    'traitClassNames' => 
    array (
      0 => 'Illuminate\\Foundation\\Events\\Dispatchable',
      1 => 'Illuminate\\Broadcasting\\InteractsWithSockets',
      2 => 'Illuminate\\Queue\\SerializesModels',
    ),
    'immediateConstants' => 
    array (
    ),
    'immediateProperties' => 
    array (
    ),
    'immediateMethods' => 
    array (
      'broadcastWhen' => 
      array (
        'name' => 'broadcastWhen',
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
        'startLine' => 38,
        'endLine' => 41,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Events',
        'declaringClassName' => 'App\\Events\\RealtimeEvent',
        'implementingClassName' => 'App\\Events\\RealtimeEvent',
        'currentClassName' => 'App\\Events\\RealtimeEvent',
        'aliasName' => NULL,
      ),
      'broadcastConnections' => 
      array (
        'name' => 'broadcastConnections',
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
 * The connection the broadcast runs on. Default (null) = the configured
 * BROADCAST_CONNECTION; when realtime is disabled nothing is queued at all.
 */',
        'startLine' => 47,
        'endLine' => 50,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Events',
        'declaringClassName' => 'App\\Events\\RealtimeEvent',
        'implementingClassName' => 'App\\Events\\RealtimeEvent',
        'currentClassName' => 'App\\Events\\RealtimeEvent',
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