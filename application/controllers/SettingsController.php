<?php

// SPDX-FileCopyrightText: 2026 Icinga GmbH <https://icinga.com>
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Icinga\Module\Sso\Controllers;

use GuzzleHttp\Psr7\ServerRequest;
use Icinga\Module\Sso\Form\SettingsForm;
use ipl\Html\Form;
use ipl\Web\Compat\CompatController;
use ipl\Web\Url;

class SettingsController extends CompatController
{
    public function init(): void
    {
        $this->assertPermission('config/modules');
        parent::init();
    }

    public function indexAction(): void
    {
        $config = $this->Config();
        $form = (new SettingsForm())
            ->populate($config->getSection('settings')->toArray())
            ->on(Form::ON_SUCCESS, function (SettingsForm $form) use ($config): void {
                $config->setSection('settings', [
                    'hide_local_login' => $form->getValue('hide_local_login') === 'y' ? 'y' : 'n'
                ]);
                $config->saveIni();
            })
            ->on(Form::ON_SUCCESS, function (): void {
                $this->redirectNow('__REFRESH__');
            });

        $form->setAction(Url::fromRequest()->getAbsoluteUrl());
        $form->handleRequest(ServerRequest::fromGlobals());
        $this->addContent($form);

        foreach ($this->Module()->getConfigTabs()->activate('settings')->getTabs() as $tab) {
            $this->tabs->add($tab->getName(), $tab);
        }
        $this->setTitle($this->translate('Settings'));
    }
}
