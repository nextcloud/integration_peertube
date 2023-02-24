<?php
/**
 * @copyright Copyright (c) 2023 Julien Veyssier <eneiluj@posteo.net>
 *
 * @author Julien Veyssier <eneiluj@posteo.net>
 *
 * @license GNU AGPL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program. If not, see <http://www.gnu.org/licenses/>.
 */

namespace OCA\Peertube\Reference;

use Exception;
use OC\Collaboration\Reference\LinkReferenceProvider;
use OCA\Peertube\Service\UtilsService;
use OCP\Collaboration\Reference\ADiscoverableReferenceProvider;
use OCP\Collaboration\Reference\ISearchableReferenceProvider;
use OCP\Collaboration\Reference\Reference;
use OC\Collaboration\Reference\ReferenceManager;
use OCA\Peertube\AppInfo\Application;
use OCA\Peertube\Service\PeertubeAPIService;
use OCP\Collaboration\Reference\IReference;
use OCP\IConfig;
use OCP\IL10N;

use OCP\IURLGenerator;
use Throwable;

class PeertubeReferenceProvider extends ADiscoverableReferenceProvider implements ISearchableReferenceProvider {

	private const RICH_OBJECT_TYPE_VIDEO = Application::APP_ID . '_video';

	private PeertubeAPIService $peertubeAPIService;
	private ?string $userId;
	private IConfig $config;
	private ReferenceManager $referenceManager;
	private IL10N $l10n;
	private IURLGenerator $urlGenerator;
	private LinkReferenceProvider $linkReferenceProvider;
	private UtilsService $utilsService;

	public function __construct(PeertubeAPIService $peertubeAPIService,
								IConfig $config,
								IL10N $l10n,
								IURLGenerator $urlGenerator,
								ReferenceManager $referenceManager,
								LinkReferenceProvider $linkReferenceProvider,
								UtilsService $utilsService,
								?string $userId) {
		$this->peertubeAPIService = $peertubeAPIService;
		$this->userId = $userId;
		$this->config = $config;
		$this->referenceManager = $referenceManager;
		$this->l10n = $l10n;
		$this->urlGenerator = $urlGenerator;
		$this->linkReferenceProvider = $linkReferenceProvider;
		$this->utilsService = $utilsService;
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string	{
		return 'peertube-videos';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('Peertube videos');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int	{
		return 10;
	}

	/**
	 * @inheritDoc
	 */
	public function getIconUrl(): string {
		return $this->urlGenerator->getAbsoluteURL(
			$this->urlGenerator->imagePath(Application::APP_ID, 'app-dark.svg')
		);
	}

	/**
	 * @inheritDoc
	 */
	public function getSupportedSearchProviderIds(): array {
		$searchProviderIds = [
			'peertube-search-video',
		];
		if ($this->userId !== null) {
			$searchVideosEnabled = $this->config->getUserValue($this->userId, Application::APP_ID, 'search_enabled', '1') === '1';
			if ($searchVideosEnabled) {
				return $searchProviderIds;
			}
			return [];
		}
		return $searchProviderIds;
	}

	/**
	 * @inheritDoc
	 */
	public function matchReference(string $referenceText): bool {
		$adminLinkPreviewEnabled = $this->config->getAppValue(Application::APP_ID, 'link_preview_enabled', '1') === '1';
		$userLinkPreviewEnabled = $this->config->getUserValue($this->userId, Application::APP_ID, 'link_preview_enabled', '1') === '1';
		if (!$adminLinkPreviewEnabled || !$userLinkPreviewEnabled) {
			return false;
		}

		return $this->getVideoId($referenceText) !== null;
	}

	/**
	 * @inheritDoc
	 */
	public function resolveReference(string $referenceText): ?IReference {
		if ($this->matchReference($referenceText)) {
			try {
				$videoId = $this->getVideoId($referenceText);
				$videoInfo = $this->peertubeAPIService->getVideoInfo($videoId);
				$videoInfo['embed_url'] = 'https://framatube.org' . $videoInfo['embedPath'];
				$reference = new Reference($referenceText);
				$reference->setTitle($videoInfo['name']);
				$thumbnailUrl = $this->urlGenerator->linkToRouteAbsolute(
					Application::APP_ID . '.peertubeAPI.getThumbnail',
					[
						'thumbnailPath' => $videoInfo['thumbnailPath'],
						'searchInstanceUrl' => 'https://framatube.org',
						'fallbackName' => $videoInfo['name'],
					]
				);
				$reference->setImageUrl($thumbnailUrl);
				$reference->setRichObject(
					self::RICH_OBJECT_TYPE_VIDEO,
					$videoInfo
				);
				return $reference;
			} catch (Exception | Throwable $e) {
				// fallback to opengraph
				return $this->linkReferenceProvider->resolveReference($referenceText);
			}
		}

		return null;
	}

	/**
	 * @param string $url
	 * @return array|null
	 */
	private function getVideoId(string $url): ?string {
		preg_match('/^(?:https?:\/\/)?(?:www\.)?framatube\.org\/w\/([a-zA-Z0-9]+)$/i', $url, $matches);
		if (count($matches) > 1) {
			return $matches[1];
		}

		return null;
	}

	/**
	 * We use the userId here because when connecting/disconnecting from the GitHub account,
	 * we want to invalidate all the user cache and this is only possible with the cache prefix
	 * @inheritDoc
	 */
	public function getCachePrefix(string $referenceId): string {
		return $this->userId ?? '';
	}

	/**
	 * We don't use the userId here but rather a reference unique id
	 * @inheritDoc
	 */
	public function getCacheKey(string $referenceId): ?string {
		return $referenceId;
	}

	/**
	 * @param string $userId
	 * @return void
	 */
	public function invalidateUserCache(string $userId): void {
		$this->referenceManager->invalidateCache($userId);
	}
}
