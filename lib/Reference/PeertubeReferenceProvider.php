<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Reference;

use Exception;
use OCA\Peertube\AppInfo\Application;
use OCA\Peertube\Service\PeertubeAPIService;
use OCP\Collaboration\Reference\ADiscoverableReferenceProvider;
use OCP\Collaboration\Reference\IReference;
use OCP\Collaboration\Reference\IReferenceManager;
use OCP\Collaboration\Reference\ISearchableReferenceProvider;
use OCP\Collaboration\Reference\LinkReferenceProvider;
use OCP\Collaboration\Reference\Reference;
use OCP\IConfig;
use OCP\IL10N;

use OCP\IURLGenerator;
use Throwable;

class PeertubeReferenceProvider extends ADiscoverableReferenceProvider implements ISearchableReferenceProvider {

	private const RICH_OBJECT_TYPE_VIDEO = Application::APP_ID . '_video';

	public function __construct(
		private PeertubeAPIService $peertubeAPIService,
		private IConfig $config,
		private IL10N $l10n,
		private IURLGenerator $urlGenerator,
		private IReferenceManager $referenceManager,
		private LinkReferenceProvider $linkReferenceProvider,
		private ?string $userId,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'peertube-videos';
	}

	/**
	 * @inheritDoc
	 */
	public function getTitle(): string {
		return $this->l10n->t('PeerTube videos');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(): int {
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
		if (count($this->peertubeAPIService->getPeertubeInstances()) === 0) {
			return [];
		}
		return ['peertube-search-video'];
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

		return $this->getInfoFromVideoUrl($referenceText) !== null;
	}

	/**
	 * @inheritDoc
	 */
	public function resolveReference(string $referenceText): ?IReference {
		if ($this->matchReference($referenceText)) {
			try {
				$urlInfo = $this->getInfoFromVideoUrl($referenceText);
				if ($urlInfo === null) {
					return $this->linkReferenceProvider->resolveReference($referenceText);
				}
				$videoId = $urlInfo['video_id'];
				$instanceUrl = $urlInfo['instance_url'];
				if (empty($instanceUrl)) {
					return $this->linkReferenceProvider->resolveReference($referenceText);
				}

				$videoInfo = $this->peertubeAPIService->getVideoInfo($instanceUrl, $videoId);
				if (!empty($videoInfo['error'])) {
					return $this->linkReferenceProvider->resolveReference($referenceText);
				}
				$videoInfo['embed_url'] = $instanceUrl . $videoInfo['embedPath'];
				$reference = new Reference($referenceText);
				$reference->setTitle($videoInfo['name'] ?? '???');
				$thumbnailUrl = $this->urlGenerator->linkToRouteAbsolute(
					Application::APP_ID . '.peertubeAPI.getThumbnail',
					[
						'thumbnailPath' => $videoInfo['thumbnailPath'] ?? '',
						'searchInstanceUrl' => $instanceUrl,
						'fallbackName' => $videoInfo['name'] ?? '???',
					]
				);
				$reference->setImageUrl($thumbnailUrl);
				$reference->setRichObject(
					self::RICH_OBJECT_TYPE_VIDEO,
					$videoInfo
				);
				return $reference;
			} catch (Exception|Throwable $e) {
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
	private function getInfoFromVideoUrl(string $url): ?array {
		foreach ($this->peertubeAPIService->getPeertubeInstances() as $instanceUrl) {
			if (str_starts_with($url, $instanceUrl)) {
				preg_match('/\/w\/([a-zA-Z0-9-]+)$/i', $url, $matches);
				if (count($matches) > 1) {
					return [
						'instance_url' => $instanceUrl,
						'video_id' => $matches[1],
					];
				}

				preg_match('/\/videos\/watch\/([a-zA-Z0-9-]+)$/i', $url, $matches);
				if (count($matches) > 1) {
					return [
						'instance_url' => $instanceUrl,
						'video_id' => $matches[1],
					];
				}
			}
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
