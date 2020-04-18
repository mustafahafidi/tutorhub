<?php

namespace Utils;

class Arrays
{
    /**
     * Returns the values from a single column in the input array.
     * The ArrayAccess interface is supported.
     *
     * @param array $input      The multidimensional array.
     * @param mixed $column_key The column to return.
     * @return array Column of values.
     */
    public static function column(array $input, $column_key) : array
    {
        $res = [];
        foreach ($input as $x)
            $res[] = $x[$column_key];
        return $res;
    }

    /**
     * Applies a function to the elements of the input array.
     *
     * @param callable $callback Function to apply.
     * @param array    $input    Input array.
     * @return array Return values of the function.
     */
    public static function map(callable $callback, array $input) : array
    {
        $res = [];
        foreach ($input as $x)
            $res[] = $callback($x);
        return $res;
    }
}
