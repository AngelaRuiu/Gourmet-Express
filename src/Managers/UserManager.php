<?php

namespace App\Managers;

use App\Core\BaseManager;
use App\Core\Database;

class UserManager extends BaseManager
{
    protected function getTable(): string
    {
        return Database::TABLE_USERS;
    }

    public function findByEmail(string $email): ?array
    {
        return $this->findOneWhere(
            ['t.email = :email'],
            [':email' => $email]
        );
    }

    public function findByUsername(string $username): ?array
    {
        return $this->findOneWhere(
            ['t.username = :username'],
            [':username' => $username]
        );
    }

    public function findActiveById(int $id): ?array
    {
        return $this->findOneWhere(
            ['t.id = :id', 't.is_active = 1'],
            [':id' => $id]
        );
    }

    /**
     * Verify a plain-text password against the stored hash.
     * Never compare passwords with === directly anywhere in the app.
     */
    public function verifyPassword(string $plainText, string $hash): bool
    {
        return password_verify($plainText, $hash);
    }

    /**
     * Hash a plain-text password ready for storage.
     * Used during user creation and password reset.
     */
    public function hashPassword(string $plainText): string
    {
        return password_hash(
            $plainText,
            PASSWORD_BCRYPT,
            ['cost' => \App\Constants\AppConstants::BCRYPT_COST]
        );
    }

    /**
     * Check if the password hash needs rehashing (e.g. if BCRYPT_COST was increased).
     * If true, the password should be rehashed on the next successful login.
     */
    public function needsRehash(string $hash): bool
    {
        return password_needs_rehash(
            $hash,
            PASSWORD_BCRYPT,
            ['cost' => \App\Constants\AppConstants::BCRYPT_COST]
        );
    }
}