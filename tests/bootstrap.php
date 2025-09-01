<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

require_once __DIR__ . '/../../../tests/bootstrap.php';

\OC_App::loadApp(OCA\Peertube\AppInfo\Application::APP_ID);
OC_Hook::clear();
