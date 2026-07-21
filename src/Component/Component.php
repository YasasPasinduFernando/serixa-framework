<?php

declare(strict_types=1);

namespace Serixa\Component;

use Serixa\Contracts\ComponentInterface;
use Serixa\Widget\Widget as BaseWidget;

/**
 * @deprecated Use Serixa\Widget\Widget. Alias retained for Milestone 1/2 compatibility.
 */
abstract class Component extends BaseWidget implements ComponentInterface
{
}
