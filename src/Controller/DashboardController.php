<?php

namespace App\Controller;

use App\Repository\BurgerRepository;
use App\Repository\MenuRepository;
use App\Repository\OrderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class DashboardController extends AbstractController
{
    #[Route('/manager/dashboard', name: 'dashboard_index', methods: ['GET'])]
    public function index(
        Request $request,
        OrderRepository $orderRepository,
        BurgerRepository $burgerRepository,
        MenuRepository $menuRepository
    ): Response {
        if (!$request->getSession()->get('manager_logged_in')) {
            return $this->redirectToRoute('manager_login');
        }

        $today = new \DateTimeImmutable('today');
        $tomorrow = $today->modify('+1 day');

        $qb = $orderRepository->createQueryBuilder('o')
            ->andWhere('o.createdAt >= :today AND o.createdAt < :tomorrow')
            ->setParameter('today', $today)
            ->setParameter('tomorrow', $tomorrow);

        $todayOrders = $qb->getQuery()->getResult();

        $ordersInProgress = array_filter($todayOrders, fn($o) => $o->getStatus() === 'en-cours');
        $ordersValidated = array_filter($todayOrders, fn($o) => $o->getStatus() === 'validée');
        $ordersCancelled = array_filter($todayOrders, fn($o) => $o->getStatus() === 'annulée');
        $ordersCompleted = array_filter($todayOrders, fn($o) => $o->getStatus() === 'terminée');

        $dailyRevenue = array_reduce(
            array_filter($todayOrders, fn($o) => $o->getStatus() !== 'annulée'),
            fn(int $sum, $o) => $sum + $o->getTotal(),
            0
        );

        $topItems = []; // Peut être calculé plus tard à partir des OrderItem

        $recentOrders = $orderRepository->createQueryBuilder('o2')
            ->orderBy('o2.id', 'DESC')
            ->setMaxResults(5)
            ->getQuery()
            ->getResult();

        $productsCount = count($burgerRepository->findAll()) + count($menuRepository->findAll());
        $clientsToday = count(array_unique(array_map(fn($o) => $o->getCustomer()->getId(), $todayOrders)));

        return $this->render('dashboard/index.html.twig', [
            'ordersInProgress' => count($ordersInProgress),
            'ordersValidated' => count($ordersValidated),
            'ordersCancelled' => count($ordersCancelled),
            'ordersCompleted' => count($ordersCompleted),
            'dailyRevenue' => $dailyRevenue,
            'todayOrdersCount' => count($todayOrders),
            'productsCount' => $productsCount,
            'clientsToday' => $clientsToday,
            'topItems' => $topItems,
            'recentOrders' => $recentOrders,
        ]);
    }
}


