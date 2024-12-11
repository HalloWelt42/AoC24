<?php

namespace App\Controller;

use Generator;
use RuntimeException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aoc24')]
class AocController extends AbstractController
{
    // Welcome to Advent of Code 2024 - this first methode is only for testing purposes
    #[Route('/welcome', name: 'welcome', methods: ['GET'])]
    public function index(): Response
    {
        return new Response('Welcome to Advent of Code 2024' . PHP_EOL);
    }

    # day 1
    #[Route('/day1/a/{file_name}', name: 'historian_hysteria_a', methods: ['GET'])]
    public function historianHysteria_a(string $file_name): Response
    {
        // reads the entire contents of a file and returns two lists of integers
        [$result_a, $result_b] = $this->historianHysteria_getAB($file_name);

        // find the smallest number a and b, calculate the distance between the two numbers and remove it from the array
        $result = 0;
        while (count($result_a) > 0 && count($result_b) > 0) {
            // Find the smallest element in both arrays
            $min_a = min($result_a);
            $min_b = min($result_b);

            // Add the difference of the smallest element to the result array
            $result += abs($min_a - $min_b);

            // Remove only the first occurrence of the smallest value from the respective arrays
            $index_a = array_search($min_a, $result_a);
            $index_b = array_search($min_b, $result_b);

            // Remove the elements with array_splice
            array_splice($result_a, $index_a, 1);
            array_splice($result_b, $index_b, 1);
        }

        return new Response(PHP_EOL . $result . PHP_EOL);
    }

    #[Route('/day1/b/{file_name}', name: 'historian_hysteria_b', methods: ['GET'])]
    public function historianHysteria_b(string $file_name): Response
    {
        // Read number pairs from the file
        [$result_a, $result_b] = $this->historianHysteria_getAB($file_name);
        $result = 0;
        $count = count($result_a);
        // Count the frequency of the numbers in $result_b
        $b = array_count_values($result_b);
        for ($i = 0; $i < $count; $i++) {
            $a = $result_a[$i];
            // If the number in $result_a also occurs in $result_b, add the product to $result
            if (key_exists($a, $b)) {
                $result += $a * ($b[$a]) ?? 0;
            }
        }
        return new Response(PHP_EOL . $result . PHP_EOL);
    }

