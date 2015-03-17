<?php
/**
 * @autor     Tom Forrer <tom.forrer@gmail.com>
 * @copyright Copyright (c) 2015 Tom Forrer (http://github.com/tmf)
 */

namespace Tmf\Sudoku;

/**
 * Class SudokuSolver
 *
 * @package Tmf\Sudoku
 */
class SudokuSolver
{
    /**
     * @var Sudoku $sudoku
     */
    protected $sudoku;

    /**
     * @param Sudoku $sudoku
     */
    public function __construct(Sudoku $sudoku)
    {
        $this->sudoku = $sudoku;
    }

    public function solve($depth=0)
    {
        $this->solveUnambiguousValues();

        $currentGrid = $this->getSudoku()->getGrid();
        $availableSymbolsRef = [];
        for ($row = 0; $row < $this->getSudoku()->getGridWidth(); $row++) {
            for ($column = 0; $column <  $this->getSudoku()->getGridHeight(); $column++) {
                if (!$this->getSudoku()->isSymbolKnown($row, $column)) {
                    $availableSymbols = $this->availableSymbolsInCell($row, $column);

                    if(count($availableSymbols) == 0){
                        return;
                    }

                    array_push($availableSymbolsRef, ['row' => $row, 'column' => $column, 'available' => $availableSymbols]);
                }
            }
        }

        usort($availableSymbolsRef, function($a, $b){
            $countA = count($a['available']);
            $countB = count($b['available']);
            if($countA == $countB){
                return 0;
            }

            return ($countA < $countB) ? -1 : 1;
        });
        foreach($availableSymbolsRef as $ref){
            foreach($ref['available'] as $symbol){
                $this->getSudoku()->setSymbol($ref['row'], $ref['column'], $symbol);

                $this->solve($depth++);
                if($this->getSudoku()->isComplete()){
                    return;
                }else{
                    $this->getSudoku()->setGrid($currentGrid);
                }
            }
        }
    }

    /**
     *
     */
    public function solveUnambiguousValues()
    {
        do {
            $foundUnambiguousSymbols = false;
            for ($row = 0; $row < $this->getSudoku()->getGridWidth(); $row++) {
                for ($column = 0; $column < $this->getSudoku()->getGridHeight(); $column++) {
                    if(!$this->getSudoku()->isSymbolKnown($row, $column)){
                        $availableSymbols = $this->availableSymbolsInCell($row, $column);
                        if(count($availableSymbols)==1){
                            $this->getSudoku()->setSymbol($row, $column, reset($availableSymbols));
                            $foundUnambiguousSymbols = true;
                        }
                    }
                }
            }


            for ($rowOffset = 0; $rowOffset < $this->getSudoku()->getGridWidth(); $rowOffset += $this->getSudoku()->getSubGridWidth()) {
                for ($columnOffset = 0; $columnOffset < $this->getSudoku()->getGridHeight(); $columnOffset += $this->getSudoku()->getSubGridHeight()) {
                    $foundUnambiguousSymbols = $foundUnambiguousSymbols || $this->solveUnambiguousSymbolsInSubGrid($rowOffset, $columnOffset);
                }
            }
        } while ($foundUnambiguousSymbols);
    }

    /**
     * @param $row
     * @param $column
     * @return bool
     */
    protected function solveUnambiguousSymbolsInSubGrid($row, $column)
    {
        $foundUnambiguousSymbols = false;
        $availableSymbolsPerSubGridCell = [];

        for ($rowOffset = $row; $rowOffset < $row + $this->getSudoku()->getSubGridWidth(); $rowOffset++) {
            for ($columnOffset = $column; $columnOffset < $column + $this->getSudoku()->getSubGridHeight(); $columnOffset++) {
                if (!$this->getSudoku()->isSymbolKnown($rowOffset, $columnOffset)) {
                    array_push($availableSymbolsPerSubGridCell, ['row' => $rowOffset, 'column' => $columnOffset, 'available' => $this->availableSymbolsInCell($rowOffset, $columnOffset)]);
                }
            }
        }

        for ($i = 0; $i < count($availableSymbolsPerSubGridCell); $i++) {
            $symbols = $availableSymbolsPerSubGridCell[$i]['available'];
            for ($j = 0; $j < count($availableSymbolsPerSubGridCell); $j++) {
                if ($i != $j) {
                    $symbols = array_diff($symbols, $availableSymbolsPerSubGridCell[$j]['available']);
                }
                if(count($symbols) == 0){
                    break;
                }
            }

            if (count($symbols) == 1) {
                $this->getSudoku()->setSymbol($availableSymbolsPerSubGridCell[$i]['row'], $availableSymbolsPerSubGridCell[$i]['column'], reset($symbols));
                $foundUnambiguousSymbols = true;
            }

        }

        return $foundUnambiguousSymbols;
    }

    /**
     * @param $row
     * @return array
     */
    protected function availableSymbolsInRow($row)
    {
        return array_diff($this->getSudoku()->getSymbols(), $this->getSudoku()->getGrid()[$row]);
    }

    /**
     * @param $column
     * @return array
     */
    protected function availableSymbolsInColumn($column)
    {
        return array_diff($this->getSudoku()->getSymbols(), array_column($this->getSudoku()->getGrid(), $column));
    }

    /**
     * @param $row
     * @param $column
     * @return array
     */
    protected function availableSymbolsInSubGrid($row, $column)
    {
        return array_diff($this->getSudoku()->getSymbols(), $this->getSudoku()->getSubGrid($row, $column));
    }

    /**
     * @param $row
     * @param $column
     * @return array
     */
    public function availableSymbolsInCell($row, $column)
    {
        return array_intersect(
            $this->availableSymbolsInSubGrid($row, $column),
            $this->availableSymbolsInRow($row),
            $this->availableSymbolsInColumn($column)
        );
    }

    /**
     * @return Sudoku
     */
    public function getSudoku()
    {
        return $this->sudoku;
    }


}