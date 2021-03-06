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

namespace OxidEsales\ComposerPlugin\Tests\Unit\Utilities;

use OxidEsales\ComposerPlugin\Utilities\VfsFileStructureOperator;

/**
 * Class FileStructureOperatorTest.
 */
class VfsFileStructureOperatorTest extends \PHPUnit_Framework_TestCase
{
    public function testReturnEmptyListWhenNoInputIsProvided()
    {
        $this->assertSame([], VfsFileStructureOperator::nest());
    }

    public function testReturnEmptyListWhenNullInputIsProvided()
    {
        $this->assertSame([], VfsFileStructureOperator::nest(null));
    }

    public function testReturnEmptyListWhenEmptyInputArrayIsProvided()
    {
        $this->assertSame([], VfsFileStructureOperator::nest([]));
    }

    public function testThrowAnExceptionIfInputIsNotAnArray()
    {
        $this->setExpectedException(\InvalidArgumentException::class, "Given input argument must be an array.");
        VfsFileStructureOperator::nest(1);
    }

    public function testReturnArrayAsIsWhenOnlyItemIsPresent()
    {
        $this->assertSame(['abc'], VfsFileStructureOperator::nest(['abc']));
    }

    public function testReturnArrayAsIsWhenMultipleItemsArePresent()
    {
        $this->assertSame(['abc', 'def'], VfsFileStructureOperator::nest(['abc', 'def']));
    }

    public function testReturnArrayAsIsWhenOnlyOneFileIsPresent()
    {
        $input = [
            'file' => 'Contents'
        ];

        $this->assertSame($input, VfsFileStructureOperator::nest($input));
    }

    public function testReturnArrayAsIsWhenOnlyOneFileIsPresentIgnoringSpacesAtBeginningAndEnd()
    {
        $input = [
            '  file ' => 'Contents'
        ];

        $expectedOutput = [
            'file' => 'Contents'
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnArrayAsIsWhenMultipleFilesArePresent()
    {
        $input = [
            'file' => 'Contents',
            'second_file' => 'Second Contents'
        ];

        $this->assertSame($input, VfsFileStructureOperator::nest($input));
    }

    public function testReturnArrayWithSingleItemWhenSameMultipleFilesArePresentLastOneBeingAsOverrider()
    {
        $input = [
            'file' => 'Contents',
            '  file  ' => 'Second Contents'
        ];

        $expectedOutput = [
            'file' => 'Second Contents',
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenSingleItemContainsMultiLevelPath()
    {
        $input = [
            'directory/file' => 'contents'
        ];

        $expectedOutput = [
            'directory' => [
                'file' => 'contents'
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenLastItemContainsMultiLevelPath()
    {
        $input = [
            'directory/fake_file' => 'contents',
            'directory/fake_file/real_file' => 'real contents',
        ];

        $expectedOutput = [
            'directory' => [
                'fake_file' => [
                    'real_file' => 'real contents'
                ]
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenSingleItemContainsMultiLevelPathWithTrailingSlash()
    {
        $input = [
            'directory/sub/' => 'contents'
        ];

        $expectedOutput = [
            'directory' => [
                'sub' => []
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenMultipleItemsContainsMultiLevelPathWithSameBase()
    {
        $input = [
            'directory/file' => 'contents',
            'directory/second_file' => 'second contents',
        ];

        $expectedOutput = [
            'directory' => [
                'file' => 'contents',
                'second_file' => 'second contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenMultipleItemsContainsMultiLevelPathWithSameBaseButWithBreakPointInTheMiddle()
    {
        $input = [
            'directory/file' => 'contents',
            'directory_a/file' => 'a contents',
            'directory/second_file' => 'second contents',
        ];

        $expectedOutput = [
            'directory' => [
                'file' => 'contents',
                'second_file' => 'second contents',
            ],
            'directory_a' => [
                'file' => 'a contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenSingleItemContainsMultiLevelPathMoreThenOneLevelDeep()
    {
        $input = [
            'directory/another_directory/file' => 'contents'
        ];

        $expectedOutput = [
            'directory' => [
                'another_directory' => [
                    'file' => 'contents'
                ]
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenMultipleItemsContainsMultiLevelPathWithDifferentBase()
    {
        $input = [
            'directory/file' => 'contents',
            'second_directory/second_file' => 'second contents',
        ];

        $expectedOutput = [
            'directory' => [
                'file' => 'contents',
            ],
            'second_directory' => [
                'second_file' => 'second contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }

    public function testReturnNestedArrayWhenComplexCasePresented()
    {
        $input = [
            'file' => 'contents',
            'directory/file' => 'second contents',
            'directory_a/directory_b/directory_c/file' => 'third contents',
            'directory/file_b' => 'b contents',
            ' file_c  ' => 'c contents',
            ' file_c   ' => 'c override contents',
            'fake_file' => 'fake contents',
            'fake_file/real_file' => 'real contents',
        ];

        $expectedOutput = [
            'file' => 'contents',
            'directory' => [
                'file' => 'second contents',
                'file_b' => 'b contents',
            ],
            'directory_a' => [
                'directory_b' => [
                    'directory_c' => [
                        'file' => 'third contents',
                    ]
                ]
            ],
            'file_c' => 'c override contents',
            'fake_file' => [
                'real_file' => 'real contents',
            ]
        ];

        $this->assertSame($expectedOutput, VfsFileStructureOperator::nest($input));
    }
}
