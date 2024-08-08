<?php

declare(strict_types=1);

namespace App\Serializer;

class AccessGroup
{
    public const string USER_READ = 'user:read';

    public const string PASSPHRASE_CREATE = 'passphrase:create';

    public const string PASSPHRASE_CREATE_RESPONSE = 'passphrase:create:response';

    public const string TASKS_READ = 'task:read';

    public const string TASKS_CREATE = 'task:create';
}