<?php

namespace Stidges\LaravelSodiumEncryption;

use Throwable;
use RuntimeException;
use Illuminate\Contracts\Encryption\Encrypter as EncrypterContract;
use Illuminate\Contracts\Encryption\DecryptException;
use Illuminate\Contracts\Encryption\EncryptException;

class Encrypter implements EncrypterContract
{
    /**
     * The encryption key.
     *
     * @var string
     */
    protected $key;

    /**
     * Create a new encrypter instance.
     *
     * @param  string  $key
     * @return void
     *
     * @throws \RuntimeException
     */
    public function __construct($key)
    {
        $key = (string) $key;

        if (! static::supported($key)) {
            throw new RuntimeException('Incorrect key provided.');
        }

        $this->key = $key;
    }

    /**
     * Determine if the given key is valid.
     *
     * @param  string  $key
     * @return bool
     */
    public static function supported($key)
    {
        return mb_strlen($key, '8bit') === SODIUM_CRYPTO_SECRETBOX_KEYBYTES;
    }

    /**
     * Create a new encryption key.
     *
     * @return string
     */
    public static function generateKey()
    {
        return \sodium_crypto_secretbox_keygen();
    }

    /**
     * Encrypt the given value.
     *
     * @param  mixed  $value
     * @param  bool  $serialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encrypt($value, $serialize = true)
    {
        $nonce = random_bytes(SODIUM_CRYPTO_SECRETBOX_NONCEBYTES);

        try {
            $value = \sodium_crypto_secretbox($serialize ? serialize($value) : $value, $nonce, $this->key);
        } catch (Throwable $e) {
            throw new EncryptException($e->getMessage(), $e->getCode(), $e);
        }

        $mac = \sodium_crypto_auth($value, $this->key);

        $json = json_encode(array_map('base64_encode', compact('nonce', 'value', 'mac')));

        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new EncryptException('Could not encrypt the data.');
        }

        return base64_encode($json);
    }

    /**
     * Encrypt a string without serialization.
     *
     * @param  string  $value
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\EncryptException
     */
    public function encryptString($value)
    {
        return $this->encrypt($value, false);
    }

    /**
     * Decrypt the given value.
     *
     * @param  mixed  $payload
     * @param  bool  $unserialize
     * @return mixed
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decrypt($payload, $unserialize = true)
    {
        $payload = $this->getJsonPayload($payload);

        $decrypted = \sodium_crypto_secretbox_open($payload['value'], $payload['nonce'], $this->key);

        if ($decrypted === false) {
            throw new DecryptException('Could not decrypt the data.');
        }

        return $unserialize ? unserialize($decrypted) : $decrypted;
    }

    /**
     * Decrypt the given string without unserialization.
     *
     * @param  string  $payload
     * @return string
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    public function decryptString($payload)
    {
        return $this->decrypt($payload, false);
    }

    /**
     * Get the JSON array from the given payload.
     *
     * @param  string  $payload
     * @return array
     *
     * @throws \Illuminate\Contracts\Encryption\DecryptException
     */
    protected function getJsonPayload($payload)
    {
        $payload = json_decode(base64_decode($payload), true);

        if (! $this->validPayload($payload)) {
            throw new DecryptException('The payload is invalid.');
        }

        $payload = $this->decodePayloadValues($payload);

        if (! $this->validMac($payload)) {
            throw new DecryptException('The MAC is invalid.');
        }

        return $payload;
    }

    /**
     * Verify that the encryption payload is valid.
     *
     * @param  mixed  $payload
     * @return bool
     */
    protected function validPayload($payload)
    {
        return is_array($payload) && isset($payload['nonce'], $payload['value'], $payload['mac']);
    }

    /**
     * Decode the base64 encoded values of the payload.
     *
     * @param  array  $payload
     * @return array
     */
    protected function decodePayloadValues(array $payload)
    {
        return array_map(function ($value) {
            return base64_decode($value, true);
        }, $payload);
    }

    /**
     * Determine if the MAC for the given payload is valid.
     *
     * @param  array  $payload
     * @return bool
     */
    protected function validMac(array $payload)
    {
        return \sodium_crypto_auth_verify($payload['mac'], $payload['value'], $this->key);
    }

    /**
     * Get the encryption key.
     *
     * @return string
     */
    public function getKey()
    {
        return $this->key;
    }
}
