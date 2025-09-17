<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Settings;

use OCA\Peertube\AppInfo\Application;
use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IAppConfig;
use OCP\AppFramework\Services\IInitialState;

use OCP\Settings\ISettings;

class Admin implements ISettings {

	public function __construct(
		private IAppConfig $appConfig,
		private IInitialState $initialStateService,
		?string $userId,
	) {
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$instances = $this->appConfig->getAppValueString('instances', '', lazy: true);

		$state = [
			'instances' => $instances,
		];
		$this->initialStateService->provideInitialState('admin-config', $state);
		return new TemplateResponse(Application::APP_ID, 'adminSettings');
	}

	public function getSection(): string {
		return 'connected-accounts';
	}

	public function getPriority(): int {
		return 10;
	}
}
