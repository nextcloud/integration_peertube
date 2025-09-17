/**
 * SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
 * SPDX-License-Identifier: AGPL-3.0-or-later
 */

let mytimer = 0

/**
 * Creates a delayed version of the provided callback function.
 *
 * @param {Function} callback - The function to be executed after the specified delay.
 * @param {number} ms - The number of milliseconds to wait before executing the callback function. Defaults to 0 if not provided.
 *
 * @return {Function} A new function that, when called, will execute the original callback function after the specified delay.
 */
export function delay(callback, ms) {
	return function() {
		const context = this
		const args = arguments
		clearTimeout(mytimer)
		mytimer = setTimeout(function() {
			callback.apply(context, args)
		}, ms || 0)
	}
}
