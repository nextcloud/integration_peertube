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
			<p class="settings-hint">
				<InformationOutlineIcon :size="20" class="icon" />
				{{ t('integration_tmdb', 'Leave this field empty to use the default API key which has a very large quota.') }}
			</p>
			<p class="settings-hint">
				<a href="https://themoviedb.org" target="_blank">
					{{ t('integration_tmdb', 'You can create an app and API key in the "API" section of your TMDB account settings.') }}
				</a>
			</p>
		</div>
	</div>
</template>

<script>
import InformationOutlineIcon from 'vue-material-design-icons/InformationOutline.vue'
import KeyIcon from 'vue-material-design-icons/Key.vue'

import TmdbIcon from './icons/TmdbIcon.vue'

import { loadState } from '@nextcloud/initial-state'
import { generateUrl } from '@nextcloud/router'
import axios from '@nextcloud/axios'
import { delay } from '../utils.js'
import { showSuccess, showError } from '@nextcloud/dialogs'

export default {
	name: 'AdminSettings',

	components: {
		TmdbIcon,
		KeyIcon,
		InformationOutlineIcon,
	},

	props: [],

	data() {
		return {
			state: loadState('integration_tmdb', 'admin-config'),
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
		saveOptions(values) {
			const req = {
				values,
			}
			const url = generateUrl('/apps/integration_tmdb/admin-config')
			axios.put(url, req).then((response) => {
				showSuccess(t('integration_tmdb', 'TMDB options saved'))
			}).catch((error) => {
				showError(
					t('integration_tmdb', 'Failed to save TMDB options')
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
#tmdb_prefs {
	#tmdb-content {
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
