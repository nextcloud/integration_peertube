<?php
/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Tests;

use OCA\Peertube\AppInfo\Application;
use OCA\Peertube\Service\PeertubeAPIService;

class PeertubeAPIServiceTest extends \PHPUnit\Framework\TestCase {

	public function testDummy() {
		$app = new Application();
		$this->assertEquals('integration_peertube', $app::APP_ID);
	}
}
