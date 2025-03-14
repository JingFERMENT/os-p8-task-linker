<?php

namespace App\Factory;

use App\Entity\Project;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Project>
 */
final class ProjectFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct() {}

    public static function class(): string
    {
        return Project::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {

        return function () {
            $createdAt = \DateTimeImmutable::createFromMutable(self::faker()->dateTimeBetween('2024-01-01', '2025-12-31'));
            $deadline = (clone $createdAt)->modify('+' . self::faker()->numberBetween(1, 90) . ' days');

            return [
                'createdAt' => $createdAt,
                'deadline' => $deadline,
                'isArchived' => self::faker()->boolean(),
                'name' => self::faker()->unique()->words(2, true),
            ];
        };
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Project $project): void {})
        ;
    }
}
