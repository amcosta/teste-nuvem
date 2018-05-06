<?php

namespace TiendaNube\Checkout\Service\Container;

use Psr\Container\ContainerInterface;
use Psr\Container\NotFoundExceptionInterface;

class Container implements ContainerInterface
{
    private $entries = [];

    /**
     * Finds an entry of the container by its identifier and returns it.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @throws NotFoundExceptionInterface  No entry was found for **this** identifier.
     * @throws ContainerException Error while retrieving the entry.
     *
     * @return mixed Entry.
     */
    public function get($id)
    {
        if (!$this->has($id)) {
            throw new NotFoundException(sprintf('Not found a entry with "%s"', $id));
        }

        return $this->entries[$id];
    }

    /**
     * Returns true if the container can return an entry for the given identifier.
     * Returns false otherwise.
     *
     * `has($id)` returning true does not mean that `get($id)` will not throw an exception.
     * It does however mean that `get($id)` will not throw a `NotFoundExceptionInterface`.
     *
     * @param string $id Identifier of the entry to look for.
     *
     * @return bool
     */
    public function has($id)
    {
        return array_key_exists($id, $this->entries);
    }

    /**
     * @param string $id
     * @param $value
     * @throws ContainerException
     */
    public function set(string $id, $value)
    {
        if ($this->has($id)) {
            throw new ContainerException(sprintf('This container already have this entry "%s"', $id));
        }

        $this->entries[$id] = $value;
    }
}