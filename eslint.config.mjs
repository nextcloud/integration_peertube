/**
 * SPDX-FileCopyrightText: 2025 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

import { recommended } from '@nextcloud/eslint-config'

export default [
	...recommended,

	{
		name: 'peertube/rule-overrides',
		rules: {
			'no-console': ['warn', { allow: ['debug', 'warn', 'error'] }],
		},
		files: ['src/**/*.vue', 'src/**/*.js'],
	},
]
