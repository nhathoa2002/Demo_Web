<?php

namespace App\Controller;

use App\Entity\Category;
use App\Form\CategoryType;
use App\Repository\CategoryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
#[Route('/category')]
class CategoryController extends AbstractController
{
    #[Route('', name: 'category_index')]
    public function ViewAllcategory()
    {
        $count = 0;
        $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
        foreach($categories as $c){
            $count++;
        }
        return $this->render('category/index.html.twig',[
            'categories'=> $categories,
            'count' => $count
        ]);
    }
    //View category by id
    #[Route('/detail/{id}', name: 'category_detail')]
    public function ViewcategoryById($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash(
               'Error',
               'category not found'
            );
            return $this->redirectToRoute('category_index');
        }
        return $this->render('category/detail.html.twig',[
            'category'=>$category
        ]);
    }
    // Delete category
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/delete/{id}', name: 'category_delete')]
    public function Deletecategory($id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        if ($category == null) {
            $this->addFlash(
               'Error',
               'Category not found'
            );
        } else if (count($category->getProducts()) > 0) {
            $this -> addFlash("Error","Can not delete this category !");
        }
        else{
        $manager = $this->getDoctrine()->getManager();
        $manager->remove($category);
        $manager->flush();
        $this->addFlash("Success","Delete category success !");
        }
        return $this->redirectToRoute('category_index');
    }
    //Add new category
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/add', name: 'category_add')]
    public function Addcategory(Request $request)
    {
        $category = new Category;
        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);
        
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $category->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('category_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $category->setImage($imageName);
            }
            
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('category_index');
        }

        return $this->renderForm('category/add.html.twig',
        [
            'categoryform'=>$form
        ]);
    }
    //Edit category
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/edit/{id}', name: 'category_edit')]
    public function Editcategory(Request $request, $id)
    {
        $category = $this->getDoctrine()->getRepository(Category::class)->find($id);
        $form = $this->createForm(CategoryType::class,$category);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $category->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('category_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $category->setImage($imageName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($category);
            $manager->flush();
            return $this->redirectToRoute('category_index');
        }
        return $this->renderForm('category/edit.html.twig',
        [
            'categoryform' => $form
        ]);
    }

    #[Route("/asc",name:"sort_asc_category")]
    public function ascName(CategoryRepository $repository) {
        $categories = $repository->sortCategoryAsc();
        return $this->render('category/index.html.twig',[
            'categories'=> $categories
        ]);
    }


    #[Route("/desc",name:"sort_desc_category")]
    public function descName(CategoryRepository $repository) {
        $categories = $repository->sortCategoryDesc();
        return $this->render('category/index.html.twig',[
            'categories'=> $categories
        ]);
        
    }

    #[Route('/search', name: 'category_search')]
    public function SearchCategory(Request $request, CategoryRepository $repository)
    {
        $name = $request->get('word');
        $categories = $repository->searchCategory($name);
        if($categories == null){
            $categories = $this->getDoctrine()->getRepository(Category::class)->findAll();
            $this->addFlash(
                'Error',
                'Category not found'
             );
            return $this->render("category/index.html.twig",
            [
                'categories' => $categories
            ]);
        }
            return $this->render("category/index.html.twig",
            [
                'categories' => $categories
            ]);
    }
}
