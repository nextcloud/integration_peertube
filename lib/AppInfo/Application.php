<?php
/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\AppInfo;

use OCA\Peertube\Listener\ContentSecurityPolicyListener;
use OCA\Peertube\Listener\PeertubeReferenceListener;
use OCA\Peertube\Reference\PeertubeReferenceProvider;
use OCA\Peertube\Search\VideoSearchProvider;
use OCP\Collaboration\Reference\RenderReferenceEvent;

use OCP\AppFramework\App;
use OCP\AppFramework\Bootstrap\IRegistrationContext;
use OCP\AppFramework\Bootstrap\IBootContext;
use OCP\AppFramework\Bootstrap\IBootstrap;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

class Application extends App implements IBootstrap {

	public const APP_ID = 'integration_peertube';

	public function __construct(array $urlParams = []) {
		parent::__construct(self::APP_ID, $urlParams);
	}

	public function register(IRegistrationContext $context): void {
		$context->registerSearchProvider(VideoSearchProvider::class);

		$context->registerReferenceProvider(PeertubeReferenceProvider::class);

		$context->registerEventListener(AddContentSecurityPolicyEvent::class, ContentSecurityPolicyListener::class);
		$context->registerEventListener(RenderReferenceEvent::class, PeertubeReferenceListener::class);
	}

	public function boot(IBootContext $context): void {
	}
}

