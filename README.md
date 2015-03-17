PHP CLI Sudoku Solver
=====================

This is a very basic Sudoku solver written in PHP. Written as a task during an interview ("Your task is to write a solver for today's local news Sudoku...")
It tries to solve the Sudoku in two steps: 
    1. solve all unambiguous symbols until there are none left
    2. solve all ambiguous symbols with recursive backtracking

Unfortunately, difficult Sudokus involving the X-Wing strategy will take *forever*...

Usage
-----

Modify the $puzzle variable for your puzzle (2-dimensional array of values, unknown values denoted with 0).

```bash
./bin/sudokusolver
```
