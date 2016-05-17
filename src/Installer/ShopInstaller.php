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
use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use Symfony\Component\Filesystem\Filesystem;

/**
 * @inheritdoc
 */
class ShopInstaller extends AbstractInstaller
{
    /** @var array Directories which shouldn't be copied. */
    private $directoriesToSkip = [
        'Application/Component',
        'Application/Controller',
        'Application/Model',
        'Core'
    ];

    /**
     * @param PackageInterface $package
     *
     * @return bool
     */
    public function isInstalled(PackageInterface $package)
    {
        return file_exists($this->getRootDirectory() .'/index.php');
    }

    /**
     * Copies all shop files from vendors to source directory.
     *
     * @param PackageInterface $package
     * @param string           $packagePath
     */
    public function install(PackageInterface $package, $packagePath)
    {
        $this->getIO()->write("Installing shop package");

        $packagePath = rtrim($packagePath, '/') . '/source';
        $root = $this->getRootDirectory();

        $fileSystem = $this->getFileSystem();
        $fileSystem->mirror($packagePath, $root, $this->formIterator($packagePath, $this->directoriesToSkip));

        if (file_exists($root.'/config.inc.php.dist')) {
            $fileSystem->copy($root.'/config.inc.php.dist', $root.'/config.inc.php');
        }
    }

    /**
     * Overwrites files in core directories.
     *
     * @param PackageInterface $package
     * @param string           $packagePath
     */
    public function update(PackageInterface $package, $packagePath)
    {
    }

    /**
     * @param string $packagePath
     * @param array  $directoriesToSkip
     *
     * @return RecursiveIteratorIterator
     */
    private function formIterator($packagePath, $directoriesToSkip)
    {
        foreach ($directoriesToSkip as &$directory) {
            $directory = "$packagePath/$directory";
        }
        $directoryIterator = new RecursiveDirectoryIterator($packagePath, FilesystemIterator::SKIP_DOTS);
        $directoryFilter = new DirectoryRecursiveFilterIterator($directoryIterator, $directoriesToSkip);
        return new RecursiveIteratorIterator($directoryFilter, RecursiveIteratorIterator::SELF_FIRST);
    }
}
