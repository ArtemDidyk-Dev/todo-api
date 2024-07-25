<?php

namespace App\Factory;

use App\Entity\Passphrase;
use Zenstruck\Foundry\Persistence\PersistentProxyObjectFactory;

/**
 * @extends PersistentProxyObjectFactory<Passphrase>
 */
final class PassphraseFactory extends PersistentProxyObjectFactory
{
    public static function class(): string
    {
        return Passphrase::class;
    }

    protected function defaults(): array|callable
    {
        return [
            'name' => self::faker()->unique()->word(),
        ];
    }

    protected function initialize(): static
    {
        return $this
            // ->afterInstantiate(function(Passphrase $passphrase): void {})
        ;
    }
}
