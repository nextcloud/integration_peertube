<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Tests;

use OCA\Peertube\AppInfo\Application;
use OCA\Peertube\Service\PeertubeAPIService;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Http\Client\IClientService;
use OCP\IL10N;
use OCP\L10N\IFactory;
use Psr\Log\LoggerInterface;

class PeertubeAPIServiceTest extends \PHPUnit\Framework\TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('integration_peertube', $app::APP_ID);
	}

	public function testGetPeertubeInstancesReadsTheInstanceListNonLazily(): void {
		$appConfig = $this->createMock(IAppConfig::class);
		$appConfig->expects($this->never())->method('getAppValue');
		$appConfig->expects($this->once())
			->method('getAppValueString')
			->with('instances', '', false)
			->willReturn("https://peertube.example/\nhttps://video.example");

		$service = new PeertubeAPIService(
			Application::APP_ID,
			$this->createMock(LoggerInterface::class),
			$this->createMock(IL10N::class),
			$appConfig,
			$this->createMock(IFactory::class),
			$this->createMock(IClientService::class),
		);

		$this->assertSame(['https://peertube.example', 'https://video.example'], $service->getPeertubeInstances());
	}
}
