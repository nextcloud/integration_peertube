<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Peertube\Tests\Reference;

use OCA\Peertube\Reference\PeertubeReferenceProvider;
use OCA\Peertube\Service\PeertubeAPIService;
use OCP\AppFramework\Services\IAppConfig;
use OCP\Collaboration\Reference\IReferenceManager;
use OCP\Collaboration\Reference\LinkReferenceProvider;
use OCP\IL10N;
use OCP\IURLGenerator;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;
use Psr\Log\LoggerInterface;

class PeertubeReferenceProviderTest extends TestCase {

	/** @var IAppConfig&MockObject */
	private $appConfig;
	private PeertubeReferenceProvider $provider;

	protected function setUp(): void {
		parent::setUp();
		$this->appConfig = $this->createMock(IAppConfig::class);
		// the untyped read is not lazy, so it would miss a lazy row
		$this->appConfig->expects($this->never())->method('getAppValue');
		// a stored user toggle is not what these tests are about
		$this->appConfig->method('getUserValue')
			->willReturnCallback(static fn (string $userId, string $key, string $default = ''): string => $default);

		$apiService = $this->createMock(PeertubeAPIService::class);
		$apiService->method('getPeertubeInstances')->willReturn(['https://peertube.example']);

		$this->provider = new PeertubeReferenceProvider(
			$apiService,
			$this->appConfig,
			$this->createMock(IL10N::class),
			$this->createMock(IURLGenerator::class),
			$this->createMock(IReferenceManager::class),
			$this->createMock(LinkReferenceProvider::class),
			$this->createMock(LoggerInterface::class),
			'alice',
		);
	}

	public function testMatchReferenceReadsTheAdminLinkPreviewToggleLazily(): void {
		$this->appConfig->expects($this->once())
			->method('getAppValueString')
			->with('link_preview_enabled', '1', true)
			->willReturn('0');

		$this->assertFalse($this->provider->matchReference('https://peertube.example/w/abc'));
	}

	public function testLinkPreviewsAreOnWhenTheAdminToggleIsNotStored(): void {
		// the toggle has no admin form, so on nearly every install the default decides
		$this->appConfig->expects($this->once())
			->method('getAppValueString')
			->with('link_preview_enabled', $this->anything(), true)
			->willReturnCallback(static fn (string $key, string $default = '', bool $lazy = false): string => $default);

		$this->assertTrue($this->provider->matchReference('https://peertube.example/w/abc'));
	}
}
