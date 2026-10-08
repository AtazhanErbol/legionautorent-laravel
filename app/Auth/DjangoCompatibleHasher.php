<?php

namespace App\Auth;

use Illuminate\Hashing\BcryptHasher;

class DjangoCompatibleHasher extends BcryptHasher
{
    public function check(mixed $value, mixed $hashedValue, array $options = []): bool
    {
        if (str_starts_with($hashedValue, 'pbkdf2_sha256$') || str_starts_with($hashedValue, 'pbkdf2_sha1$')) {
            [$algorithm,$rounds,$salt,$hash] = explode('$', $hashedValue, 4);
            if (! ctype_digit($rounds) || (int) $rounds > 5000000) {
                return false;
            }$raw = hash_pbkdf2($algorithm === 'pbkdf2_sha256' ? 'sha256' : 'sha1', $value, $salt, (int) $rounds, 0, true);

            return hash_equals($hash, base64_encode($raw));
        }
        if (str_starts_with($hashedValue, 'argon2$')) {
            return password_verify($value, substr($hashedValue, 6));
        }
        if (str_starts_with($hashedValue, 'bcrypt_sha256$')) {
            return password_verify(hash('sha256', $value), substr($hashedValue, 14));
        }

        return parent::check($value, $hashedValue, $options);
    }

    public function needsRehash(mixed $hashedValue, array $options = []): bool
    {
        return ! str_starts_with($hashedValue, '$2y$') || parent::needsRehash($hashedValue, $options);
    }
}
