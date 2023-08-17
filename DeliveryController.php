<?php

namespace App\Controller;
use App\Entity\Delivery;
use App\Form\DeliveryType;
use App\Repository\DeliveryRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

#[Route('/delivery')]
class DeliveryController extends AbstractController
{
    #[Route('', name: 'delivery_index')]
    public function ViewAllDelivery(DeliveryRepository $repository)
    {
        // $delivery = $this->getDoctrine()->getRepository(Delivery::class)->findAll();
        $delivery = $repository->ViewDeliveryList();
        return $this->render(
            "delivery/index.html.twig",
            [
                'deliverys' => $delivery
            ]
        );
    }

    #[Route('/detail/{id}', name: 'delivery_detail')]
    public function ViewDeliveryById($id)
    {
        $delivery = $this->getDoctrine()->getRepository(Delivery::class)->find($id);
        if ($delivery == null) {
            $this->addFlash("Error", "Delivery not found !");

            return $this->redirectToRoute('delivery_index');
        }
        return $this->render(
            "delivery/detail.html.twig",
            [
                'delivery' => $delivery
            ]
        );
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/delete/{id}', name: 'delivery_delete')]
    public function DeleteDelivery($id)
    {
        $delivery = $this-> getDoctrine()->getRepository(Delivery::class)-> find($id);
        if ($delivery === null) {
            $this -> addFlash("Error","Delivery not found !");
        }
        else if (count($delivery->getOrders()) > 0) {
            $this -> addFlash("Error","Can not delete this delivery !");
        }
        else{
            $manager =$this -> getDoctrine()->getManager();
            $manager -> remove($delivery);
            $manager -> flush();
            $this -> addFlash("Success","Delivery deleted successfully !");
        }
        return $this -> redirectToRoute('delivery_index');
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/add', name: 'delivery_add')]
    public function AddDelivery(Request $request)
    {
        $delivery = new Delivery;
        $form = $this -> createForm(DeliveryType::class,$delivery);
        $form -> handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $delivery->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('delivery_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $delivery->setImage($imageName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($delivery);
            $manager->flush();
            return $this->redirectToRoute('delivery_index');
        }
        return $this->renderForm('delivery/add.html.twig',
        [
            'deliveryForm' => $form
        ]);
    }
    /**
     * @IsGranted("ROLE_ADMIN")
     */
    #[Route('/edit/{id}', name: 'delivery_edit')]
    public function EditDelivery(Request $request, $id)
    {
        $delivery = $this->getDoctrine()->getRepository(Delivery::class)->find($id);
        $form = $this->createForm(DeliveryType::class, $delivery);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $file = $form['image']->getData();
            if($file != null){
                $image = $delivery->getImage();
                $imgName = uniqid();
                $imgExtension = $image->guessExtension();
                $imageName = $imgName . '.' . $imgExtension;
                try {
                    $image->move(
                        $this->getParameter('delivery_image'),$imageName
                    );
                } catch (FileException $e) {
                    throwException($e);
                }
                $delivery->setImage($imageName);
            }
            $manager = $this->getDoctrine()->getManager();
            $manager->persist($delivery);
            $manager->flush();
            return $this->redirectToRoute('delivery_index');
        }
        return $this->renderForm(
            'delivery/edit.html.twig',
            [
            'deliveryForm' => $form
        ]
        );
    }

    #[Route('/search', name: 'delivery_search')]
    public function SearchDelivery(Request $request, DeliveryRepository $repository)
    {
        $name = $request->get('word');
        $delivery = $repository->searchDelivery($name);
            return $this->render("delivery/index.html.twig",
            [
                'deliverys' => $delivery
            ]);
    }
}
