<template>
	<div id="peertube_prefs" class="section">
		<h2>
			<PeertubeIcon class="icon" />
			{{ t('integration_peertube', 'Peertube integration') }}
		</h2>
		<div id="peertube-content">
			<div id="peertube-search-block">
				<NcCheckboxRadioSwitch
					:checked="state.search_enabled"
					@update:checked="onCheckboxChanged($event, 'search_enabled')">
					{{ t('integration_peertube', 'Enable searching for Peertube videos') }}
				</NcCheckboxRadioSwitch>
				<br>
				<p v-if="state.search_enabled" class="settings-hint">
					<InformationOutlineIcon :size="20" class="icon" />
					{{ t('integration_peertube', 'Warning, everything you type in the search bar will be sent to some Peertube instances.') }}
				</p>
				<NcCheckboxRadioSwitch
					:checked="state.link_preview_enabled"
					@update:checked="onCheckboxChanged($event, 'link_preview_enabled')">
					{{ t('integration_peertube', 'Enable Peertube video link previews') }}
				</NcCheckboxRadioSwitch>
			</div>
		</div>
	</div>
</template>

<script>
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'

import PeertubeIcon from './icons/PeertubeIcon.vue'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'

import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'
import { delay } from '../utils.js'

export default {
	name: 'PersonalSettings',

	components: {
		PeertubeIcon,
		NcCheckboxRadioSwitch,
		InformationOutlineIcon,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_peertube', 'user-config'),
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
			axios.put(url, req).then((response) => {
				showSuccess(t('integration_peertube', 'OpenStreetMap options saved'))
			}).catch((error) => {
				showError(
					t('integration_peertube', 'Failed to save OpenStreetMap options')
					+ ': ' + (error.response?.data?.error ?? '')
				)
				console.debug(error)
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
		.icon {
			margin-right: 4px;
		}
	}

	h2 .icon {
		margin-right: 8px;
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
