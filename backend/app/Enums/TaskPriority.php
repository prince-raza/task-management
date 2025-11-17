<?php

declare(strict_types=1);

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * Enum representation of the various task priorities.
 *
 * @extends Enum<string>
 */
final class TaskPriority extends Enum
{
    /**
     * Low priority
     *
     * @var string
     */
    public const LOW = 'low';

    /**
     * Medium priority
     *
     * @var string
     */
    public const MEDIUM = 'medium';

    /**
     * High priority
     *
     * @var string
     */
    public const HIGH = 'high';
}