    # day 2
    #[Route('/day2/a/{file_name}', name: 'Red_Nosed_Reports_a', methods: ['GET'])]
    public function redNosedReports_a(string $file_name): Response
    {
        $result = 0;
        foreach ($this->readDataLine(2, $file_name) as $line) {
            $vals = explode(' ', $line);
            $valid = $this->redNosedReports_isSave($vals);
            if ($valid === true) {
                $result++;
            }
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    #[Route('/day2/b/{file_name}', name: 'Red_Nosed_Reports_b', methods: ['GET'])]
    public function redNosedReports_b(string $file_name): Response
    {
        $result = 0;
        foreach ($this->readDataLine(2, $file_name) as $line) {
            $vals = explode(' ', $line);
            $valid = $this->redNosedReports_isSave($vals);
            if ($valid === true) {
                $result++;
            } else {
                for ($i = 0; $i < count($vals); $i++) {
                    $vals_copy = [];
                    for ($j = 0; $j < count($vals); $j++) {
                        if ($i !== $j) {
                            $vals_copy[] = $vals[$j];
                        }
                    }
                    $valid_chk = $this->redNosedReports_isSave($vals_copy);
                    if ($valid_chk === true) {
                        $result++;
                        break;
                    }
                }
            }
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    # day 3
    #[Route('/day3/a/{file_name}', name: 'mull_it_over_a', methods: ['GET'])]
    public function mullItOver_a(string $file_name): Response
    {
        $result = 0;
        $regex = '/mul\(\d{1,3},\d{1,3}\)/';
        preg_match_all($regex, $this->getAllData(3, $file_name), $matches);
        foreach ($matches[0] as $match) {
            $numbers = explode(',', substr($match, 4, -1));
            $result += $numbers[0] * $numbers[1];
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    #[Route('/day3/b/{file_name}', name: 'mull_it_over_b', methods: ['GET'])]
    public function mullItOver_b(string $file_name): Response
    {
        $result = 0;
        $regex = '/mul\((\d{1,3}),(\d{1,3})\)|don\'t\(\)|do\(\)/';
        preg_match_all($regex, $this->getAllData(3, $file_name), $matches);
        $calc = true;
        foreach ($matches[0] as $match) {
            if (
                str_contains($match, "don't()")
                || str_contains($match, 'do()')
            ) {
                if (str_contains($match, "don't()")) {
                    $calc = false;
                }
                if (str_contains($match, 'do()')) {
                    $calc = true;
                }
                continue;
            }
            if (!$calc) {
                continue;
            }
            $numbers = explode(',', substr($match, 4, -1));
            $result += $numbers[0] * $numbers[1];
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    # day 4
    #[Route('/day4/a/{file_name}', name: 'ceres_search_a', methods: ['GET'])]
    public function ceresSearch_a(string $file_name): Response
    {
        $m = explode(PHP_EOL, $this->getAllData(4, $file_name));
        $search = 'XMAS';
        $result = 0;
        $line_count = count($m);

        // Define directions (dx, dy)
        $directions = [
            [0, 1],   // Horizontal forward
            [0, -1],  // Horizontal backward
            [1, 0],   // Vertical downward
            [-1, 0],  // Vertical upward
            [1, 1],   // Diagonal forward (bottom-right)
            [-1, -1], // Diagonal backward (top-left)
            [1, -1],  // Diagonal forward (bottom-left)
            [-1, 1],  // Diagonal backward (top-right)
        ];

        // Iterate over the entire grid
        for ($y = 0; $y < $line_count; $y++) {
            for ($x = 0; $x < $line_count; $x++) {
                foreach ($directions as [$dy, $dx]) {
                    $matches = function ($y, $x, $dy, $dx) use ($m, $search, $line_count) {
                        for ($i = 0; $i < strlen($search); $i++) {
                            $ny = $y + $i * $dy;
                            $nx = $x + $i * $dx;
                            if ($ny < 0 || $ny >= $line_count || $nx < 0 || $nx >= $line_count || $m[$ny][$nx] !== $search[$i]) {
                                return false;
                            }
                        }
                        return true;
                    };

                    // Check if the pattern matches in the current direction
                    if ($matches($y, $x, $dy, $dx)) {
                        $result++;
                    }
                }
            }
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    #[Route('/day4/b/{file_name}', name: 'ceres_search_b', methods: ['GET'])]
    public function ceresSearch_b(string $file_name): Response
    {
        $result = 0;
        $search = ['MSAMS', 'MMASS', 'SMASM', 'SSAMM'];
        $data = $this->getAllData(4, $file_name);
        foreach ($this->ceresSearch($data) as $matrix) {
            $result += in_array($matrix, $search);
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    # day 5
    #[Route('/day5/a/{file_name}', name: 'print_queue', methods: ['GET'])]
    public function printQueue_a(string $file_name): Response
    {
        $result = 0;
        $rules = explode(PHP_EOL, $this->getAllData(5, $file_name));
        $rules = array_map(fn($rule) => explode('|', $rule), $rules);

        $lists = explode(PHP_EOL, $this->getAllData(5, $file_name . '2'));
        $lists = array_map(fn($list) => explode(',', $list), $lists);

        $lists_count = count($lists);
        for ($i = 0; $i < $lists_count; $i++) {
            $list = $lists[$i];
            $list_count = count($list);
            for ($j = 0; $j < $list_count - 1; $j++) {
                $rule_valid = $this->printQueue_ChkRule($rules, $list[$j], $list[$j + 1]);
                if (!$rule_valid) {
                    break;
                }
            }
            if ($rule_valid) {
                $result += $list[(count($list) / 2)];
            }
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }


//    #[Route('/day5/b/{file_name}', name: 'print_queue_b', methods: ['GET'])]
//    public function printQueue_b(string $file_name): Response
//    {
//        $result = 0;
//        $rules = explode(PHP_EOL, $this->getAllData(5, $file_name));
//        $rules = array_map(fn($rule) => explode('|', $rule), $rules);
//
//        $lists = explode(PHP_EOL, $this->getAllData(5, $file_name.'2'));
//        $lists = array_map(fn($list) => explode(',', $list), $lists);
//
//        $lists_count = count($lists);
//
//        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
//    }


    # day 6
    #[Route('/day6/a/{file_name}', name: 'guard_gallivant_a', methods: ['GET'])]
    public function guardGallivant_a(string $file_name): Response
    {
        $result = 0;
        $data = $this->getAllData(6, $file_name);
        $data_y = explode(PHP_EOL, $data);

        $out = false;
        $cursor = ['x' => 0, 'y' => 0]; // define default cursor position
        $direction = 'UP'; // UP, RIGHT, DOWN, LEFT
        $border_max = count($data_y) - 1;
        $border_min = 0;

        // find the starting point
        foreach ($data_y as $y => $line) {
            foreach (str_split($line) as $x => $char) {
                if ($char === '^') {
                    $cursor = ['x' => $x, 'y' => $y];
                    break;
                }
            }
            if ($out) {
                break;
            }
        }

        // replace ^ with X
        $data_y[$cursor['y']][$cursor['x']] = 'X';

        // rules:
        //      If something is directly in front of you, turn 90 degrees to the right.
        //      Otherwise, take a step forward.
        while (true) {
            // check if char in next step is '#'
            $char = $this->lookAhead($cursor, $direction, $data_y);
            if ($char === '#') {
                // turn 90 degrees to the right
                $direction = match ($direction) {
                    'UP' => 'RIGHT',
                    'RIGHT' => 'DOWN',
                    'DOWN' => 'LEFT',
                    'LEFT' => 'UP',
                };
            } else {
                // take a step forward
                $cursor = $this->moveCursor($cursor, $direction);

                // check if the cursor is on the border of the grid
                if (($cursor['x'] === $border_max ||
                        $cursor['x'] === $border_min ||
                        $cursor['y'] === $border_max ||
                        $cursor['y'] === $border_min) &&
                    $data_y[$cursor['y']][$cursor['x']] === '.'
                ) {
                    $data_y[$cursor['y']][$cursor['x']] = 'X';
                    break;
                }

                // replace . with X
                $data_y[$cursor['y']][$cursor['x']] = 'X';
            }

        }

        // count the number of X in the grid
        foreach ($data_y as $line) {
            $result += substr_count($line, 'X');
        }


        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    # helper function

    /**
     * Move the cursor in the given direction
     *
     * @param array $cursor
     * @param string $direction
     * @return array
     */
    private function moveCursor(array $cursor, string $direction): array
    {
        $one_step = [
            'UP' => ['x' => 0, 'y' => -1],
            'RIGHT' => ['x' => 1, 'y' => 0],
            'DOWN' => ['x' => 0, 'y' => 1],
            'LEFT' => ['x' => -1, 'y' => 0],
        ];
        $cursor['x'] += $one_step[$direction]['x'];
        $cursor['y'] += $one_step[$direction]['y'];
        return $cursor;
    }

    // look at the next step char in the given direction
    private function lookAhead(array $cursor, string $direction, array $data_y): string
    {
        $one_step = [
            'UP' => ['x' => 0, 'y' => -1],
            'RIGHT' => ['x' => 1, 'y' => 0],
            'DOWN' => ['x' => 0, 'y' => 1],
            'LEFT' => ['x' => -1, 'y' => 0],
        ];
        $cursor['x'] += $one_step[$direction]['x'];
        $cursor['y'] += $one_step[$direction]['y'];
        return $data_y[$cursor['y']][$cursor['x']];
    }

    /**
     * Check if the given list of numbers is valid
     *
     * @param array $rules
     * @param int $a
     * @param int $b
     * @return bool
     */
    private function printQueue_ChkRule(array $rules, int $a, int $b): bool
    {
        $list_comb = $a . '|' . $b;
        foreach ($rules as $rule) {
            $rule_comb = $rule[0] . '|' . $rule[1];
            if ($list_comb === $rule_comb) {
                return true;
            }
        }
        return false;
    }

    /**
     * Search for the string XMAS in the file
     *
     * @param string $data content of the file
     * @return Generator
     */
    private function ceresSearch(string $data): Generator
    {
        $m = explode(PHP_EOL, $data);
        $line_count = count($m) - 2;
        for ($y = 0; $y < $line_count; $y++) {
            for ($x = 0; $x < $line_count; $x++) {
                yield $m[$y][$x] . $m[$y][$x + 2] . $m[$y + 1][$x + 1] . $m[$y + 2][$x] . $m[$y + 2][$x + 2];
            }
        }
    }

    /**
     * Reads the entire contents of a file and returns two lists of integers
     *
     * @param string $file_name part of the file name
     * @return array[] 2 lists of integers in one array
     */
    private function historianHysteria_getAB(string $file_name): array
    {
        $data = $this->getAllData(1, $file_name);
        $result_a = [];
        $result_b = [];

        // extract the number pairs line by line
        preg_match_all('/(\d+)\s+(\d+)/', $data, $matches, PREG_SET_ORDER);

        // Iterate over each line and add the number pairs
        foreach ($matches as $match) {
            $result_a[] = (int)$match[1];
            $result_b[] = (int)$match[2];
        }

        return [$result_a, $result_b];
    }

    /**
     * Check if the given array is valid
     *
     * @param array $vals
     * @return bool
     */
    private function redNosedReports_isSave(array $vals): bool
    {
        $isLocked = false;
        $initialGradient = 0;
        $isValid = true;

        foreach ($this->redNosedReports_readAB($vals) as [$a, $b]) {
            $gradient = $a <=> $b;
            $diff = abs($a - $b);

            if (!$isLocked && $gradient !== 0) {
                $isLocked = true;
                $initialGradient = $gradient;
            }

            if (
                $diff < 1 || $diff > 3 ||                       // invalid distance
                $gradient === 0 ||                              // No gradient
                ($isLocked && $gradient !== $initialGradient)   // change of direction
            ) {
                $isValid = false;
                break;
            }
        }
        return $isValid;
    }

    /**
     * Read the array in pairs of two
     *
     * @param array $vals
     * @return Generator
     */
    private function redNosedReports_readAB(array $vals): Generator
    {
        for ($i = 1; $i < count($vals); $i++) {
            yield [$vals[$i - 1], $vals[$i]];
        }
    }

    /**
     * Reads the entire contents of a file
     *
     * @param int $day Day of the challenge
     * @param string $file_name part of the file name
     * @return string content of the file
     */
    private function getAllData(int $day, string $file_name): string
    {
        $file_path = __DIR__ . "/../../data/day{$day}/{$file_name}.txt";
        if (!file_exists($file_path)) {
            throw $this->createNotFoundException("The file {$file_path} was not found.");
        }
        return file_get_contents($file_path);
    }

    /**
     * @param string $day Day of the challenge
     * @param string $file_name part of the file name
     * @return Generator content of the file
     */
    private function readDataLine(string $day, string $file_name): Generator
    {
        $file_path = __DIR__ . "/../../data/day{$day}/{$file_name}.txt";

        if (!file_exists($file_path)) {
            throw new RuntimeException("File not found: {$file_path}");
        }

        $handle = fopen($file_path, "r");
        if ($handle === false) {
            throw new RuntimeException("Could not open file: {$file_path}");
        }

        try {
            while (($line = fgets($handle)) !== false) {
                yield trim($line); // trim removes unnecessary spaces and line breaks
            }
        } finally {
            fclose($handle);
        }
    }

}

