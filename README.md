# ArrayLib README

## Overview

The `ArrayLib` PHP library provides utility functions for working with arrays. This README focuses on the `ArrayUtil` class, which includes methods for retrieving values from arrays based on a path and filtering arrays based on specific criteria.

## Table of Contents

- [Class `ArrayUtil`](#class-arrayutil)
  - [Method `getValueFromPath`](#method-getvaluefrompath)
  - [Method `createPathFilter`](#method-createpathfilter)
- [Usage Examples](#usage-examples)
- [Requirements](#requirements)
- [License](#license)

## Class `ArrayUtil`

### Method `getValueFromPath`

```php
public static function getValueFromPath(
    array  $array,
    string $path,
    string $pathSeparator = '.',
    mixed  $default = null,
    array  $filters = null
): mixed
```

#### Description

Retrieves a value from a multidimensional array based on a specified path. The path is defined as a string where each level is separated by the `pathSeparator`. Optionally, filters can be applied to the array at each level of the path.

#### Parameters

- **`$array`** (`array`): The array to search within.
- **`$path`** (`string`): The path to the target property, e.g., `'root.child.grandchild'`.
- **`$pathSeparator`** (`string`): Separator used to delimit path levels. Default is `'.'`.
- **`$default`** (`mixed|null`): Value returned if the property does not exist. Default is `null`.
- **`$filters`** (`array|null`): Filters for each path level, indexed by path level. Example: `['0.children.0.name' => $filterCallable]`.

#### Returns

- **`mixed`**: The value found at the specified path, or the default value if the path is not found.

### Method `createPathFilter`

```php
public static function createPathFilter(
    string     $path,
    mixed      $value,
    Comparison $compare = Comparison::EQ,
    string     $pathSeparator = '.'
): callable
```

#### Description

Creates a filter function that can be used with `array_filter` or other array filtering methods. The filter function checks if the value at the specified path matches a given value according to a specified comparison operator.

#### Parameters

- **`$path`** (`string`): The path to the property in the array to be compared.
- **`$value`** (`mixed`): The value to compare against.
- **`$compare`** (`Comparison`): The comparison operator to use. Defaults to `Comparison::EQ`. Other options include `Comparison::NE`, `Comparison::GE`, `Comparison::GT`, `Comparison::LE`, `Comparison::LT`.
- **`$pathSeparator`** (`string`): Separator used to delimit path levels. Default is `'.'`.

#### Returns

- **`callable`**: A function that can be used as a filter in `array_filter`.

## Usage Examples

### Example 1: Getting a Value from an Array

```php
$array = [
    'user' => [
        'profile' => [
            'name' => 'John Doe',
            'age' => 30
        ]
    ]
];

$value = ArrayUtil::getValueFromPath($array, 'user.profile.name');
echo $value; // Outputs: John Doe
```

### Example 2: Using Filters with `getValueFromPath`

```php
$array = [
    ['name' => 'John', 'age' => 30],
    ['name' => 'Jane', 'age' => 25]
];

$filter = function($item) {
    return $item['age'] > 26;
};

$result = ArrayUtil::getValueFromPath($array, '0', '.', null, [0 => $filter]);
print_r($result); // Outputs: Array ( [name] => John [age] => 30 )
```

### Example 3: Creating and Using a Path Filter

```php
$filter = ArrayUtil::createPathFilter('user.profile.age', 30, Comparison::GE);

$array = [
    ['user' => ['profile' => ['age' => 25]]],
    ['user' => ['profile' => ['age' => 30]]]
];

$filteredArray = array_filter($array, $filter);
print_r($filteredArray); // Outputs: Array ( [1] => Array ( [user] => Array ( [profile] => Array ( [age] => 30 ) ) ) )
```

### More complex examples with combination of getValueFromPath and pathFilters



```php
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

print_r($name); // Outputs: Good Kid

// or retrieve the whole item:

$goodKid = ArrayUtil::getValueFromPath(
    array: $array,
    path: '0.children.0',
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

print_r( $goodKid ); // Outputs: Array ( [id] => 5 [name] => Good Kid )

// or get all kids that are good kids
$goodKids = ArrayUtil::getValueFromPath(
    array: $array,
    path: '0.children',
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

print_r($goodKids); // Outputs: Array ( [0] => Array ( [id] => 5 [name] => Good Kid ) )

```

## Requirements

- PHP 7.4 or higher

## License

This library is licensed under the MIT License. See the [LICENSE](LICENSE) file for details.

---

For more information, please refer to the library's documentation or contact the maintainer.
