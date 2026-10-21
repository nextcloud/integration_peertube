<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Peertube\Tests\Settings;

use OCA\Peertube\Settings\Admin;
use OCP\AppFramework\Services\IAppConfig;
use OCP\AppFramework\Services\IInitialState;
use PHPUnit\Framework\TestCase;

class AdminTest extends TestCase {

	public function testGetFormReadsTheInstanceListNonLazily(): void {
		$appConfig = $this->createMock(IAppConfig::class);
		$appConfig->expects($this->never())->method('getAppValue');
		$appConfig->expects($this->once())
			->method('getAppValueString')
			->with('instances', '', false)
			->willReturn('https://peertube.example');
		$initialState = $this->createMock(IInitialState::class);
		$initialState->expects($this->once())
			->method('provideInitialState')
			->with('admin-config', ['instances' => 'https://peertube.example']);

		$response = (new Admin($appConfig, $initialState, 'admin'))->getForm();

		$this->assertSame('adminSettings', $response->getTemplateName());
	}
}
