<?php

declare(strict_types=1);

use Rector\Core\Configuration\Option;
use Rector\Laravel\Set\LaravelLevelSetList;
use Rector\Php74\Rector\Property\TypedPropertyRector;
use Rector\Set\ValueObject\LevelSetList;
use Symfony\Component\DependencyInjection\Loader\Configurator\ContainerConfigurator;

return static function (ContainerConfigurator $containerConfigurator): void {
    // get parameters
    $parameters = $containerConfigurator->parameters();
    $parameters->set(Option::PATHS, [
        __DIR__ . '/app'
    ]);

    $containerConfigurator->import(LaravelLevelSetList::UP_TO_LARAVEL_80);

    // Define what rule sets will be applied
    $containerConfigurator->import(LevelSetList::UP_TO_PHP_74);
};
