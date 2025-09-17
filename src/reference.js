/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { getCSPNonce } from '@nextcloud/auth'
import { linkTo } from '@nextcloud/router'
import { registerWidget } from '@nextcloud/vue/components/NcRichText'

__webpack_nonce__ = getCSPNonce()
__webpack_public_path__ = linkTo('integration_peertube', 'js/') // eslint-disable-line

registerWidget('integration_peertube_video', async (el, { richObject, accessible, interactive }) => {
	const { createApp } = await import(/* webpackChunkName: "vue-lazy" */'vue')
	const { default: VideoReferenceWidget } = await import(/* webpackChunkName: "reference-video-lazy" */'./components/VideoReferenceWidget.vue')
	const app = createApp(VideoReferenceWidget, {
		richObject,
		accessible,
		interactive,
	})
	app.mixin({ methods: { t, n } })
	app.mount(el)
})
