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
    // Welcome to Advent of Code 2024
    // only for testing purposes
    #[Route('/welcome', name: 'welcome', methods: ['GET'])]
    public function index(): Response
    {
        return new Response('Welcome to Advent of Code 2024' . PHP_EOL);
    }

    # day 1
    #[Route('/day1/a/{name}', name: 'historian_hysteria_a', methods: ['GET'])]
    public function historianHysteria_a(string $name): Response
    {
        // reads the entire contents of a file and returns two lists of integers
        [$result_a, $result_b] = $this->historianHysteria_getAB($name);

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

    #[Route('/day1/b/{name}', name: 'historian_hysteria_b', methods: ['GET'])]
    public function historianHysteria_b(string $name): Response
    {
        // Read number pairs from the file
        [$result_a, $result_b] = $this->historianHysteria_getAB($name);
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
    #[Route('/day2/a/{name}', name: 'Red_Nosed_Reports_a', methods: ['GET'])]
    public function red_nosed_reports_a(string $name): Response
    {
        $result = 0;
        foreach ($this->readDataLine(2, $name) as $line) {
            $vals = explode(' ', $line);
            $valid = $this->redNosedReports_isSave($vals);
            if ($valid === true) {
                $result++;
            }
        }
        return new Response(PHP_EOL . print_r($result, true) . PHP_EOL);
    }

    #[Route('/day2/b/{name}', name: 'Red_Nosed_Reports_b')]
    public function red_nosed_reports_b(string $name): Response
    {
        $result = 0;
        foreach ($this->readDataLine(2, $name) as $line) {
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


    # helper function

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
