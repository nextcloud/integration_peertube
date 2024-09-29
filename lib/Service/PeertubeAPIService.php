<?php
/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Service;

use Exception;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Exception\ServerException;
use OCA\Peertube\AppInfo\Application;
use OCP\Http\Client\IClient;
use OCP\IConfig;
use OCP\IL10N;
use OCP\L10N\IFactory;
use Psr\Log\LoggerInterface;
use OCP\Http\Client\IClientService;
use Throwable;

/**
 * Service to make requests to Peertube REST API
 */
class PeertubeAPIService {

	private IClient $client;

	public function __construct (string                  $appName,
								 private LoggerInterface $logger,
								 private IL10N           $l10n,
								 private IConfig         $config,
								 private IFactory        $l10nFactory,
								 IClientService          $clientService) {
		$this->client = $clientService->newClient();
	}

	/**
	 * @return array
	 */
	public function getPeertubeInstances(): array {
		$instances = trim($this->config->getAppValue(Application::APP_ID, 'instances'));
		$instances = preg_replace('/\s+/', ',', $instances);
		$instances = preg_replace('/\n/', ',', $instances);
		return explode(',', $instances);
	}

	/**
	 * Search videos
	 *
	 * @param string $query
	 * @param int $offset
	 * @param int $limit
	 * @return array request result
	 */
	public function searchVideo(string $query, int $offset = 0, int $limit = 5): array {
		$instances = $this->getPeertubeInstances();
		$params = [
			'search' => $query,
			'count' => $offset + $limit,
		];
		$cumulatedResults = [];
		foreach ($instances as $instanceUrl) {
			$result = $this->request($instanceUrl, 'search/videos', $params);
			if (!isset($result['error']) && isset($result['data']) && is_array($result['data'])) {
				$instanceResults = array_map(static function (array $video) use ($instanceUrl) {
					$video['search_instance_url'] = $instanceUrl;
					return $video;
				}, array_slice($result['data'], $offset, $limit));
				$cumulatedResults = array_merge($cumulatedResults, $instanceResults);
				if (count($cumulatedResults) >= $limit) {
					return $cumulatedResults;
				}
			} else {
				$this->logger->warning('Peertube search error (' . $instanceUrl . ') : ' . $result['error'], ['app' => Application::APP_ID]);
			}
		}
		return $cumulatedResults;
	}

	private function getLanguage(): string {
		$language = $this->l10nFactory->findLanguage();
		if (strlen($language) === 2) {
			return $language . '-' . strtoupper($language);
		}
		return $language;
	}

	/**
	 * @param string $instanceUrl
	 * @param string $videoId
	 * @return array
	 */
	public function getVideoInfo(string $instanceUrl, string $videoId): array {
		return $this->request($instanceUrl, 'videos/' . $videoId);
	}

	/**
	 * @param string $serverUrl
	 * @param string $thumbnailPath
	 * @return array
	 */
	public function getThumbnail(string $serverUrl, string $thumbnailPath): array {
		$instanceUrls = $this->getPeertubeInstances();
		if (!in_array($serverUrl, $instanceUrls)) {
			return ['error' => 'Invalid Peertube instance: ' . $serverUrl];
		}

		$url = $serverUrl . $thumbnailPath;
		$options = [
			'headers' => [
				'User-Agent' => 'Nextcloud Peertube integration',
			],
		];
		try {
			$response = $this->client->get($url, $options);
			$body = $response->getBody();
			$respCode = $response->getStatusCode();

			if ($respCode >= 400) {
				return ['error' => 'Peertube thumbnail request failure'];
			} else {
				return [
					'body' => $body,
					'headers' => $response->getHeaders(),
				];
			}
		} catch (Exception | Throwable $e) {
			$this->logger->warning('Peertube get image error : ' . $e->getMessage(), ['app' => Application::APP_ID]);
			return ['error' => $e->getMessage()];
		}
	}

	/**
	 * Make an HTTP request to the Peertube API
	 * @param string $instanceUrl
	 * @param string $endPoint The path to reach in api.github.com
	 * @param array $params Query parameters (key/val pairs)
	 * @param string $method HTTP query method
	 * @param bool $rawResponse
	 * @return array decoded request result or error
	 */
	public function request(string $instanceUrl, string $endPoint, array $params = [], string $method = 'GET', bool $rawResponse = false): array {
		try {
			$url = $instanceUrl . '/api/v1/' . $endPoint;
			$options = [
				'headers' => [
					'User-Agent' => 'Nextcloud Peertube integration',
				],
			];

			if (count($params) > 0) {
				if ($method === 'GET') {
					$paramsContent = http_build_query($params);
					$url .= '?' . $paramsContent;
				} else {
					$options['body'] = json_encode($params);
				}
			}

			if ($method === 'GET') {
				$response = $this->client->get($url, $options);
			} else if ($method === 'POST') {
				$response = $this->client->post($url, $options);
			} else if ($method === 'PUT') {
				$response = $this->client->put($url, $options);
			} else if ($method === 'DELETE') {
				$response = $this->client->delete($url, $options);
			} else {
				return ['error' => $this->l10n->t('Bad HTTP method')];
			}
			$body = $response->getBody();
			$respCode = $response->getStatusCode();

			if ($respCode >= 400) {
				return ['error' => $this->l10n->t('Bad credentials')];
			} else {
				if ($rawResponse) {
					return [
						'body' => $body,
						'headers' => $response->getHeaders(),
					];
				} else {
					return json_decode($body, true) ?: [];
				}
			}
		} catch (ClientException | ServerException $e) {
			$responseBody = $e->getResponse()->getBody();
			$parsedResponseBody = json_decode($responseBody, true);
			if ($e->getResponse()->getStatusCode() === 404) {
				$this->logger->debug('Peertube API error : ' . $e->getMessage(), ['response_body' => $parsedResponseBody, 'app' => Application::APP_ID]);
			} else {
				$this->logger->warning('Peertube API error : ' . $e->getMessage(), ['response_body' => $parsedResponseBody, 'app' => Application::APP_ID]);
			}
			return [
				'error' => $e->getMessage(),
				'body' => $parsedResponseBody,
			];
		} catch (Exception | Throwable $e) {
			$this->logger->warning('Peertube API error : ' . $e->getMessage(), ['app' => Application::APP_ID]);
			return ['error' => $e->getMessage()];
		}
	}
}
