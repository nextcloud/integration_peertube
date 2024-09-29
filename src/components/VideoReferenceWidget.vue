<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<iframe
		:title="richObject.name"
		:src="richObject.embed_url"
		:allowfullscreen="true"
		sandbox="allow-same-origin allow-scripts allow-popups"
		width="100%"
		height="315"
		frameborder="0" />
</template>

<script>
export default {
	name: 'VideoReferenceWidget',

	components: {
	},

	props: {
		richObjectType: {
			type: String,
			default: '',
		},
		richObject: {
			type: Object,
			default: null,
		},
		accessible: {
			type: Boolean,
			default: true,
		},
	},

	data() {
		return {
		}
	},

	computed: {
	},

	methods: {
	},
}
</script>

<style scoped lang="scss">
// nothing yet
</style>
