<?php declare(strict_types = 1);

// odsl-C:\Users\Somnath Mali\Desktop\MyVivah-AI\app\Console\Commands\IntegrationOnboardCommand.php-PHPStan\BetterReflection\Reflection\ReflectionClass-App\Console\Commands\IntegrationOnboardCommand
return \PHPStan\Cache\CacheItem::__set_state(array(
   'variableKey' => 'v2-6.70.0.6-8.2.12-5b95bae8a6a89b68e8c554a85ea39739444977bfcd99c41f453532ef7c37adc7',
   'data' => 
  array (
    'locatedSource' => 
    array (
      'class' => 'PHPStan\\BetterReflection\\SourceLocator\\Located\\LocatedSource',
      'data' => 
      array (
        'name' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'filename' => 'C:/Users/Somnath Mali/Desktop/MyVivah-AI/app/Console/Commands/IntegrationOnboardCommand.php',
      ),
    ),
    'namespace' => 'App\\Console\\Commands',
    'name' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
    'shortName' => 'IntegrationOnboardCommand',
    'isInterface' => false,
    'isTrait' => false,
    'isEnum' => false,
    'isBackedEnum' => false,
    'modifiers' => 0,
    'docComment' => '/**
 * Idempotent programmatic onboarding for a platform integration (Phase 3C).
 *
 *   php artisan integration:onboard matrimonyband --name="Matrimony Band"
 *
 * Prints the platform `client_id` and, only on first provisioning, the
 * ONE-TIME `client_secret`. The secret is never stored or recoverable — if
 * lost, rotate keys: `POST /api/v1/integration/keys/rotate`.
 */',
    'attributes' => 
    array (
    ),
    'startLine' => 17,
    'endLine' => 55,
    'startColumn' => 1,
    'endColumn' => 1,
    'parentClassName' => 'Illuminate\\Console\\Command',
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
      'signature' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'implementingClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'name' => 'signature',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'integration:onboard
                            {slug : unique platform slug (URL-safe, used as the PASETO audience)}
                            {--name= : display name for the platform}
                            {--service=realtime_chat : service key to entitle on first provisioning}\'',
          'attributes' => 
          array (
            'startLine' => 19,
            'endLine' => 22,
            'startTokenPos' => 35,
            'startFilePos' => 572,
            'endTokenPos' => 35,
            'endFilePos' => 861,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 19,
        'endLine' => 22,
        'startColumn' => 5,
        'endColumn' => 102,
        'isPromoted' => false,
        'declaredAtCompileTime' => true,
        'immediateVirtual' => false,
        'immediateHooks' => 
        array (
        ),
      ),
      'description' => 
      array (
        'declaringClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'implementingClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'name' => 'description',
        'modifiers' => 2,
        'type' => NULL,
        'default' => 
        array (
          'code' => '\'Provision a platform API integration (idempotent) and print one-time credentials.\'',
          'attributes' => 
          array (
            'startLine' => 24,
            'endLine' => 24,
            'startTokenPos' => 44,
            'startFilePos' => 894,
            'endTokenPos' => 44,
            'endFilePos' => 976,
          ),
        ),
        'docComment' => NULL,
        'attributes' => 
        array (
        ),
        'startLine' => 24,
        'endLine' => 24,
        'startColumn' => 5,
        'endColumn' => 113,
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
      'handle' => 
      array (
        'name' => 'handle',
        'parameters' => 
        array (
          'onboard' => 
          array (
            'name' => 'onboard',
            'default' => NULL,
            'type' => 
            array (
              'class' => 'PHPStan\\BetterReflection\\Reflection\\ReflectionNamedType',
              'data' => 
              array (
                'name' => 'App\\Services\\PlatformOnboardingService',
                'isIdentifier' => false,
              ),
            ),
            'isVariadic' => false,
            'byRef' => false,
            'isPromoted' => false,
            'attributes' => 
            array (
            ),
            'startLine' => 26,
            'endLine' => 26,
            'startColumn' => 28,
            'endColumn' => 61,
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
            'name' => 'int',
            'isIdentifier' => true,
          ),
        ),
        'attributes' => 
        array (
        ),
        'docComment' => NULL,
        'startLine' => 26,
        'endLine' => 54,
        'startColumn' => 5,
        'endColumn' => 5,
        'couldThrow' => false,
        'isClosure' => false,
        'isGenerator' => false,
        'isVariadic' => false,
        'modifiers' => 1,
        'namespace' => 'App\\Console\\Commands',
        'declaringClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'implementingClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
        'currentClassName' => 'App\\Console\\Commands\\IntegrationOnboardCommand',
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