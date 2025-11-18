<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum representation of the various task statuses.
 *
 * @extends Enum<string>
 */
final class TaskStatus extends Enum
{
    /**
     * In progress status
     *
     * @var string
     */
    public const IN_PROGRESS = 'in_progress';

    /**
     * Completed status
     *
     * @var string
     */
    public const COMPLETED = 'completed';
}

