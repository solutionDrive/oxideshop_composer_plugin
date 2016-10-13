<?php
/**
 * This file is part of OXID eShop Composer plugin.
 *
 * OXID eShop Composer plugin is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * OXID eShop Composer plugin is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with OXID eShop Composer plugin.  If not, see <http://www.gnu.org/licenses/>.
 *
 * @link      http://www.oxid-esales.com
 * @copyright (C) OXID eSales AG 2003-2016
 * @version   OXID eShop Composer plugin
 */

namespace OxidEsales\ComposerPlugin\Installer;

use Composer\Package\PackageInterface;

/**
 * @inheritdoc
 */
class ThemeInstaller extends AbstractInstaller
{
    const METADATA_FILE_NAME = 'theme.php';
    const PATH_TO_THEMES = "application/views";

    /**
     * Checks if theme already installed
     *
     * @return bool
     */
    public function isInstalled()
    {
        return file_exists($this->formThemeTargetPath().'/'.static::METADATA_FILE_NAME);
    }

    /**
     * Copies module files to shop directory.
     *
     * @param string $packagePath
     */
    public function install($packagePath)
    {
        $package = $this->getPackage();
    
        $this->getIO()->write("Installing {$package->getPrettyName()} package");
        
        if ($this->isInstalled()) {
            if ($this->getIO()->ask("<info>Override existing Theme ({$package->getPrettyName()})? [y, n (default: y)]</info>", ' ?') == 'n') {
                $this->getIO()->write("- Overriding of package {$package->getPrettyName()} was successfully skipped!");
                // Skip override
                return;
            }
        }
            
        // Install Views
        $this->getIO()->write(" - Installing views of {$package->getPrettyName()}");
        $this->installViews($packagePath);

        // Install Assets
        $this->getIO()->write(" - Installing assets of {$package->getPrettyName()}");
        $this->installAssets($packagePath);
    }

    /**
     * Copies module files to shop directory.
     *
     * @param string $packagePath
     */
    public function update($packagePath)
    {
    }

    /**
     * @return string
     */
    protected function formThemeTargetPath()
    {
        $package = $this->getPackage();
        $themeDirectoryName = $this->formThemeDirectoryName($package);
        return "{$this->getRootDirectory()}/" . static::PATH_TO_THEMES . "/$themeDirectoryName";
    }
    
    /**
     * @param $packagePath
     */
    protected function installViews($packagePath)
    {
        $iterator = $this->getDirectoriesToSkipIteratorBuilder()->build(
            $packagePath, [
                $this->formAssetsDirectoryName()
            ]
        );
    
        $viewsDirectory = $this->formViewsDirectoryName();
        $source = $packagePath . '/' . $viewsDirectory;
    
        $fileSystem = $this->getFileSystem();
        if (file_exists($source)) {
            $fileSystem->mirror($source, $this->formThemeTargetPath(), $iterator, ['override' => true]);
        }
    }
    
    /**
     * @param $packagePath
     */
    protected function installAssets($packagePath)
    {
        $package = $this->getPackage();
        $target = $this->getRootDirectory() . '/out/' . $this->formThemeDirectoryName($package);

        $assetsDirectory = $this->formAssetsDirectoryName();
        $source = $packagePath . '/' . $assetsDirectory;

        $fileSystem = $this->getFileSystem();
        if (file_exists($source)) {
            $fileSystem->mirror($source, $target, null, ['override' => true]);
        }
    }
    
    /**
     * Forms the Theme directory name,
     * specially from the composer.json of vendor/theme
     *
     * If not defined in vendor/theme this will return the specific package name
     * example return value: "name": "company_name/__the_specific_package_name__"
     *
     * @param $package
     *
     * @return string
     */
    protected function formThemeDirectoryName($package)
    {
        $themePath = $this->getExtraParameterValueByKey(static::EXTRA_PARAMETER_KEY_TARGET);
        if (is_null($themePath)) {
            $themePath = explode('/', $package->getName())[1];
        }
        return $themePath;
    }
    
    /**
     * Forms the Path for specific views folder,
     * specially from the composer.json of vender/theme
     *
     * @return string
     */
    protected function formViewsDirectoryName()
    {
        $assetsDirectory = $this->getExtraParameterValueByKey(static::EXTRA_PARAMETER_KEY_VIEWS);
        if (is_null($assetsDirectory)) {
            $assetsDirectory = 'views';
        }
        return $assetsDirectory;
    }
    
    /**
     * Forms the Path for specific asset folder,
     * specially from the composer.json of vender/theme
     *
     * @return string
     */
    protected function formAssetsDirectoryName()
    {
        $assetsDirectory = $this->getExtraParameterValueByKey(static::EXTRA_PARAMETER_KEY_ASSETS);
        if (is_null($assetsDirectory)) {
            $assetsDirectory = 'out';
        }
        return $assetsDirectory;
    }

    /**
     * @return DirectoriesSkipIteratorBuilder
     */
    protected function getDirectoriesToSkipIteratorBuilder()
    {
        return new DirectoriesSkipIteratorBuilder();
    }
}
