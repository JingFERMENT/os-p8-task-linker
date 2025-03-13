<?php

namespace App\Factory;

use App\Entity\Employee;
use App\Enum\ContractName;
use App\Enum\RoleName;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @extends PersistentProxyObjectFactory<Employee>
 */
final class EmployeeFactory extends PersistentProxyObjectFactory
{
    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#factories-as-services
     *
     * @todo inject services if required
     */

    private static ?string $hashedPassword = null;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        parent::__construct();

        // Hash the password once and store it for reuse
        if (self::$hashedPassword === null) {
            self::$hashedPassword = $passwordHasher->hashPassword(new Employee(), 'password123');
        }
    }
    

    public static function class(): string
    {
        return Employee::class;
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#model-factories
     *
     * @todo add your default values here
     */
    protected function defaults(): array|callable
    {
        return [
            'contract' => self::faker()->randomElement(ContractName::cases()),
            'email' => self::faker()->text(255),
            'firstname' => self::faker()->text(255),
            'isActif' => self::faker()->boolean(),
            'lastname' => self::faker()->text(255),
            'password' => self::$hashedPassword,
            'role' => self::faker()->randomElement(RoleName::cases()),
            'startDate' => self::faker()->dateTime(),
        ];
    }

    /**
     * @see https://symfony.com/bundles/ZenstruckFoundryBundle/current/index.html#initialization
     */
    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Employee $employee): void {})
        ;
    }
}
