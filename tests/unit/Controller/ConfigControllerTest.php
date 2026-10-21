<?php

/**
 * SPDX-FileCopyrightText: 2026 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

declare(strict_types=1);

namespace OCA\Peertube\Tests\Controller;

use OCA\Peertube\AppInfo\Application;
use OCA\Peertube\Controller\ConfigController;
use OCP\AppFramework\Http;
use OCP\AppFramework\Services\IAppConfig;
use OCP\IRequest;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class ConfigControllerTest extends TestCase {

	/** @var IAppConfig&MockObject */
	private $appConfig;
	private ConfigController $controller;

	protected function setUp(): void {
		parent::setUp();
		$this->appConfig = $this->createMock(IAppConfig::class);
		// the untyped methods read and write with lazy: false whatever the stored flag
		$this->appConfig->expects($this->never())->method('getAppValue');
		$this->appConfig->expects($this->never())->method('setAppValue');
		$this->controller = new ConfigController(Application::APP_ID, $this->createMock(IRequest::class), $this->appConfig, 'alice');
	}

	public function testSetAdminConfigSavesTheAdminFormPayloadNonLazily(): void {
		// what AdminSettings.vue sends
		$instances = "https://peertube.example\nhttps://video.example/";
		$this->appConfig->expects($this->once())
			->method('setAppValueString')
			->with('instances', $instances, false)
			->willReturn(true);

		$response = $this->controller->setAdminConfig(['instances' => $instances]);

		$this->assertSame(Http::STATUS_OK, $response->getStatus());
	}

	public static function rejectedAdminValues(): array {
		return [
			'a server row of the app' => [['enabled' => 'yes']],
			'a server row after a valid key' => [['instances' => 'https://peertube.example', 'enabled' => 'yes']],
			'a key without an admin form field' => [['link_preview_enabled' => '0']],
			'a list key' => [['https://peertube.example']],
			'a non-string value' => [['instances' => ['https://peertube.example']]],
		];
	}

	/**
	 * @dataProvider rejectedAdminValues
	 */
	public function testSetAdminConfigWritesNothingForAnythingElse(array $values): void {
		$this->appConfig->expects($this->never())->method('setAppValueString');

		$response = $this->controller->setAdminConfig($values);

		$this->assertSame(Http::STATUS_BAD_REQUEST, $response->getStatus());
	}

	public static function personalFormValues(): array {
		// what PersonalSettings.vue sends, one checkbox at a time
		return [
			'search' => ['search_enabled', '1'],
			'link previews' => ['link_preview_enabled', '0'],
		];
	}

	/**
	 * @dataProvider personalFormValues
	 */
	public function testSetConfigSavesThePersonalFormPayload(string $key, string $value): void {
		$this->appConfig->expects($this->once())
			->method('setUserValue')
			->with('alice', $key, $value);

		$response = $this->controller->setConfig([$key => $value]);

		$this->assertSame(Http::STATUS_OK, $response->getStatus());
	}

	public static function rejectedPersonalValues(): array {
		return [
			'an unknown key' => [['enabled' => 'yes']],
			'an unknown key after a valid one' => [['search_enabled' => '1', 'enabled' => 'yes']],
			'a non-string value' => [['search_enabled' => true]],
		];
	}

	/**
	 * @dataProvider rejectedPersonalValues
	 */
	public function testSetConfigWritesNothingForAnythingElse(array $values): void {
		$this->appConfig->expects($this->never())->method('setUserValue');

		$response = $this->controller->setConfig($values);

		$this->assertSame(Http::STATUS_BAD_REQUEST, $response->getStatus());
	}
}
