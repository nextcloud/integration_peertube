<?php

declare(strict_types=1);

/**
 * @copyright Copyright (c) 2023, Julien Veyssier
 *
 * @author Julien Veyssier <julien-nc@posteo.net>
 *
 * @license AGPL-3.0
 *
 * This code is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License, version 3,
 * as published by the Free Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License, version 3,
 * along with this program. If not, see <http://www.gnu.org/licenses/>
 *
 */
namespace OCA\Peertube\Search;

use OCA\Peertube\Service\PeertubeAPIService;
use OCA\Peertube\AppInfo\Application;
use OCP\App\IAppManager;
use OCP\IL10N;
use OCP\IConfig;
use OCP\IURLGenerator;
use OCP\IUser;
use OCP\Search\IProvider;
use OCP\Search\ISearchQuery;
use OCP\Search\SearchResult;
use OCP\Search\SearchResultEntry;

class VideoSearchProvider implements IProvider {

	public function __construct(private IAppManager        $appManager,
								private IL10N              $l10n,
								private IConfig            $config,
								private IURLGenerator      $urlGenerator,
								private PeertubeAPIService $peertubeAPIService) {
	}

	/**
	 * @inheritDoc
	 */
	public function getId(): string {
		return 'peertube-search-video';
	}

	/**
	 * @inheritDoc
	 */
	public function getName(): string {
		return $this->l10n->t('PeerTube videos');
	}

	/**
	 * @inheritDoc
	 */
	public function getOrder(string $route, array $routeParameters): int {
		if (strpos($route, Application::APP_ID . '.') === 0) {
			// Active app, prefer Peertube results
			return -1;
		}

		return 20;
	}

	/**
	 * @inheritDoc
	 */
	public function search(IUser $user, ISearchQuery $query): SearchResult {
		if (!$this->appManager->isEnabledForUser(Application::APP_ID, $user)) {
			return SearchResult::complete($this->getName(), []);
		}

		$limit = $query->getLimit();
		$term = $query->getTerm();
		$offset = $query->getCursor();
		$offset = $offset ? intval($offset) : 0;

		$routeFrom = $query->getRoute();
		$requestedFromSmartPicker = $routeFrom === '' || $routeFrom === 'smart-picker';

		if (!$requestedFromSmartPicker) {
			$searchEnabled = $this->config->getUserValue($user->getUID(), Application::APP_ID, 'search_enabled', '1') === '1';
			if (!$searchEnabled) {
				return SearchResult::paginated($this->getName(), [], 0);
			}
		}

		$searchResult = $this->peertubeAPIService->searchVideo($term, $offset, $limit);
		if (isset($searchResult['error'])) {
			$items = [];
		} else {
			$items = $searchResult;
		}

		$formattedResults = array_map(function (array $entry): SearchResultEntry {
			return new SearchResultEntry(
				$this->getThumbnailUrl($entry),
				$this->getMainText($entry),
				$this->getSubline($entry),
				$this->getLink($entry),
				$this->getIconUrl($entry),
				false
			);
		}, $items);

		return SearchResult::paginated(
			$this->getName(),
			$formattedResults,
			$offset + $limit
		);
	}

	protected function getMainText(array $entry): string {
		return $entry['name'] ?? '???';
	}

	protected function getSubline(array $entry): string {
		if (isset($entry['account'], $entry['account']['displayName'], $entry['account']['host'])) {
			return $entry['account']['displayName'] . '@' . $entry['account']['host'];
		}
		return '';
	}

	protected function getLink(array $entry): string {
		return $entry['search_instance_url'] . '/w/' . $entry['shortUUID'];
	}

	protected function getIconUrl(array $entry): string {
		return '';
	}

	protected function getThumbnailUrl(array $entry): string {
		return $this->urlGenerator->linkToRouteAbsolute(
			Application::APP_ID . '.peertubeAPI.getThumbnail',
			[
				'thumbnailPath' => $entry['thumbnailPath'],
				'searchInstanceUrl' => $entry['search_instance_url'],
				'fallbackName' => $entry['name'],
			]
		);
	}
}
