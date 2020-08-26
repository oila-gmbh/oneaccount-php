<?php


use GuzzleHttp\Client;

final class Oneaccount
{
    private Client $client;
    /**
     * @var EngineInterface
     */
    private EngineInterface $engine;
    /**
     * @var array
     */
    private array $options;

    private string $verifyURL = "https://api.oneaccount.app/widget/verify";

    public function __construct(EngineInterface $engine)
    {
        $this->client = new Client();
        $this->engine = $engine;
    }


    public function auth(?string $token, array $body)
    {
        if (!$body['uuid']) {
            throw new InvalidArgumentException("the uuid field is required");
        }
        if (null === $token) {
            $this->engine->set($body['uuid'], $body);

            return false;
        } else {
            if (!$this->verify($token, $body['uuid'])) {
                throw new RuntimeException("incorrect token");
            }
            return $this->engine->get($body['uuid']);
        }
    }

    public function verify(string $token, string $uuid)
    {
        try {
            $response = $this->client->post(
                $this->verifyURL,
                [
                    'auth' => $token,
                    'json' => ['uuid' => $uuid]
                ]
            );
        } catch (Throwable $e) {
            return false;
        }

        return 200 === $response->getStatusCode();
    }
}