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


}
