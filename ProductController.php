<?php

namespace App\Controller;

use App\Entity\Brand;
use App\Entity\Product;
use App\Entity\Category;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


#[Route('/product')]
class ProductController extends AbstractController
{
    //View all products
    #[Route('', name: 'product_index')]
    public function ViewAllProduct()
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        return $this->render('product/index.html.twig',[
            'products'=> $products,
            'brands'=> $brands,
            'categories'=> $categories
        ]);
    }
    //View product by id
    #[Route('/detail/{id}', name: 'product_detail')]
    public function ViewProductById($id)
    {
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash(
               'Error',
               'Product not found'
            );
            return $this->redirectToRoute('product_index');
        }
        return $this->render('product/detail.html.twig',[
            'product'=>$product,
            'brands'=> $brands,
            'categories'=> $categories
        ]);
    }
    //View product by category

    #[Route('/catID/{catId}', name: 'product_detail_catId')]
    public function findProductByCatId($catId, ProductRepository $repository)
    {
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        $cat = $this -> getDoctrine()->getRepository(Category::class) ->find($catId);
        $products = $repository->findProductByCatId($cat);
        if ($products == null) {
            $this->addFlash(
               'Error',
               'Product not found'
            );
            return $this->redirectToRoute('product_index');
        }
            return $this->render("product/index.html.twig",
            [
                'products' => $products,
                'brands' => $brands,
                'categories' => $categories
            ]);
    }

    //View product by brand

    #[Route('/brandID/{brandId}', name: 'product_detail_brandId')]
    public function findProductByBrandId($brandId, ProductRepository $repository)
    {
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        $brand = $this -> getDoctrine()->getRepository(Brand::class) ->find($brandId);
        $products = $repository->findProductByBrandId($brand);
        if ($products == null) {
            $this->addFlash(
               'Error',
               'Product not found'
            );
            return $this->redirectToRoute('product_index');
        }
            return $this->render("product/index.html.twig",
            [
                'products' => $products,
                'brands' => $brands,
                'categories' => $categories
            ]);
    }
    // Delete product
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/delete/{id}', name: 'product_delete')]
    public function DeleteProduct($id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        if ($product == null) {
            $this->addFlash(
               'Error',
               'product not found'
            );
        }
        else{
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($product);
        $manager->flush();
        $this->addFlash("Success","Delete product success !");
        }
        return $this->redirectToRoute('product_index');
    }
    //Add new product
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/add', name: 'product_add')]
    public function AddProduct(Request $request)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $product->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('product_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $product->setImage($imageName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }

        return $this->renderForm('product/add.html.twig',
        [
            'productform'=>$form
        ]);
    }
    //Edit product
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/edit/{id}', name: 'product_edit')]
    public function EditProduct(Request $request, $id)
    {
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $form = $this->createForm(ProductType::class,$product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $product->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('product_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $product->setImage($imageName);
            }
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($product);
            $manager->flush();
            return $this->redirectToRoute('product_index');
        }
        return $this->renderForm('product/edit.html.twig',
        [
            'productform' => $form
        ]);
    }

    #[Route("/asc",name:"sort_asc_product")]
    public function ascName(ProductRepository $repository) {
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        $products = $repository->sortProductAsc();
        return $this->render('product/index.html.twig',[
            'products'=> $products,
            'brands' => $brands,
            'categories' => $categories
        ]);
    }


    #[Route("/desc",name:"sort_desc_product")]
    public function descName(ProductRepository $repository) {
        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        $products = $repository->sortProductDesc();
        return $this->render('product/index.html.twig',[
            'products'=> $products,
            'brands' => $brands,
            'categories' => $categories
        ]);
        
    }

    #[Route('/search', name: 'product_search')]
    public function SearchProduct(Request $request, ProductRepository $repository)
    {

        $categories = $this -> getDoctrine()->getRepository(Category::class) ->findAll();
        $brands = $this -> getDoctrine()->getRepository(Brand::class) ->findAll();
        $name = $request->get('word');
        $product = $repository->searchProduct($name);
            return $this->render("product/index.html.twig",
            [
                'products' => $product,
                'brands' => $brands,
                'categories' => $categories
            ]);
    }
}
