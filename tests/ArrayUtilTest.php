<?php

namespace ErikKubica\tests;

use ErikKubica\ArrayLib\ArrayUtil;
use ErikKubica\ArrayLib\Comparison;
use PHPUnit\Framework\TestCase;

final class ArrayUtilTest extends TestCase
{
    public function testGetValueFromPath(): void
    {
        $array = [
            [
                'id' => 1,
                'name' => 'John Parent',
                'children' => [
                    [
                        'id' => 2,
                        'name' => 'Freeloader Child',
                    ],
                    [
                        'id' => 3,
                        'name' => 'Older Freeloader Child',
                    ],
                ]
            ],
            [
                'id' => 4,
                'name' => 'John Alone',
                'children' => [
                    [
                        'id' => 5,
                        'name' => 'Good Kid',
                    ],
                    [
                        'id' => 6,
                        'name' => 'Bad Kid',
                    ],
                ]
            ]
        ];

        // Test retrieving a nested value without any filters
        $name = ArrayUtil::getValueFromPath($array, '1.children.1.name');
        $this->assertEquals('Bad Kid', $name);

        // Test retrieving a nested value using a filter at the first level
        $name = ArrayUtil::getValueFromPath(
            array: $array,
            path: '0.children.1.name',
            filters: [
                0 => ArrayUtil::createPathFilter(
                    path: 'name',
                    value: 'John Alone',
                    compare: Comparison::EQ
                )
            ]
        );
        $this->assertEquals('Bad Kid', $name);

        // Test retrieving a nested value with a filter at multiple levels
        $name = ArrayUtil::getValueFromPath(
            array: $array,
            path: '0.children.0.name',
            filters: [
                0 => ArrayUtil::createPathFilter(
                    path: 'name',
                    value: 'John Alone',
                    compare: Comparison::EQ
                ),
                2 => ArrayUtil::createPathFilter(
                    path: 'name',
                    value: 'Good Kid',
                    compare: Comparison::EQ
                )
            ]
        );
        $this->assertEquals('Good Kid', $name);

        // Test retrieving a value that does not exist and returns null
        $name = ArrayUtil::getValueFromPath(
            array: $array,
            path: '0.children.1.typo',
        );
        $this->assertNull($name);

        // Test retrieving a value that does not exist and returns a default value
        $name = ArrayUtil::getValueFromPath(
            array: $array,
            path: '0.children.1.typo',
            default: 'Not Found'
        );
        $this->assertEquals('Not Found', $name);


        $filter = ArrayUtil::createPathFilter('user.profile.age', 21, Comparison::GE);

        $array = [
            ['user' => ['profile' => ['age' => 20]]],
            ['user' => ['profile' => ['age' => 25]]],
            ['user' => ['profile' => ['age' => 30]]]
        ];

        $filteredArray = array_values(array_filter($array, $filter));

        $this->assertCount(2, $filteredArray);
        $this->assertEquals(25, $filteredArray[0]['user']['profile']['age']);
    }

    public function testCreatePathFilter(): void
    {
        $filter = ArrayUtil::createPathFilter('name', 'Erik');

        $this->assertTrue($filter(['name' => 'Erik']));
        $this->assertFalse($filter(['name' => 'Not Erik']));
    }
}
