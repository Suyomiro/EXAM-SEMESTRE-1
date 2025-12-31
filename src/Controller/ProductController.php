<?php

namespace App\Controller;

use App\Entity\Burger;
use App\Entity\Complement;
use App\Entity\Menu;
use App\Repository\BurgerRepository;
use App\Repository\ComplementRepository;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ProductController extends AbstractController
{
    #[Route('/manager/produits', name: 'products_index', methods: ['GET'])]
    #[Route('/products', name: 'products_public', methods: ['GET'])]
    public function index(
        Request $request,
        BurgerRepository $burgerRepository,
        MenuRepository $menuRepository,
        ComplementRepository $complementRepository
    ): Response {
        $search = (string) $request->query->get('q', '');

        $burgers = $burgerRepository->createQueryBuilder('b')
            ->andWhere('LOWER(b.name) LIKE :search')
            ->setParameter('search', '%' . mb_strtolower($search) . '%')
            ->orderBy('b.id', 'DESC')
            ->getQuery()
            ->getResult();

        $menus = $menuRepository->createQueryBuilder('m')
            ->andWhere('LOWER(m.name) LIKE :search')
            ->setParameter('search', '%' . mb_strtolower($search) . '%')
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();

        // transform menus into simple arrays so templates can access computed price
        $menus = $menuRepository->createQueryBuilder('m')
            ->andWhere('LOWER(m.name) LIKE :search')
            ->setParameter('search', '%' . mb_strtolower($search) . '%')
            ->orderBy('m.id', 'DESC')
            ->getQuery()
            ->getResult();

        $menusData = [];
        foreach ($menus as $m) {
            $burger = $m->getBurger();
            $boisson = $m->getBoisson();
            $frite = $m->getFrite();

            $burgerPrice = (float) $burger->getPrice();
            $boissonPrice = (float) $boisson->getPrice();
            $fritePrice = (float) $frite->getPrice();

            $price = (int) round($burgerPrice + $boissonPrice + $fritePrice);

            $menusData[] = [
                'id' => $m->getId(),
                'name' => $m->getName(),
                'description' => $burger->getDescription() ?? '',
                'price' => $price,
                'image' => $m->getImage() ?? $burger->getImage(),
                'available' => !$m->isArchived(),
            ];
        }

        $complements = $complementRepository->createQueryBuilder('c')
            ->andWhere('LOWER(c.name) LIKE :search')
            ->setParameter('search', '%' . mb_strtolower($search) . '%')
            ->orderBy('c.id', 'DESC')
            ->getQuery()
            ->getResult();

        return $this->render('products/index.html.twig', [
            'search' => $search,
            'burgers' => $burgers,
            'menus' => $menusData,
            'complements' => $complements,
        ]);
    }

    #[Route('/manager/produits/burger/nouveau', name: 'products_burger_new', methods: ['POST'])]
    public function createBurger(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $burger = new Burger();
        $burger
            ->setName((string) $request->request->get('name'))
            ->setDescription((string) $request->request->get('description'))
            ->setPrice((int) $request->request->get('price'))
            ->setCategory((string) $request->request->get('category'))
            ->setImage((string) $request->request->get('image'))
            ->setAvailable(true);

        $em->persist($burger);
        $em->flush();

        return $this->redirectToRoute('products_index');
    }

    #[Route('/manager/produits/burger/{id}/modifier', name: 'products_burger_edit', methods: ['POST'])]
    public function editBurger(Burger $burger, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $burger
            ->setName((string) $request->request->get('name'))
            ->setDescription((string) $request->request->get('description'))
            ->setPrice((int) $request->request->get('price'))
            ->setCategory((string) $request->request->get('category'))
            ->setImage((string) $request->request->get('image'));

        $em->flush();

        return $this->redirectToRoute('products_index');
    }

    #[Route('/manager/produits/burger/{id}/archive', name: 'products_burger_archive', methods: ['POST'])]
    public function archiveBurger(Burger $burger, EntityManagerInterface $em): RedirectResponse
    {
        $burger->setAvailable(false);
        $em->flush();

        return $this->redirectToRoute('products_index');
    }
}


