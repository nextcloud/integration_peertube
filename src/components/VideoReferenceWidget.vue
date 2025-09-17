<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<NcReferenceWidget
		v-if="!interactive && reference"
		class="non-interactive-widget"
		:reference="reference" />
	<iframe
		v-if="interactive"
		:title="richObject.name"
		:src="richObject.embed_url"
		:allowfullscreen="true"
		sandbox="allow-same-origin allow-scripts allow-popups"
		width="100%"
		height="315"
		frameborder="0" />
</template>

<script setup>
import { computed } from 'vue'
import { NcReferenceWidget } from '@nextcloud/vue/components/NcRichText'

defineOptions({
	name: 'VideoReferenceWidget',

	components: {
		NcReferenceWidget,
	},
})

const props = defineProps({
	richObject: {
		type: Object,
		default: null,
	},

	interactive: {
		type: Boolean,
		default: false,
	},

	accessible: {
		type: Boolean,
		default: false,
	},
})

const reference = computed(() => ({
	richObjectType: 'open-graph',
	richObject: props.richObject,
	accessible: props.accessible,
	openGraphObject: { ...props.richObject },
}))
</script>

<style scoped lang="scss">
/* stylelint-disable-next-line selector-pseudo-class-no-unknown */
:global(.non-interactive-widget a) {
	border: unset !important;
	margin: unset !important;
}
</style>
