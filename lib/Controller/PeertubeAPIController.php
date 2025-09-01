<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Controller;

use OCA\Peertube\Service\PeertubeAPIService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\AppFramework\Http\Response;

use OCP\IRequest;
use OCP\IURLGenerator;

class PeertubeAPIController extends Controller {

	public function __construct(
		string $appName,
		IRequest $request,
		private PeertubeAPIService $peertubeAPIService,
		private IURLGenerator $urlGenerator,
		?string $userId,
	) {
		parent::__construct($appName, $request);
	}

	/**
	 * @param string $searchInstanceUrl
	 * @param string $thumbnailPath
	 * @param string $fallbackName
	 * @return Response
	 */
	#[NoAdminRequired]
	#[NoCSRFRequired]
	public function getThumbnail(string $searchInstanceUrl, string $thumbnailPath, string $fallbackName): Response {
		$result = $this->peertubeAPIService->getThumbnail($searchInstanceUrl, $thumbnailPath);
		if (isset($result['error'])) {
			$fallbackAvatarUrl = $this->urlGenerator->linkToRouteAbsolute('core.GuestAvatar.getAvatar', ['guestName' => $fallbackName, 'size' => 44]);
			return new RedirectResponse($fallbackAvatarUrl);
		} else {
			$response = new DataDownloadResponse(
				$result['body'],
				'peertube-image',
				isset($result['headers']['Content-Type'][0]) ? (string)$result['headers']['Content-Type'][0] : 'image/jpeg'
			);
			$response->cacheFor(60 * 60 * 24);
			return $response;
		}
	}
}
