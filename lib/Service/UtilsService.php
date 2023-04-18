<?php
/**
 * Nextcloud - Peertube
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier
 * @copyright Julien Veyssier 2023
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
