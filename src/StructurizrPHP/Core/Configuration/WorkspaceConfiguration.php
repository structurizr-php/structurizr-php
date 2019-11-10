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

namespace StructurizrPHP\StructurizrPHP\Core\Configuration;

use Assert\AssertionFailedException;
use StructurizrPHP\StructurizrPHP\Assertion;

final class WorkspaceConfiguration
{
    /**
     * @var User[]
     */
    private $users;

    public function __construct()
    {
        $this->users = [];
    }

    /**
     * @return User[]
     */
    public function users() : array
    {
        return $this->users;
    }

    /**
     * @param string $username
     * @param Role $role
     *
     * @throws AssertionFailedException
     */
    public function addUser(string $username, Role $role) : void
    {
        Assertion::notEmpty($username, 'A username must be specified.');
        $this->users[] = new User($username, $role);
    }
}
