<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/aoc24')]
class AocController extends AbstractController
{
    #[Route('/welcome', name: 'welcome', methods: ['GET'])]
    public function index(): Response
    {
        return new Response('Welcome to Advent of Code 2024' . PHP_EOL);
    }

    # day 1
    #[Route('/day1/a/{name}', name: 'historian_hysteria_a', methods: ['GET'])]
    public function historian_hysteria_a(string $name): Response
    {
        // Zahlenpaare aus der Datei lesen
        [$result_a, $result_b] = $this->historian_hysteria($name);

        // kleinste zahl a und b finden abstand der beiden zahlen berechnen und aus dem array löschen
        $result = 0;
        while (count($result_a) > 0 && count($result_b) > 0) {
            // Finde das kleinste Element in beiden Arrays
            $min_a = min($result_a);
            $min_b = min($result_b);

            // Füge die Differenz des kleinsten Elements in das Ergebnis-Array ein
            $result += abs($min_a - $min_b);

            // Entferne nur das erste Vorkommen des kleinsten Werts aus den jeweiligen Arrays
            $index_a = array_search($min_a, $result_a);
            $index_b = array_search($min_b, $result_b);

            // Entferne die Elemente mit array_splice
            array_splice($result_a, $index_a, 1);
            array_splice($result_b, $index_b, 1);
        }

        return new Response(PHP_EOL . $result . PHP_EOL);
    }
    private function historian_hysteria(string $name): array
    {
        $data = $this->getAllData(1,$name);
        $result_a = [];
        $result_b = [];

        // Nutze Regex, um die Zahlenpaare zeilenweise zu extrahieren
        preg_match_all('/(\d+)\s+(\d+)/', $data, $matches, PREG_SET_ORDER);

        // Jede Zeile durchlaufen und die Zahlenpaare hinzufügen
        foreach ($matches as $match) {
            $result_a[] = (int)$match[1];
            $result_b[] = (int)$match[2];
        }

        return [$result_a, $result_b];
    }

    # helper function
    private function getAllData(int $day,string $name):string
    {
        $filePath = __DIR__ . "/../../data/day{$day}/{$name}.txt";
        if (!file_exists($filePath)) {
            throw $this->createNotFoundException("Die Datei {$filePath} wurde nicht gefunden.");
        }
        return file_get_contents($filePath);
    }
}
