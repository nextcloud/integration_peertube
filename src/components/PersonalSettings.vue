<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<div id="peertube_prefs" class="section">
		<h2>
			<PeertubeIcon class="icon" />
			{{ t('integration_peertube', 'PeerTube integration') }}
		</h2>
		<div id="peertube-content">
			<div id="peertube-search-block">
				<NcFormBox>
					<NcFormBoxSwitch
						:model-value="state.search_enabled"
						@update:model-value="onCheckboxChanged($event, 'search_enabled')">
						{{ t('integration_peertube', 'Enable searching for PeerTube videos') }}
					</NcFormBoxSwitch>
					<NcFormBoxSwitch
						:model-value="state.link_preview_enabled"
						@update:model-value="onCheckboxChanged($event, 'link_preview_enabled')">
						{{ t('integration_peertube', 'Enable PeerTube video link previews') }}
					</NcFormBoxSwitch>
				</NcFormBox>
				<NcNoteCard
					v-if="state.search_enabled"
					type="warning">
					{{ t('integration_peertube', 'Warning, everything you type in the search bar will be sent to some PeerTube instances.') }}
				</NcNoteCard>
			</div>
		</div>
	</div>
</template>

<script>
import axios from '@nextcloud/axios'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import NcFormBoxSwitch from '@nextcloud/vue/components/NcFormBoxSwitch'
import NcFormBox from '@nextcloud/vue/components/NcFormBox'
import NcNoteCard from '@nextcloud/vue/components/NcNoteCard'
import PeertubeIcon from './icons/PeertubeIcon.vue'
import { delay } from '../utils.js'

export default {
	name: 'PersonalSettings',

	components: {
		PeertubeIcon,
		NcNoteCard,
		NcFormBoxSwitch,
		NcFormBox,
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
	#peertube-content {
		margin-inline-start: 40px;
		max-width: 800px;
	}

	h2 {
		display: flex;
		justify-content: start;
		align-items: center;
		gap: 8px;
	}
}
</style>
