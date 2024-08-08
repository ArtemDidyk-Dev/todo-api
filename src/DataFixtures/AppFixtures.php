<?php

declare(strict_types=1);

namespace App\DataFixtures;

use App\Factory\PassphraseFactory;
use App\Factory\TaskFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $passphrases = PassphraseFactory::createMany(10);

        foreach ($passphrases as $passphrase) {
            TaskFactory::createMany(15, [
                'passphrase' => $passphrase,
            ]);
        }

    }
}
