<template>
	<div id="tmdb_prefs" class="section">
		<h2>
			<TmdbIcon class="icon" />
			{{ t('integration_tmdb', 'TMDB integration') }}
		</h2>
		<div id="tmdb-content">
			<div class="line">
				<label for="tmdb-api-key">
					<KeyIcon :size="20" class="icon" />
					{{ t('integration_tmdb', 'TMDB API key') }}
				</label>
				<input id="tmdb-api-key"
					v-model="state.api_key"
					type="password"
					:placeholder="t('integration_tmdb', '...')"
					@input="onInput">
			</div>
			<div id="tmdb-search-block">
				<NcCheckboxRadioSwitch
					:checked="state.search_enabled"
					@update:checked="onCheckboxChanged($event, 'search_enabled')">
					{{ t('integration_tmdb', 'Enable searching for movies/persons/series') }}
				</NcCheckboxRadioSwitch>
				<br>
				<p v-if="state.search_enabled" class="settings-hint">
					<InformationOutlineIcon :size="20" class="icon" />
					{{ t('integration_tmdb', 'Warning, everything you type in the search bar will be sent to TMDB.') }}
				</p>
				<NcCheckboxRadioSwitch
					:checked="state.link_preview_enabled"
					@update:checked="onCheckboxChanged($event, 'link_preview_enabled')">
					{{ t('integration_tmdb', 'Enable TMDB/IMDB link previews') }}
				</NcCheckboxRadioSwitch>
			</div>
			<NcCheckboxRadioSwitch
				:checked="state.navigation_enabled"
				@update:checked="onCheckboxChanged($event, 'navigation_enabled')">
				{{ t('integration_tmdb', 'Enable navigation link') }}
			</NcCheckboxRadioSwitch>
		</div>
	</div>
</template>

<script>
import KeyIcon from 'vue-material-design-icons/Key.vue'
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'

import TmdbIcon from './icons/TmdbIcon.vue'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { showSuccess, showError } from '@nextcloud/dialogs'

import NcCheckboxRadioSwitch from '@nextcloud/vue/dist/Components/NcCheckboxRadioSwitch.js'
import { delay } from '../utils.js'

export default {
	name: 'PersonalSettings',

	components: {
		TmdbIcon,
		NcCheckboxRadioSwitch,
		InformationOutlineIcon,
		KeyIcon,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_tmdb', 'user-config'),
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
			const url = generateUrl('/apps/integration_tmdb/config')
			axios.put(url, req).then((response) => {
				showSuccess(t('integration_tmdb', 'OpenStreetMap options saved'))
			}).catch((error) => {
				showError(
					t('integration_tmdb', 'Failed to save OpenStreetMap options')
					+ ': ' + (error.response?.data?.error ?? '')
				)
				console.debug(error)
			})
		},
	},
}
</script>

<style scoped lang="scss">
#tmdb_prefs {
	#tmdb-content {
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
