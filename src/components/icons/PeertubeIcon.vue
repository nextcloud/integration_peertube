<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: AGPL-3.0-or-later
-->

<template>
	<span
		:aria-hidden="!title"
		:aria-label="title"
		class="material-design-icon peertube-icon"
		role="img"
		v-bind="$attrs"
		@click="$emit('click', $event)">
		<svg
			:fill="fillColor"
			:width="size"
			:height="size"
			enable-background="new 0 0 1000 1000"
			version="1.1"
			viewBox="0 0 1000 1000"
			xml:space="preserve"
			xmlns="http://www.w3.org/2000/svg">
			<path d="M 500 274C 500 274 161 500 161 500C 161 500 161 48 161 48C 161 48 500 274 500 274M 500 274C 500 274 839 500 839 500C 839 500 500 726 500 726C 500 726 500 274 500 274C 500 274 500 274 500 274M 500 726C 500 726 161 952 161 952C 161 952 161 500 161 500C 161 500 500 726 500 726" />
		</svg>
	</span>
</template>

<script>
export default {
	name: 'PeertubeIcon',
	props: {
		title: {
			type: String,
			default: '',
		},

		fillColor: {
			type: String,
			default: 'currentColor',
		},

		size: {
			type: Number,
			default: 24,
		},
	},

	emits: ['click'],
}
</script>
