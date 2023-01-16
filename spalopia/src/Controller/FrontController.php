<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class FrontController extends AbstractController
{

    #[Route('/', name: 'app_home', methods: [Request::METHOD_GET])]
    public function homeAction(): Response {
        return $this->render('home.html.twig', []);
    }
}