<?php

namespace Oilastudio\Oneaccount;

interface EngineInterface
{
    public function set(string $key, array $value): bool;

    public function get(string $key): array;
}
