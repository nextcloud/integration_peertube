<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div id="peertube_prefs" class="settings-section">
		<h2>
			<PeertubeIcon class="icon" />
			{{ t('integration_peertube', 'PeerTube integration') }}
		</h2>
		<div id="peertube-content">
			<div id="peertube-search-block">
				<NcCheckboxRadioSwitch
					:model-value="state.search_enabled"
					@update:model-value="onCheckboxChanged($event, 'search_enabled')">
					{{ t('integration_peertube', 'Enable searching for PeerTube videos') }}
				</NcCheckboxRadioSwitch>
				<NcNoteCard
					v-if="state.search_enabled"
					type="warning"
					class="settings-hint"
					:text="t('integration_peertube', 'Warning, everything you type in the search bar will be sent to some PeerTube instances.')" />
				<NcCheckboxRadioSwitch
					:model-value="state.link_preview_enabled"
					@update:model-value="onCheckboxChanged($event, 'link_preview_enabled')">
					{{ t('integration_peertube', 'Enable PeerTube video link previews') }}
				</NcCheckboxRadioSwitch>
			</div>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import NcCheckboxRadioSwitch from '@nextcloud/vue/components/NcCheckboxRadioSwitch'
import NcNoteCard from '@nextcloud/vue/components/NcNoteCard'
import PeertubeIcon from './icons/PeertubeIcon.vue'
import { delay } from '../utils.js'

export default {
	name: 'PersonalSettings',

	components: {
		PeertubeIcon,
		NcCheckboxRadioSwitch,
		NcNoteCard,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_peertube', 'user-config'),
		}
	},

	computed: {
	},

	watch: {
	},

	mounted() {
	},

	methods: {
		onInput() {
			delay(() => {
				this.saveOptions({
					api_key: this.state.api_key,
				})
			}, 2000)()
		},

		onCheckboxChanged(newValue, key) {
			this.state[key] = newValue
			this.saveOptions({ [key]: this.state[key] ? '1' : '0' })
		},

		saveOptions(values) {
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_peertube/config')
			axios.put(url, req).then(() => {
				showSuccess(t('integration_peertube', 'PeerTube options saved'))
			}).catch((error) => {
				showError(t('integration_peertube', 'Failed to save PeerTube options')
					+ ': ' + (error.response?.data?.error ?? ''))
				console.debug(error)
			})
		},
	},
}
</script>

<style scoped lang="scss">
#peertube_prefs {
	margin-inline-start: 12px;

	#peertube-content {
		margin-inline-start: 32px;
		max-width: 800px;
	}

	h2,
	.settings-hint {
		display: flex;
		justify-content: start;
		align-items: center;
		.icon {
			margin-inline-end: 4px;
		}
	}

	.settings-hint {
		margin-inline-start: 32px;
	}

	h2 .icon {
		margin-inline-end: 8px;
	}
}
</style>
