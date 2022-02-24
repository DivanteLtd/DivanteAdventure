<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Mercure;

class MercureDecorator
{
    private JwtProvider $jwtProvider;
    private MercureConfig $config;

    public function __construct(JwtProvider $jwtProvider, MercureConfig $config)
    {
        $this->config = $config;
        $this->jwtProvider = $jwtProvider;
    }

    /**
     * @param array<string,mixed> $result
     * @param string $id
     * @return array<string,mixed>
     */
    public function decorate(array $result, string $id): array
    {
        return [
            '@id' => $id,
            '@mercure' => $this->config->getMercureUrl(),
            '@token' => $this->jwtProvider->getToken(JwtProvider::MODE_SUBSCRIBE, $id),
            '@result' => $result,
        ];
    }
}
