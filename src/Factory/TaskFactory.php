<?php

namespace App\Factory;

use App\Entity\Task;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Task>
 */
final class TaskFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */
    public function __construct()
    {
    }

    public static function class(): string
    {
        return Task::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        $project = ProjectFactory::new()->create(); // Ensure task is linked to a project

        // convert to string
        $projectCreatedAt = $project->getCreatedAt()->format('Y-m-d H:i:s');
        $projectDeadline = $project->getDeadline()->format('Y-m-d H:i:s');
        $taskDeadline = self::faker()->dateTimeBetween($projectCreatedAt, $projectDeadline);
        
        return [
            'project'  => $project,
            'deadline' => $taskDeadline,
            'description' => self::faker()->text(),
            'title' => self::faker()->unique()->sentence(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Task $task): void {})
        ;
    }
}
