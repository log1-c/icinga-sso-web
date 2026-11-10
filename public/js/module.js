// SPDX-FileCopyrightText: 2026 Icinga GmbH <https://icinga.com>
// SPDX-License-Identifier: GPL-3.0-or-later

;(function (Icinga) {
    'use strict';

    class ResolveAbsoluteUrl extends Icinga.EventListener {
        constructor(icinga) {
            super(icinga);

            this.on('rendered', '.icinga-module.module-sso', event => this.resolveUrls(event), this);
            this.on('rendered', '.login-form', event => this.setupLocalLoginToggle(event.target), this);

            // The login page itself is not necessarily rendered through Icinga's AJAX loader.
            this.setupLocalLoginToggle(document);
        }

        resolveUrls(event) {
            for (const input of event.target.getElementsByClassName('resolve-absolute-url')) {
                if (/^\//.exec(input.value)) {
                    // Url::fromPath('module/controller/action')->getAbsoluteUrl() returns a URI relative
                    // to the current host, e.g. /icingaweb2/module/controller/action.
                    // Completing it to an absolute URL on the server side (using $_SERVER) is theoretically possible,
                    // but the quality of the result would depend on all reverse proxies' (if any) configuration.

                    const a = document.createElement('a');
                    a.href = input.value; // Set relative URL
                    input.value = a.href; // Get absolute URL
                }
            }
        }

        setupLocalLoginToggle(container) {
            const forms = [];
            if (container.matches && container.matches('#login-form')) {
                forms.push(container);
            }
            forms.push(...container.querySelectorAll('#login-form'));

            for (const form of forms) {
                if (form.querySelector('.sso-local-login-toggle')) {
                    continue;
                }

                const controls = ['username', 'password', 'rememberme', 'submit_login']
                    .map(name => form.querySelector(`[name="${name}"]`))
                    .filter(element => element !== null)
                    .map(element => element.closest('.control-group') || element);

                if (controls.length === 0) {
                    continue;
                }

                const toggle = document.createElement('label');
                toggle.className = 'sso-local-login-toggle';

                const checkbox = document.createElement('input');
                checkbox.type = 'checkbox';
                checkbox.className = 'sso-local-login-toggle-control';
                checkbox.checked = true;
                checkbox.setAttribute('role', 'switch');
                checkbox.setAttribute('aria-label', 'Show local Icinga Web login fields');

                const text = document.createElement('span');
                text.textContent = 'Local Icinga Web login';

                toggle.append(checkbox, text);

                const firstControl = form.querySelector('.control-group');
                form.insertBefore(toggle, firstControl || form.firstChild);

                checkbox.addEventListener('change', () => {
                    checkbox.setAttribute('aria-checked', String(checkbox.checked));
                    for (const control of controls) {
                        control.hidden = ! checkbox.checked;
                    }
                });
                checkbox.setAttribute('aria-checked', 'true');
            }
        }
    }

    Icinga.Behaviors = Icinga.Behaviors || {};
    Icinga.Behaviors.ResolveAbsoluteUrl = ResolveAbsoluteUrl;
})(Icinga);
