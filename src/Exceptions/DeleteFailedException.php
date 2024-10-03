<?php

namespace Codedor\MediaLibrary\Exceptions;

use Illuminate\Support\Collection;
use Throwable;

class DeleteFailedException extends \Exception
{
    protected $failedRecords;

    public function __construct(Collection $failedRecords, $message = 'Delete operation failed for some records', $code = 0, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
        $this->failedRecords = $failedRecords;
    }

    public function getFailedRecords()
    {
        return $this->failedRecords;
    }
}
