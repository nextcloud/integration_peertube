<?php

/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

namespace OCA\Peertube\Listener;

use OCA\Peertube\Service\PeertubeAPIService;
use OCP\AppFramework\Http\ContentSecurityPolicy;
use OCP\EventDispatcher\Event;
use OCP\EventDispatcher\IEventListener;
use OCP\Security\CSP\AddContentSecurityPolicyEvent;

/**
 * @template-implements IEventListener<AddContentSecurityPolicyEvent>
 */
class ContentSecurityPolicyListener implements IEventListener {

	public function __construct(
		private PeertubeAPIService $peertubeAPIService,
	) {
	}

	/**
	 * @inheritDoc
	 */
	public function handle(Event $event): void {
		if (!$event instanceof AddContentSecurityPolicyEvent) {
			return;
		}

		$policy = new ContentSecurityPolicy();
		foreach ($this->peertubeAPIService->getPeertubeInstances() as $instanceUrl) {
			$policy->addAllowedFrameDomain($instanceUrl);
		}
		$event->addPolicy($policy);
	}
}
