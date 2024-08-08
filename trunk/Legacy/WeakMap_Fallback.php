<?php

if (!class_exists('WeakMap')) {
    final class WeakMap implements ArrayAccess, Countable, IteratorAggregate {
        private array $storage = [];

        public function offsetExists($offset): bool {
            return isset($this->storage[spl_object_id($offset)]);
        }

        public function offsetGet($offset) {
            return $this->storage[spl_object_id($offset)] ?? null;
        }

        public function offsetSet($offset, $value): void {
            $this->storage[spl_object_id($offset)] = $value;
        }

        public function offsetUnset($offset): void {
            unset($this->storage[spl_object_id($offset)]);
        }

        public function count(): int {
            return count($this->storage);
        }

        public function getIterator(): Traversable {
            foreach ($this->storage as $key => $value) {
                yield $key => $value;
            }
        }
    }
}