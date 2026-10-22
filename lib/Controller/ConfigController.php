<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http;
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
		if (!$this->isAllowed($values, ['search_enabled', 'link_preview_enabled'])) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
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
		if (!$this->isAllowed($values, ['instances'])) {
			return new DataResponse([], Http::STATUS_BAD_REQUEST);
		}
		try {
			foreach ($values as $key => $value) {
				// not lazy: the instance list is read on every page, see PeertubeAPIService::getPeertubeInstances()
				$this->appConfig->setAppValueString($key, $value, lazy: false);
			}
		} catch (\Exception $e) {
			return new DataResponse(['error' => $e->getMessage()], 500);
		}

		return new DataResponse('');
	}

	/**
	 * Whether every key is one that the endpoint's form sends and every value
	 * is a string. Any other key could overwrite one of the server's own rows
	 * of the app, such as "enabled".
	 *
	 * @param array $values
	 * @param list<string> $keys
	 */
	private function isAllowed(array $values, array $keys): bool {
		foreach ($values as $key => $value) {
			if (!in_array($key, $keys, true) || !is_string($value)) {
				return false;
			}
		}
		return true;
	}
}
