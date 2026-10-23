<!--
  - SPDX-FileCopyrightText: 2020 Nextcloud GmbH and Nextcloud contributors
  - SPDX-License-Identifier: CC0-1.0
-->
# Change Log
All notable changes to this project will be documented in this file.

The format is based on [Keep a Changelog](http://keepachangelog.com/)
and this project adheres to [Semantic Versioning](http://semver.org/).

## [Unreleased]

### Removed

- Nextcloud 32 and PHP 8.1 support

## 2.1.2 - 2026-08-18

### Added

- Added support for Nextcloud 35

### Changed

- Remove non-existent branches from the npm audit workflow
- Import from @nextcloud/vue/functions/registerReference
- Consider package-lock.json and composer.lock as binary in Git
- Update dependencies

## 2.1.1 - 2026-04-16

### Added

- Added support for Nextcloud 34

### Changed

- Modernize the settings
- Update the workflows and add the npm audit fix workflow
- Update dependencies

### Fixed

- Remove trailing slashes from the PeerTube instance URLs
- Display the loading spinner next to the instance list label in the admin settings
- Fall back to the plain link preview when PeerTube answers a video without its ID, name or embed path

## 2.1.0 - 2025-10-06

### Added

- Added support for Nextcloud 33
- Migrate to Vue 3
- Lighter non-interactive reference widget by default

### Changed

- Use lazy admin config values
- Update dependencies

## 2.0.0 - 2025-09-01

### Changed

- Add the psalm-matrix check

### Removed

- Nextcloud 28 to 31 support

## 1.3.0 - 2025-08-29

### Added

- Added support for Nextcloud 32
- Mark the search providers as external sources

### Changed

- Update the CSP nonce
- Update dependencies

## 1.2.0 - 2025-01-09

### Added

- Added support for Nextcloud 31

### Changed

- Update dependencies

## 1.1.0 - 2024-09-25

### Changed

- Update dependencies

### Removed

- Nextcloud 26 and 27 support

### Fixed

- Correct the error handling and the work with the empty PeerTube instance list

## 1.0.4 - 2024-07-25

### Added

- Added support for Nextcloud 30

### Changed

- Update dependencies & translations

## 1.0.3 - 2024-03-11

### Added

- Added support for Nextcloud 29

### Changed

- Update dependencies & translations

## 1.0.2 - 2023-11-02

### Changed

- Update dependencies & translations

## 1.0.1 - 2023-07-06

### Added

- Added support for Nextcloud 28

### Changed

- Disable unified search by default

### Fixed

- Fix toggling the search provider, always allow it when used in the smart picker

## 1.0.0 - 2023-03-06

### Added

- Initial release for Nextcloud 26 and 27
