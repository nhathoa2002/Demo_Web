<?php

namespace App\Controller;

use App\Entity\Delivery;
use App\Repository\DeliveryRepository;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;


#[Route('/cart')]
class CartController extends AbstractController
{


    /**
     *  @IsGranted("ROLE_USER");
     */
    #[Route('/', name: 'cart')]
    public function index(SessionInterface $si, ProductRepository $PR, DeliveryRepository $DR)
    {
        $cart = $si->get('cart', []);

        $cartWithData = [];

        foreach ($cart as $id => $quantity) {
            $cartWithData[] = [
                'product' => $PR->find($id),
                'quantity' => $quantity,
            ];
        }


        $total = 0;
        foreach ($cartWithData as $item) {
            $totalItem = $item['product']->getPrice() * $item['quantity'];
            $total += $totalItem;
        }

        // dd($cartWithData);

        // $delivery = $this -> getDoctrine() -> getRepository(Delivery::class)->findall();

        return $this->render('cart/index.html.twig', [
            'items' => $cartWithData,
            'total' => $total,
            'delivery' => $DR->findall(),
        ]);
    }


    /**
     *  @IsGranted("ROLE_USER");
     */
    #[Route('/add/{id}', name: 'cart_add')]
    public function cart_add($id, Session $session)
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            $cart[$id]++;
        } else {
            $cart[$id] = 1;
        }

        $session->set('cart', $cart);

        // dd($session->get('cart'));

        return $this->redirectToRoute('cart', []);
    }

    /**
     *  @IsGranted("ROLE_USER");
     */
    #[Route('/decrease/{id}', name: 'cart_delete')]
    public function cart_decrease($id, Session $session)
    {
        $cart = $session->get('cart', []);

        if ($cart[$id] > 1) {
            $cart[$id]--;
        } else {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        // dd($session->get('cart'));

        return $this->redirectToRoute(
            'cart'
        );
    }


    /**
     *  @IsGranted("ROLE_USER");
     */
    #[Route('/remove/{id}', name: 'cart_remove')]
    public function cart_remove($id, Session $session)
    {
        $cart = $session->get('cart', []);

        if (!empty($cart[$id])) {
            unset($cart[$id]);
        }

        $session->set('cart', $cart);

        return $this->redirectToRoute('cart');
    }
}
