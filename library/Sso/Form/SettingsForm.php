<?php

// SPDX-FileCopyrightText: 2026 Icinga GmbH <https://icinga.com>
// SPDX-License-Identifier: GPL-3.0-or-later

namespace Icinga\Module\Sso\Form;

use Icinga\Web\Session;
use ipl\I18n\Translation;
use ipl\Web\Common\CsrfCounterMeasure;
use ipl\Web\Compat\CompatForm;

class SettingsForm extends CompatForm
{
    use CsrfCounterMeasure;
    use Translation;

    protected function assemble(): void
    {
        $this->addElement('checkbox', 'hide_local_login', [
            'label'       => $this->translate('Hide local login fields'),
            'description' => $this->translate(
                'Hide the built-in Icinga Web username and password login form on the login page'
            ),
            'value'       => 'y'
        ]);

        $this->addElement('submit', 'btn_submit', [
            'label' => $this->translate('Save')
        ]);

        $this->addElement($this->createCsrfCounterMeasure(Session::getSession()->getId()));
    }
}
