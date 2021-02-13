<?php

declare(strict_types=1);

/*
 * This file is part of the Structurizr for PHP.
 *
 * (c) Norbert Orzechowicz <norbert@orzechowicz.pl>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace StructurizrPHP\Core\Configuration;

final class User
{
    /**
     * @var string
     */
    private $username;

    /**
     * @var Role
     */
    private $role;

    /**
     * @param string $username
     * @param Role $role
     */
    public function __construct(string $username, Role $role)
    {
        $this->username = $username;
        $this->role = $role;
    }

    /**
     * @return string
     */
    public function username() : string
    {
        return $this->username;
    }

    /**
     * @return Role
     */
    public function role() : Role
    {
        return $this->role;
    }

    /**
     * @param User $user
     *
     * @return bool
     */
    public function equals(self $user) : bool
    {
        return $this->username === $user->username() &&
            $this->role()->toString() === $user->role()->toString();
    }

    /**
     * @return string
     */
    public function toString() : string
    {
        return 'User {' .
            'username=\'' . $this->username . '\'' .
            ', role=' . $this->role->toString() .
            '}';
    }
}
