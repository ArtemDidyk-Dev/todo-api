<?php

declare(strict_types=1);

namespace App\Exception;

use Symfony\Component\HttpKernel\Attribute\WithHttpStatus;
use Throwable;

#[WithHttpStatus(400)]
final class BadPassphraseException extends \Exception
{
   public function __construct(string $message = '', int $code = 0, ?Throwable $previous = null)
   {
       parent::__construct($message, $code, $previous);
   }
}
