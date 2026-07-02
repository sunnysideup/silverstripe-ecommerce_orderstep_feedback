# Upgrade Guide to SS6

This document outlines the changes required to upgrade your project to be compatible with Silverstripe CMS 6.

## New Requirements

Your `composer.json` must be updated to require the new major versions of the core Silverstripe modules.

-   **`silverstripe/recipe-cms`**: Update constraint to `^6.0`
-   **`sunnysideup/ecommerce`**: Update constraint to `^33.0`

## API Changes

### ⚠️ BREAKING CHANGE: Database Administration

The deprecated `SilverStripe\ORM\DatabaseAdmin` has been removed.

-   Replace any YAML configurations referencing `SilverStripe\ORM\DatabaseAdmin:` with `SilverStripe\Dev\DbBuild:`.

### PHP `Override` Attribute

The codebase has been updated to use the native PHP `#[Override]` attribute for methods that override a parent implementation (e.g., `getCMSFields`, `doStep`). This is a code-style improvement and does not require changes in your project code unless you are extending these classes and overriding the same methods.
