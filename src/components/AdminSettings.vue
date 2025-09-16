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
			<div class="line">
				<label for="peertube-instances">
					<EarthIcon :size="20" class="icon" />
					{{ t('integration_peertube', 'PeerTube instance list (separated by commas or new lines)') }}
				</label>
				<NcLoadingIcon v-if="loading" :size="20" class="icon" />
			</div>
			<div class="line">
				<textarea id="peertube-instances"
					v-model="state.instances"
					placeholder="…"
					@input="onInput" />
			</div>
			<p class="settings-hint">
				<InformationOutlineIcon :size="20" class="icon" />
				{{ t('integration_peertube', 'Nextcloud will search and resolve video links for all those instances.') }}
			</p>
		</div>
	</div>
</template>

<script>
import EarthIcon from 'vue-material-design-icons/Earth.vue'
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'
import PeertubeIcon from './icons/PeertubeIcon.vue'

import NcLoadingIcon from '@nextcloud/vue/dist/Components/NcLoadingIcon.js'

import axios from '@nextcloud/axios'
import { showError, showSuccess } from '@nextcloud/dialogs'
import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import { delay } from '../utils.js'

export default {
	name: 'AdminSettings',

	components: {
		PeertubeIcon,
		EarthIcon,
		InformationOutlineIcon,
		NcLoadingIcon,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_peertube', 'admin-config'),
			loading: false,
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
					instances: this.state.instances,
				})
			}, 2000)()
		},
		saveOptions(values) {
			this.loading = true
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_peertube/admin-config')
			axios.put(url, req).then((response) => {
				showSuccess(t('integration_peertube', 'PeerTube options saved'))
			}).catch((error) => {
				showError(t('integration_peertube', 'Failed to save PeerTube options')
					+ ': ' + (error.response?.data?.error ?? ''))
				console.error(error)
			}).finally(() => {
				this.loading = false
			})
		},
	},
}
</script>

<style scoped lang="scss">
#peertube_prefs {
	#peertube-content {
		margin: 24px 0 0 32px;
	}
	h2,
	.line,
	.settings-hint {
		display: flex;
		justify-content: start;
		align-items: center;
		margin-top: 12px;
		.icon {
			margin-right: 4px;
		}
	}

	h2 .icon {
		margin-right: 8px;
	}

	#peertube-instances {
		width: 550px;
		height: 100px;
	}

	.line {
		> label {
			display: flex;
			align-items: center;
		}
		> input {
			width: 300px;
		}
	}
}
</style>
