<template>
	<div id="peertube_prefs" class="section">
		<h2>
			<PeertubeIcon class="icon" />
			{{ t('integration_peertube', 'Peertube integration') }}
		</h2>
		<div id="peertube-content">
			<div class="line">
				<label for="peertube-instances">
					<EarthIcon :size="20" class="icon" />
					{{ t('integration_peertube', 'Peertube instance list (separated by comas or new lines)') }}
				</label>
				<textarea id="peertube-instances"
					v-model="state.instances"
					:placeholder="…"
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
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'
import EarthIcon from 'vue-material-design-icons/Earth.vue'

import PeertubeIcon from './icons/PeertubeIcon.vue'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils.js'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
	name: 'AdminSettings',

	components: {
		PeertubeIcon,
		EarthIcon,
		InformationOutlineIcon,
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
			this.loading = true
			delay(() => {
				this.saveOptions({
					instances: this.state.instances,
				})
			}, 2000)()
		},
		saveOptions(values) {
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_peertube/admin-config')
			axios.put(url, req).then((response) => {
				showSuccess(t('integration_peertube', 'Peertube options saved'))
			}).catch((error) => {
				showError(
					t('integration_peertube', 'Failed to save Peertube options')
					+ ': ' + (error.response?.data?.error ?? '')
				)
				console.error(error)
			}).then(() => {
				this.loading = false
			})
		},
	},
}
</script>

<style scoped lang="scss">
#peertube_prefs {
	#peertube-content {
		margin-left: 40px;
	}
	h2,
	.line,
	.settings-hint {
		display: flex;
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
		width: 350px;
		height: 100px;
	}

	.line {
		> label {
			width: 300px;
			display: flex;
			align-items: center;
		}
		> input {
			width: 300px;
		}
	}
}
</style>
