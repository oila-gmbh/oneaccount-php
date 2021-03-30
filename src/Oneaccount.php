<?php

namespace Oilastudio\Oneaccount;

use GuzzleHttp\Client;
use InvalidArgumentException;
use RuntimeException;
use Throwable;

final class Oneaccount
{
    private $client;
    /**
     * @var EngineInterface
     */
    private $engine;

    private $verifyURL = "https://api.oneaccount.app/widget/verify";

    public function __construct(EngineInterface $engine)
    {
        $this->client = new Client();
        $this->engine = $engine;
    }

    public function auth()
    {
        $data = json_decode(file_get_contents('php://input'), true);
        $data = $data ?: $_POST;
        $headers = getallheaders();

        if (!isset($data['uuid'])) {
            throw new InvalidArgumentException("the uuid field is required");
        }
        if (!isset($headers['Authorization'])) {
            $this->engine->set($data['uuid'], $data);

            return false;
        }


        if (!$this->verify($headers['Authorization'], $data['uuid'])) {
            throw new RuntimeException("incorrect token");
        }
        $data = $this->engine->get($data['uuid']);

        if (isset($data['externalId'])) {
            unset($data['externalId']);
        }
        if (isset($data['uuid'])) {
            unset($data['uuid']);
        }
        return $data;
    }

    public function verify($token, $uuid)
    {
        try {
            $response = $this->client->post(
                $this->verifyURL,
                [
                    'headers' => ['Authorization' => $token],
                    'json' => ['uuid' => $uuid]
                ]
            );
        } catch (Throwable $e) {
            return false;
        }

        return $response->getStatusCode() === 200;
    }
}
