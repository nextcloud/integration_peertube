<?php
/**
 * Nextcloud - Peertube
 *
 * This file is licensed under the Affero General Public License version 3 or
 * later. See the COPYING file.
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 * @copyright Julien Veyssier 2023
 */

namespace OCA\Peertube\Controller;

use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\DataDownloadResponse;
use OCP\AppFramework\Http\RedirectResponse;
use OCP\IRequest;

use OCA\Peertube\Service\PeertubeAPIService;
use OCP\IURLGenerator;

class PeertubeAPIController extends Controller {

	public function __construct(string $appName,
								IRequest $request,
								private PeertubeAPIService $peertubeAPIService,
								private IURLGenerator $urlGenerator,
								?string $userId) {
		parent::__construct($appName, $request);
	}

	/**
	 * @NoAdminRequired
	 * @NoCSRFRequired
	 *
	 * @param string $searchInstanceUrl
	 * @param string $thumbnailPath
	 * @param string $fallbackName
	 * @return DataDownloadResponse|RedirectResponse
	 */
	public function getThumbnail(string $searchInstanceUrl, string $thumbnailPath, string $fallbackName) {
		$result = $this->peertubeAPIService->getThumbnail($searchInstanceUrl, $thumbnailPath);
		if (isset($result['error'])) {
			$fallbackAvatarUrl = $this->urlGenerator->linkToRouteAbsolute('core.GuestAvatar.getAvatar', ['guestName' => $fallbackName, 'size' => 44]);
			return new RedirectResponse($fallbackAvatarUrl);
		} else {
			$response = new DataDownloadResponse(
				$result['body'],
				'peertube-image',
				$result['headers']['Content-Type'][0] ?? 'image/jpeg'
			);
			$response->cacheFor(60 * 60 * 24);
			return $response;
		}
	}
}
