OXID eShop composer plugin
==========================

This plugin is used to install OXID eShop and OXID eShop third party integrations (modules, themes).
More information how to install OXID eShop using this plugin can be found `here <http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/getting_started/eshop_installation.html#eshop-installation-via-composer>`__.

Supported types
---------------

Packages are recognised by their type, specified in composer.json file.
Available types are:

- oxideshop - Main shop package is installed into source directory.
- oxideshop-module - Modules, which are installed into source directory. Modules depends on main shop package.
- oxideshop-theme - Themes, which are installed into source directory. Themes depends on main shop package.
- oxideshop-demodata - Demodata package contains demodata.sql and pictures directories.

More information how to create module installable via composer: http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/modules/module_via_composer.html

More information how to create themes installable via composer: http://oxid-eshop-developer-documentation.readthedocs.io/en/latest/themes/theme_via_composer.html

Executing tests
---------------

Run all tests (only dev):
 - $: bin/phpunit tests

Extra type for Composer
------------------------

Control your themes trough composer templates:

target-directory = Theme name
assets-directory = Repository assets path
views-directory  = Repository views path
source-path:     = Theme Basepath (default '/')

"extra": {
    "oxideshop": {
        "target-directory": "mainTheme",
        "assets-directory": "out/mainTheme",
        "views-directory":  "application/views/mainTheme",
        "source-path":      "/"
    }
}

Changelog | Fork - solutionDrive GmbH
-------------------------------------

Description:
We've forked this repository to work with themes over composer installer templates.

1.0.0
- Changed ThemeInstaller::PATH_TO_THEMES value to 'application/views' to match old structure
- Changed PackagesInstaller::getShopSourcePath() to PackagesInstaller::getShopRootPath() to work directly with /application and /out - folders
    - Modified the tests for those changes

1.0.1
- Added views-directory to composer file
- Added overriding ask case, to handle force updates

1.0.2
- Removed overriding case to force update every install/update
- onUpdate - Theme will be installed again
