<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Category;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class UserController extends AbstractController
{
    #[Route('/users', name: 'app_user')]
    public function index()
    {
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        return $this->render('user/index.html.twig', [
            'brands'=> $brands,
            'categories'=> $categories
        ]);
    }
}
