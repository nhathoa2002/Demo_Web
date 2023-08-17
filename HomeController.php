<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController
{
    #[Route('/', name: 'homepage')]
    public function index(): Response
    {
        $products = $this -> getDoctrine()->getRepository(Product::class) ->findAll();
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        return $this->render('home/index.html.twig', [
            'products' => $products,
            'categories' => $categories,
            'brands' => $brands,
        ]);
    }
}
