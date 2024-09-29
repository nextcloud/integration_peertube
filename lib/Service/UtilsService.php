<?php
/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Service;

use DateTime;
use OCP\IDateTimeFormatter;

class UtilsService {

	public function __construct (string             $appName,
								 private IDateTimeFormatter $dateTimeFormatter) {
	}

	public function formatDate(string $date, string $format = 'long'): string {
		return $this->dateTimeFormatter->formatDate(new DateTime($date), $format);
	}
}
