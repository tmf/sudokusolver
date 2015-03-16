<?php
/**
 * @autor     Tom Forrer <tom.forrer@gmail.com>
 * @copyright Copyright (c) 2012 Tom Forrer (http://github.com/tmf)
 */

/**
 * valid symbols: according to sudoku rules each sub-grid, row and column must contain each symbol only once
 */
$numbers = [1, 2, 3, 4, 5, 6, 7, 8, 9];

/**
 * input puzzle with known values. 0 represent unknown values
 */
$puzzle = [
    [0, 0, 0,   8, 4, 1,   0, 0, 0],
    [0, 5, 0,   0, 0, 9,   0, 7, 0],
    [0, 0, 0,   3, 0, 0,   0, 0, 2],

    [1, 7, 0,   0, 0, 0,   8, 0, 4],
    [2, 0, 0,   0, 0, 0,   0, 0, 7],
    [4, 0, 6,   0, 0, 0,   0, 2, 5],

    [0, 0, 0,   0, 0, 3,   0, 0, 0],
    [0, 3, 0,   9, 0, 0,   0, 6, 0],
    [0, 0, 0,   7, 5, 2,   4, 0, 0],
];

/**
 * solver helper: return the available symbols for a specific cell in a sub-grid of the puzzle
 *
 * @param int $row cell row (starting at 0)
 * @param int $col cell column (starting at 0)
 * @param array $matrix 2d array of the current state
 * @param array $numbers available symbols
 * @return array valid symbols for the requested cell
 */
function availableSquareNumbers($row, $col, $matrix, $numbers)
{
    $rowOffset = 3 * floor($row / 3);
    $colOffset = 3 * floor($col / 3);

    $squareRow1 = array_slice($matrix[$rowOffset], $colOffset, 3);
    $squareRow2 = array_slice($matrix[$rowOffset + 1], $colOffset, 3);
    $squareRow3 = array_slice($matrix[$rowOffset + 2], $colOffset, 3);


    return array_diff($numbers,
        $squareRow1,
        $squareRow2,
        $squareRow3
    );
}

/**
 * solver helper: return the available symbols for a specific cell in the puzzle grid
 *
 * @param int $row cell row (starting at 0)
 * @param array $matrix 2d array of the current state
 * @param array $numbers available symbols
 * @return array valid symbols for the requested cell
 */
function availableRowNumbers($row, $matrix, $numbers)
{
    return array_diff($numbers, $matrix[$row]);
}

/**
 * solver helper: return the available symbols for a specific cell in the puzzle grid
 *

 * @param int $col cell column (starting at 0)
 * @param array $matrix 2d array of the current state
 * @param array $numbers available symbols
 * @return array valid symbols for the requested cell
 */
function availableColNumbers($col, $matrix, $numbers)
{
    $column = [];
    foreach ($matrix as $row) {
        $column[] = $row[$col];
    }

    return array_diff($numbers, $column);
}

/**
 * solver helper: return the available symbols for a specific cell in the puzzle grid
 *
 * @param int $row cell row (starting at 0)
 * @param int $col cell column (starting at 0)
 * @param array $puzzle 2d array of the current state
 * @param array $numbers available symbols
 * @return array valid symbols for the requested cell
 */
function availableNumbersForCell($row, $col, $puzzle, $numbers)
{
    return array_intersect(availableSquareNumbers($row, $col, $puzzle, $numbers),
        availableRowNumbers($row, $puzzle, $numbers),
        availableColNumbers($col, $puzzle, $numbers));
}

/**
 * @param array $puzzle 2d array of the current state
 * @param array $numbers available symbols
 * @return bool|array false if the sudoku is unsolvable
 */
function solveUnique($puzzle, $numbers)
{
    do {
        $foundUnique = false;
        foreach ($puzzle as $row => $line) {
            foreach ($line as $col => $cell) {
                if ($puzzle[$row][$col] == 0) {

                    $availableNumbersForCell = availableNumbersForCell($row, $col, $puzzle, $numbers);

                    if (count($availableNumbersForCell) == 0) {
                        return false;
                    }
                    if (count($availableNumbersForCell) == 1) {

                        $puzzle[$row][$col] = reset($availableNumbersForCell);
                        $foundUnique = true;
                    }
                }
            }
        }

    } while ($foundUnique);

    return $puzzle;
}

/**
 * solve the sudoku puzzle recursively:
 *  - return the solved puzzle, if finished
 *  - fill in unambiguous numbers
 *  - recursively solve ambiguous solutions
 *
 * @param array $puzzle 2d array of the current state
 * @param array $numbers available symbols
 * @return bool|array false if the sudoku is unsolvable
 */
function solve($puzzle, $numbers)
{
    if (finished($puzzle)) {
        return $puzzle;
    }
    $resultUnique = solveUnique($puzzle, $numbers);
    if ($resultUnique !== false) {

        foreach ($puzzle as $row => $line) {
            foreach ($line as $col => $cell) {
                if ($puzzle[$row][$col] == 0) {
                    $availableNumbersForCell = availableNumbersForCell($row, $col, $puzzle, $numbers);
                    if (count($availableNumbersForCell) == 0) {
                        return false;
                    }
                    foreach ($availableNumbersForCell as $value) {
                        $puzzle[$row][$col] = $value;
                        $result = solve($puzzle, $numbers);
                        if(is_array($result)){
                            return $result;
                        }
                    }
                }
            }
        }
    }
    return false;
}

/**
 * helper function to determine if puzzle is complete (assuming that only valid numbers are filled in)
 *
 * @param array $puzzle 2d array of the current state
 * @return bool
 */
function finished($puzzle)
{
    foreach ($puzzle as $line) {
        if (array_search(0, $line) !== false) {
            return false;
        }
    }

    return true;
}

/**
 * helper function to print the puzzle in its current state
 *
 * @param array $puzzle 2d array of the current state
 */
function printSudoku($puzzle)
{
    echo ' ------------------- ' . PHP_EOL;
    foreach ($puzzle as $row => $line) {

        foreach ($line as $col => $cell) {
            if ($col % 3 == 0) {
                echo ' | ';
            }
            echo $cell == 0 ? ' ' : $cell;
            if ($col == count($line) - 1) {
                echo ' | ';
            }
        }
        echo PHP_EOL;
        if (($row + 1) % 3 == 0) {
            echo ' ------------------- ' . PHP_EOL;
        }


    }
    echo PHP_EOL;
}

// print the input puzzle
printSudoku($puzzle);
// print a solution of the input puzzle
printSudoku(solve($puzzle, $numbers));

