<?php

// SPDX-FileCopyrightText: 2026 Icinga GmbH <https://icinga.com>
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Icinga\Module\Sso\Controllers;

use Icinga\Application\Config;
use ipl\Web\Compat\CompatController;

/**
 * Public, read-only endpoint for the login page's local-login visibility setting.
 */
class ConfigController extends CompatController
{
    protected $requiresAuthentication = false;

    public function init(): void
    {
        parent::init();
        $this->getHelper('viewRenderer')->setNoRender(true);
        $this->getHelper('layout')->disableLayout();
    }

    public function loginfieldsAction(): void
    {
        $hideLocalLogin = Config::module('sso')->get('settings', 'hide_local_login', 'n') === 'y';
        $this->getResponse()
            ->setHeader('Content-Type', 'application/json; charset=UTF-8')
            ->setBody(json_encode(['hideLocalLogin' => $hideLocalLogin], JSON_THROW_ON_ERROR));
    }
}
