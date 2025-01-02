<?php

namespace App\Service;



class JWTService 
{
    /**
     * create token
     *
     * @param array $header
     * @param array $payload
     * @param string $secret
     * @param integer $validity
     * @return string
     */
    public function generate(array $header, array $payload, string $secret, int $validity = 3600): string
    {
        //calcul ecart date debut et date fin
        if($validity > 0){
            $maintenant = new \DateTimeImmutable();
            $expire = $maintenant->getTimestamp() + $validity;

            $payload['iat'] = $maintenant->getTimestamp();
            $payload['exp'] = $expire;

        }
        //encode base 64
        $base64Header = base64_encode(json_encode($header));
        $base64Payload = base64_encode(json_encode($payload));
        //nettoyage
        $base64Header = str_replace(['+', '/', '='], ['-', '_', ''], $base64Header );
        $base64Payload = str_replace(['+', '/', '='], ['-', '_', ''], $base64Payload );
        // generation signature
        $secret = base64_encode($secret);
        $signature = hash_hmac('sha256',$base64Header . '.' . $base64Payload,$secret,true);
        $base64Signature = base64_encode($signature);
        $base64Signature = str_replace(['+', '/', '='], ['-', '_', ''], $base64Signature);
        // creation jeton
        $jwt = $base64Header . '.' . $base64Payload . '.' . $base64Signature;
        return $jwt;
    }

    /**
     * integrity token function
     *
     * @param string $token
     * @return boolean
     */
    public function isValid(string $token): bool
    {
        return preg_match('/^[a-zA-Z0-9\-\_\ ]+\.[a-zA-Z0-9\-\_\ ]+\.[a-zA-Z0-9\-\_\ ]+$/', $token)===1;
    }

    /**
     * extract header function
     *
     * @param string $token
     * @return array
     */
    public function getHeader(string $token): array
    {
        // chucky on progress...
        $tableau = explode('.', $token);
        // decode header
        $header = json_decode(base64_decode($tableau[0],true));
        return $header;
    }
    /**
     * extract payload function
     *
     * @param string $token
     * @return array
     */
    public function getPayload(string $token): array
    {
        // chucky on progress again.... :)
        $tableau = explode('.',$token);
        $payload = json_decode(base64_decode($tableau[1],true));
        return $payload;
    }
    /**
     * check interval time function
     *
     * @param string $token
     * @return boolean
     */
    public function isExpired(string $token): bool
    {
        $payload = $this->getPayload($token);
        $maintenant = new \DateTimeImmutable();
        return $payload['exp'] < $maintenant->getTimestamp();
    }

    /**
     * control token function
     *
     * @param string $token
     * @param string $secret
     * @return boolean
     */
    public function check(string $token,string $secret): bool
    {
        $header = $this->getHeader($token);
        $payload = $this->getPayload($token);
        $checkToken = $this->generate($header,$payload,$secret,0);
        return $token === $checkToken;
    }
}