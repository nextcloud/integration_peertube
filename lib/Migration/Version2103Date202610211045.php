<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Peertube\Migration;

use Closure;
use OCA\Peertube\AppInfo\Application;
use OCP\IAppConfig;
use OCP\Migration\IOutput;
use OCP\Migration\SimpleMigrationStep;

class Version2103Date202610211045 extends SimpleMigrationStep {

	public function __construct(
		private IAppConfig $appConfig,
	) {
	}

	/**
	 * @param IOutput $output
	 * @param Closure $schemaClosure The `\Closure` returns a `ISchemaWrapper`
	 * @param array $options
	 */
	public function postSchemaChange(IOutput $output, Closure $schemaClosure, array $options) {
		// only read when a link is resolved
		$this->appConfig->updateLazy(Application::APP_ID, 'link_preview_enabled', true);
		// read on every page, but stored as lazy by every admin save since 2.1.0
		$this->appConfig->updateLazy(Application::APP_ID, 'instances', false);
	}
}
