<?php

namespace Divante\Bundle\AdventureBundle\Services\Hardware;

class Cipher
{
    private const METHOD = "aes-256-ctr";

    public function encrypt(string $data, string $password, int $id) : string
    {
        $key = $this->passwordToKey($password);
        $iv = $this->idToIv($id);
        return openssl_encrypt($data, self::METHOD, $key, 0, $iv);
    }

    public function decrypt(?string $encrypted, string $password, int $id) : ?string
    {
        if (is_null($encrypted)) {
            return null;
        }
        $key = $this->passwordToKey($password);
        $iv = $this->idToIv($id);
        return openssl_decrypt($encrypted, self::METHOD, $key, 0, $iv);
    }

    private function idToIv(int $id) : string
    {
        return substr(md5((string)$id), 16);
    }

    private function passwordToKey(string $password) : string
    {
        return hash("sha256", $password);
    }
}
