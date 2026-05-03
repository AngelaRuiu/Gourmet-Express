<?php

namespace App\Services;

use App\Constants\UserRole;
use App\Core\SessionManager;
use App\Managers\UserManager;
use App\Constants\AppConstants;

/**
 * AuthService
 *
 * Owns all authentication business logic.
 * Controllers call this — they never touch UserManager or SessionManager directly.
 */
class AuthService
{
    private UserManager $users;

    public function __construct()
    {
        $this->users = new UserManager();
    }

    /**
     * Attempt login with username + password.
     * Returns the user array on success, null on failure.
     * Handles: user not found, wrong password, inactive account,
     *          and opportunistic password rehash.
     */
    public function attempt(string $username, string $password): ?array
    {
        $user = $this->users->findByUsername($username);

        // User not found — do a dummy verify to mitigate timing attacks, then return null
        if (!$user) {
            password_verify($password, '$2y$12$invalid.hash.to.prevent.timing.attacks.......');
            return null;
        }

        // Account disabled
        if (!$user['is_active']) {
            return null;
        }

        // Wrong password
        if (!$this->users->verifyPassword($password, $user['password'])) {
            return null;
        }

        // Opportunistic rehash — if BCRYPT_COST was increased, upgrade the hash silently
        if ($this->users->needsRehash($user['password'])) {
            $this->users->updateById($user['id'], [
                'password' => $this->users->hashPassword($password),
            ]);
        }

        // Establish session
        SessionManager::setUser($user);

        return $user;
    }

    public function logout(): void
    {
        SessionManager::destroy();
    }

    public function currentUser(): ?array
    {
        return SessionManager::getUser();
    }

    public function isLoggedIn(): bool
    {
        return SessionManager::isLoggedIn();
    }

    /**
     * Check if the logged-in user has a specific role.
     * Used for admin vs manager vs staff access control.
     */
    public function hasRole(UserRole $role): bool
    {
        $user = $this->currentUser();
        if (!$user) return false;
        return $user['role'] === $role->value;
    }

    public function canAccessAdmin(): bool
    {
        $user = $this->currentUser();
        if (!$user) return false;
        return UserRole::from($user['role'])->canAccessAdmin();
    }
}