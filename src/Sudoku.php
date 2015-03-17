<?php
/**
 * @autor     Tom Forrer <tom.forrer@gmail.com>
 * @copyright Copyright (c) 2015 Tom Forrer (http://github.com/tmf)
 */

namespace Tmf\Sudoku;

/**
 * Class Sudoku
 *
 * @package Tmf\Sudoku
 */
class Sudoku
{
    const SYMBOL_UNKNOWN = 0;

    /**
     * @var array 2d array (with the size of count($symbols) x count($symbols)) of the current state of the Sudoku
     *      puzzle (filled with values from the $symbols array)
     */
    protected $grid;

    /**
     * @var array
     */
    protected $symbols;

    /**
     * @param array      $knownGrid
     * @param array|null $symbols
     */
    public function __construct(array $knownGrid, $symbols = null)
    {
        $this->grid = $knownGrid;

        if (is_null($symbols)) {

            // flatten grid
            $gridSymbols = [];
            foreach ($this->grid as $line) {
                $gridSymbols = array_merge($gridSymbols, $line);
            }

            // assume each symbol is at least present once, if not otherwise specified
            $this->symbols = array_unique($gridSymbols);
        } else {
            $this->symbols = $symbols;
        }
    }

    public function render()
    {
        echo ' ------------------- ' . PHP_EOL;
        foreach ($this->grid as $row => $line) {

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

    public function getSubGrid($row, $column)
    {
        $subGridWidth = $this->getSubGridWidth();
        $rowOffset = $subGridWidth * floor($row / $subGridWidth);
        $colOffset = $subGridWidth * floor($column / $subGridWidth);

        $squareRow1 = array_slice($this->getGrid()[$rowOffset], $colOffset, $subGridWidth);
        $squareRow2 = array_slice($this->getGrid()[$rowOffset + 1], $colOffset, $subGridWidth);
        $squareRow3 = array_slice($this->getGrid()[$rowOffset + 2], $colOffset, $subGridWidth);

        return array_merge($squareRow1, $squareRow2, $squareRow3);
    }

    public function isComplete()
    {
        foreach ($this->getGrid() as $line) {
            if (array_search(static::SYMBOL_UNKNOWN, $line) !== false) {
                return false;
            }
        }

        return true;
    }

    public function isSymbolKnown($row, $column)
    {
        return $this->getGrid()[$row][$column] !== static::SYMBOL_UNKNOWN;
    }

    public function setSymbol($row, $column, $symbol)
    {
        $this->grid[$row][$column] = $symbol;
    }

    /**
     * @return array
     */
    public function getGrid()
    {
        return $this->grid;
    }

    /**
     * @param array $grid
     */
    public function setGrid($grid)
    {
        $this->grid = $grid;
    }


    public function getGridWidth()
    {
        return count($this->getGrid());
    }

    public function getGridHeight()
    {
        return $this->getGridWidth();
    }

    public function getSubGridWidth()
    {
        return intval(floor(sqrt($this->getGridWidth())));
    }

    public function getSubGridHeight()
    {
        return intval(floor(sqrt($this->getGridHeight())));
    }

    /**
     * @return array
     */
    public function getSymbols()
    {
        return $this->symbols;
    }
}