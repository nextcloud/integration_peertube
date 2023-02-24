<?php
namespace OCA\Tmdb\Settings;

use OCP\AppFramework\Http\TemplateResponse;
use OCP\AppFramework\Services\IInitialState;
use OCP\IConfig;
use OCP\Settings\ISettings;

use OCA\Tmdb\AppInfo\Application;

class Personal implements ISettings {

	private IConfig $config;
	private IInitialState $initialStateService;
	private ?string $userId;

	public function __construct(IConfig       $config,
								IInitialState $initialStateService,
								?string       $userId) {
		$this->config = $config;
		$this->initialStateService = $initialStateService;
		$this->userId = $userId;
	}

	/**
	 * @return TemplateResponse
	 */
	public function getForm(): TemplateResponse {
		$searchEnabled = $this->config->getUserValue($this->userId, Application::APP_ID, 'search_enabled', '1') === '1';
		$navigationEnabled = $this->config->getUserValue($this->userId, Application::APP_ID, 'navigation_enabled', '0') === '1';
		$linkPreviewEnabled = $this->config->getUserValue($this->userId, Application::APP_ID, 'link_preview_enabled', '1') === '1';
		$adminApiKey = $this->config->getAppValue(Application::APP_ID, 'api_key', Application::DEFAULT_API_KEY_V3) ?: Application::DEFAULT_API_KEY_V3;
		$apiKey = $this->config->getUserValue($this->userId, Application::APP_ID, 'api_key', $adminApiKey) ?: $adminApiKey;

		$userConfig = [
			'search_enabled' => $searchEnabled,
			'navigation_enabled' => $navigationEnabled ,
			'link_preview_enabled' => $linkPreviewEnabled,
			'api_key' => $apiKey,
		];
		$this->initialStateService->provideInitialState('user-config', $userConfig);
		return new TemplateResponse(Application::APP_ID, 'personalSettings');
	}

	public function getSection(): string {
		return 'connected-accounts';
	}

	public function getPriority(): int {
		return 10;
	}
}
