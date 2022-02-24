<?php

namespace Divante\Bundle\AdventureBundle\Infrastructure\Mercure;

use Lcobucci\JWT\Builder;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key;
use Symfony\Component\Mercure\Update;

class JwtProvider
{
    private string $jwtKey;

    public const MODE_SUBSCRIBE = 1;
    public const MODE_PUBLISH = 2;

    public function __construct(MercureConfig $config)
    {
        $this->jwtKey = $config->getJwtKey();
    }

    public function getToken(int $mode, string $url = '*'): string
    {
        return (new Builder())
            ->withClaim('mercure', [ $this->getClaimKey($mode) => [ $url ]])
            ->getToken(new Sha256(), new Key($this->jwtKey));
    }

    public function __invoke(Update $update): string
    {
        return $this->getToken(self::MODE_PUBLISH);
    }

    private function getClaimKey(int $mode): string
    {
        switch ($mode) {
            case self::MODE_PUBLISH:
                return 'publish';
            case self::MODE_SUBSCRIBE:
                return 'subscribe';
            default:
                return 'unknown';
        }
    }
}
