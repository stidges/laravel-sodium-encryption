<?php

namespace Stidges\Tests;

use RuntimeException;
use PHPUnit\Framework\TestCase;
use Stidges\LaravelSodiumEncryption\Encrypter;
use Illuminate\Contracts\Encryption\DecryptException;

class EncrypterTest extends TestCase
{
    /** @test */
    public function itEncryptsAString()
    {
        $encrypter = new Encrypter(str_repeat('a', 32));

        $encrypted = $encrypter->encryptString('foobar');

        $this->assertNotEquals('foobar', $encrypted);
        $this->assertEquals('foobar', $encrypter->decryptString($encrypted));
    }

    /** @test */
    public function itEncryptsAnArray()
    {
        $encrypter = new Encrypter(str_repeat('a', 32));

        $encrypted = $encrypter->encrypt(['foo' => 'bar']);

        $this->assertNotEquals(['foo' => 'bar'], $encrypted);
        $this->assertEquals(['foo' => 'bar'], $encrypter->decrypt($encrypted));
    }

    /** @test */
    public function itThrowsAnExceptionIfTheKeyIsTooLong()
    {
        try {
            new Encrypter(str_repeat('a', 33));
        } catch (RuntimeException $e) {
            $this->assertEquals('Incorrect key provided.', $e->getMessage());

            return;
        }

        $this->fail('Expected a RuntimeException to be thrown, but it was not.');
    }

    /** @test */
    public function itThrowsAnExceptionIfTheKeyIsTooShort()
    {
        try {
            new Encrypter(str_repeat('a', 31));
        } catch (RuntimeException $e) {
            $this->assertEquals('Incorrect key provided.', $e->getMessage());

            return;
        }

        $this->fail('Expected a RuntimeException to be thrown, but it was not.');
    }

    /** @test */
    public function itThrowsAnExceptionIfThePayloadIsInvalid()
    {
        $encrypter = new Encrypter(str_repeat('a', 32));
        $payload = str_shuffle($encrypter->encrypt('foobar'));

        try {
            $encrypter->decrypt($payload);
        } catch (DecryptException $e) {
            $this->assertEquals('The payload is invalid.', $e->getMessage());

            return;
        }

        $this->fail('Expected a DecryptException to be thrown, but it was not.');
    }

    /** @test */
    public function itThrowsAnExceptionIfTheKeyIsInvalid()
    {
        $encrypterA = new Encrypter(str_repeat('a', 32));
        $encrypterB = new Encrypter(str_repeat('b', 32));

        try {
            $encrypterB->decrypt($encrypterA->encrypt('foobar'));
        } catch (DecryptException $e) {
            $this->assertEquals('The MAC is invalid.', $e->getMessage());

            return;
        }

        $this->fail('Expected a DecryptException to be thrown, but it was not.');
    }

    /** @test */
    public function itThrowsAnExceptionIfTheEncryptedValueHasBeenTamperedWith()
    {
        $encrypter = new Encrypter(str_repeat('a', 32));
        $payload = $encrypter->encrypt('foobar');
        $payload = json_decode(base64_decode($payload), true);
        $payload['value'] = base64_decode($payload['value'], true);
        $payload['value'] .= $payload['value'][0];
        $payload['value'] = base64_encode($payload['value']);
        $payload = base64_encode(json_encode($payload));

        try {
            $encrypter->decrypt($payload);
        } catch (DecryptException $e) {
            $this->assertEquals('The MAC is invalid.', $e->getMessage());

            return;
        }

        $this->fail('Expected a DecryptException to be thrown, but it was not.');
    }

    /** @test */
    public function itCanGenerateUniqueKeys()
    {
        $keys = [];

        for ($i = 0; $i < 100; $i++) {
            $keys[] = Encrypter::generateKey();
        }

        $this->assertCount(100, array_unique($keys));
    }
}
