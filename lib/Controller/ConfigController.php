<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataResponse;
use OCP\AppFramework\Services\IAppConfig;

use OCP\IRequest;
use OCP\PreConditionNotMetException;

class ConfigController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private IAppConfig $appConfig,
		private ?string $userId,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 *
	 * @param array $values
	 * @return DataResponse
	 * @throws PreConditionNotMetException
	 */
	public function setConfig(array $values): DataResponse {
		try {
			foreach ($values as $key => $value) {
				$this->appConfig->setUserValue($this->userId, $key, $value);
			}
		} catch (\Exception $e) {
			return new DataResponse(['error' => 'Failed to set config values'], 500);
		}
		return new DataResponse('');
	}

	/**
	 * @param array $values
	 * @return DataResponse
	 */
	public function setAdminConfig(array $values): DataResponse {
		try {
			foreach ($values as $key => $value) {
				$this->appConfig->setAppValueString($key, $value, lazy:true);
			}
		} catch (\Exception $e) {
			return new DataResponse(['error' => $e->getMessage()], 500);
		}

		return new DataResponse('');
	}
}
