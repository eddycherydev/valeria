<?php
namespace Core\Attributes;

use Attribute;

#[Attribute(Attribute::TARGET_CLASS | Attribute::TARGET_METHOD)]
class Middleware
{
    public array $middlewares;

    public function __construct(array|string $middlewares)
    {
        $this->middlewares = is_array($middlewares) ? $middlewares : [$middlewares];
    }
}
