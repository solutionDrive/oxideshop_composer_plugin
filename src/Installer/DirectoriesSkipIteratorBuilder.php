<?php
/**
 * #PHPHEADER_OXID_LICENSE_INFORMATION#
 */

namespace OxidEsales\ComposerPlugin\Installer;

use FilesystemIterator;
use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;

class DirectoriesSkipIteratorBuilder
{
    
    /**
     * @var array
     */
    private $_forceSkipDirectories = [
        '.git'
    ];
    
    /**
     * @param string $packagePath
     * @param array  $directoriesToSkip
     *
     * @return RecursiveIteratorIterator
     */
    public function build($packagePath, $directoriesToSkip)
    {
        // Add force skipping directories
        $directoriesToSkip += $this->getForceSkipDirectories();
        
        foreach ($directoriesToSkip as &$directory) {
            $directory = "$packagePath/$directory";
        }
        $directoryIterator = new RecursiveDirectoryIterator($packagePath, FilesystemIterator::SKIP_DOTS);
        $directoryFilter = new DirectoryRecursiveFilterIterator($directoryIterator, $directoriesToSkip);
        return new RecursiveIteratorIterator($directoryFilter, RecursiveIteratorIterator::SELF_FIRST);
    }
    
    /**
     * @return array
     */
    public function getForceSkipDirectories()
    {
        return $this->_forceSkipDirectories;
    }
    
    /**
     * @param array $forceSkipDirectories
     */
    public function setForceSkipDirectories(array $forceSkipDirectories)
    {
        $merged = array_merge($this->_forceSkipDirectories, $forceSkipDirectories);
        $this->_forceSkipDirectories = $merged;
    }
}
