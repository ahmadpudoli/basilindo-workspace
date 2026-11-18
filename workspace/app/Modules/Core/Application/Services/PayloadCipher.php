<?php

namespace App\Modules\Core\Application\Services;

use RuntimeException;

/** Shared authenticated encryption for sensitive application payloads. */
class PayloadCipher
{
    public function encrypt(string $plaintext): string
    {
        $cipher = (string) config('encryption.payload.cipher', 'aes-256-gcm');
        $iv = random_bytes((int) config('encryption.payload.iv_bytes', 12));
        $tag = '';
        $encrypted = openssl_encrypt($plaintext, $cipher, $this->currentKey(), OPENSSL_RAW_DATA, $iv, $tag);
        if ($encrypted === false) throw new RuntimeException('Payload encryption failed.');

        return base64_encode($iv.$tag.$encrypted);
    }

    public function decrypt(string $payload): string
    {
        $cipher = (string) config('encryption.payload.cipher', 'aes-256-gcm');
        $decoded = base64_decode($payload, true);
        $ivBytes = (int) config('encryption.payload.iv_bytes', 12);
        $tagBytes = (int) config('encryption.payload.tag_bytes', 16);
        if ($decoded === false || strlen($decoded) <= $ivBytes + $tagBytes) throw new RuntimeException('Encrypted payload is invalid.');

        foreach ($this->keys() as $key) {
            $plaintext = openssl_decrypt(
                substr($decoded, $ivBytes + $tagBytes),
                $cipher,
                $key,
                OPENSSL_RAW_DATA,
                substr($decoded, 0, $ivBytes),
                substr($decoded, $ivBytes, $tagBytes),
            );
            if ($plaintext !== false) return $plaintext;
        }

        throw new RuntimeException('Payload decryption failed.');
    }

    /** @return list<string> */
    private function keys(): array
    {
        $secrets = array_filter([
            config('encryption.payload.key'),
            ...config('encryption.payload.previous_keys', []),
            app()->environment(['local', 'testing']) ? config('app.key') : null,
        ]);
        if ($secrets === []) throw new RuntimeException('Application payload encryption key is not configured.');

        return array_values(array_unique(array_map(fn (string $secret): string => $this->normalizeKey($secret), $secrets)));
    }

    private function currentKey(): string
    {
        $secret = (string) config('encryption.payload.key');
        if ($secret === '' && app()->environment(['local', 'testing'])) $secret = (string) config('app.key');
        if ($secret === '') throw new RuntimeException('Application payload encryption key is not configured.');

        return $this->normalizeKey($secret);
    }

    private function normalizeKey(string $secret): string
    {
        $secret = trim($secret);
        if (str_starts_with($secret, 'base64:')) $secret = base64_decode(substr($secret, 7), true) ?: '';
        if (strlen($secret) < 16) throw new RuntimeException('Application payload encryption key must contain at least 16 bytes.');

        return hash('sha256', $secret, true);
    }
}
