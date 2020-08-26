<?php


final class FileEngine implements EngineInterface
{

    public function set(string $key, array $value): bool
    {
        return false !== file_put_contents($key . '.txt', json_encode($value));
    }

    public function get(string $key): array
    {
        $data = file_get_contents($key . '.txt');
        unlink($key . '.txt');

        return json_decode($data, true);
    }
}