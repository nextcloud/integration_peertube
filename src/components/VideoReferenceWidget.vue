<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<iframe :title="richObject.name"
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
		richObject: {
			type: Object,
			default: null,
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
