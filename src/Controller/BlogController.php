<?php

// src/Controller/BLogController.php

namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

use App\DatabaseAccess;

class BlogController extends AbstractController
{
    // public $conn = new DatabaseAccess;
    #[Route('/blog', name: 'blog_list')]
    public function list(): Response
    {
        return new Response('Welcome to my Blog');
    }
}
