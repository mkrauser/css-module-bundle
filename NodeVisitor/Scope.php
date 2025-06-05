<?php

/*
 * This file is part of the CssModuleBundle package.
 * Copyright (c) Matthias Krauser <matthias@krauser.eu>
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace MaK\CssModuleBundle\NodeVisitor;

use LogicException;
use function array_key_exists;

/**
 * this file is almost a copy of Scope.php from symfony/twig-bridge.
 */
class Scope
{
    /** @var array<string, mixed> */
    private array $data = [];
    
    private bool $left = false;

    public function __construct(private readonly ?self $parent = null)
    {
    }

    /**
     * Opens a new child scope.
     */
    public function enter(): self
    {
        return new self($this);
    }

    /**
     * Closes current scope and returns parent one.
     */
    public function leave(): ?self
    {
        $this->left = true;

        return $this->parent;
    }

    /**
     * Stores data into current scope.
     *
     * @return $this
     *
     * @throws LogicException
     */
    public function set(string $key, mixed $value): static
    {
        if ($this->left) {
            throw new LogicException('Left scope is not mutable.');
        }

        $this->data[$key] = $value;

        return $this;
    }

    /**
     * Tests if a data is visible from current scope.
     */
    public function has(string $key): bool
    {
        if (array_key_exists($key, $this->data)) {
            return true;
        }

        if (!$this->parent instanceof Scope) {
            return false;
        }

        return $this->parent->has($key);
    }

    /**
     * Returns data visible from current scope.
     */
    public function get(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }

        if (!$this->parent instanceof Scope) {
            return $default;
        }

        return $this->parent->get($key, $default);
    }
}
