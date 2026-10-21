<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Peertube\Tests\Migration;

use OCA\Peertube\AppInfo\Application;
use OCA\Peertube\Migration\Version2103Date202610211045;
use OCP\IAppConfig;
use OCP\Migration\IOutput;
use PHPUnit\Framework\TestCase;

class Version2103Date202610211045Test extends TestCase {

	public function testPostSchemaChangeSetsTheLazyFlagOfEachAppValue(): void {
		$calls = [];
		$appConfig = $this->createMock(IAppConfig::class);
		$appConfig->method('updateLazy')
			->willReturnCallback(function (string $app, string $key, bool $lazy) use (&$calls): bool {
				$calls[] = [$app, $key, $lazy];
				return true;
			});

		$migration = new Version2103Date202610211045($appConfig);
		$migration->postSchemaChange($this->createMock(IOutput::class), static fn () => null, []);

		$this->assertSame([
			[Application::APP_ID, 'link_preview_enabled', true],
			[Application::APP_ID, 'instances', false],
		], $calls);
	}
}
