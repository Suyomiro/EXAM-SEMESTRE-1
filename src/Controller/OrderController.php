<?php

namespace App\Controller;

use App\Entity\Order;
use App\Repository\OrderRepository;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class OrderController extends AbstractController
{
    #[Route('/manager/commandes', name: 'orders_index', methods: ['GET'])]
    #[Route('/orders', name: 'orders_public', methods: ['GET'])]
    public function index(
        Request $request,
        OrderRepository $orderRepository,
        ZoneRepository $zoneRepository,
        \App\Repository\LivreurRepository $livreurRepository
    ): Response {
        if (!$request->getSession()->get('manager_logged_in')) {
            return $this->redirectToRoute('manager_login');
        }

        $search = (string) $request->query->get('q', '');
        $statusFilter = (string) $request->query->get('status', 'all');
        $typeFilter = (string) $request->query->get('type', 'all');

        $qb = $orderRepository->createQueryBuilder('o')
            ->leftJoin('o.customer', 'c')->addSelect('c')
            ->leftJoin('o.zone', 'z')->addSelect('z')
            ->orderBy('o.id', 'DESC');

        if ($search !== '') {
            $qb
                ->andWhere('LOWER(c.name) LIKE :s OR CAST(o.id AS string) LIKE :s')
                ->setParameter('s', '%' . mb_strtolower($search) . '%');
        }
        if ($statusFilter !== 'all') {
            $qb->andWhere('o.status = :status')->setParameter('status', $statusFilter);
        }
        if ($typeFilter !== 'all') {
            $qb->andWhere('o.type = :type')->setParameter('type', $typeFilter);
        }

        $orders = $qb->getQuery()->getResult();
        $zones = $zoneRepository->findAll();
        $livreurs = $livreurRepository->findAll();

        $stats = [
            'en-cours' => $orderRepository->count(['status' => 'en-cours']),
            'validée' => $orderRepository->count(['status' => 'validée']),
            'terminée' => $orderRepository->count(['status' => 'terminée']),
            'annulée' => $orderRepository->count(['status' => 'annulée']),
        ];

        return $this->render('orders/index.html.twig', [
            'orders' => $orders,
            'zones' => $zones,
            'search' => $search,
            'statusFilter' => $statusFilter,
            'typeFilter' => $typeFilter,
            'stats' => $stats,
            'livreurs' => $livreurs,
        ]);
    }

    #[Route('/manager/commandes/{id}/statut', name: 'orders_update_status', methods: ['POST'])]
    public function updateStatus(Order $order, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $status = (string) $request->request->get('status');

        // Business rule: any order that becomes 'terminée' must be validated first.
        if ($status === 'terminée' && $order->getStatus() !== 'validée') {
            // auto-validate (inform the manager)
            $order->setStatus('validée');
            $em->flush();
            $this->addFlash('info', 'La commande a été validée automatiquement avant d\'être marquée terminée.');
        }

        $order->setStatus($status);
        $em->flush();

        if ($status === 'terminée') {
            $this->addFlash('success', 'La commande a été marquée comme terminée.');
        }

        return $this->redirectToRoute('orders_index');
    }

    #[Route('/manager/commandes/{id}/livreur', name: 'orders_assign_delivery', methods: ['POST'])]
    public function assignDelivery(Order $order, Request $request, EntityManagerInterface $em, \App\Repository\LivreurRepository $livreurRepository): RedirectResponse
    {
        $deliveryId = (int) $request->request->get('deliveryPerson');
        $livreur = $livreurRepository->find($deliveryId);
        if ($livreur) {
            $order->setLivreur($livreur);
            // when assigning a delivery person, ensure the order is validated so it can be completed
            if ($order->getStatus() === 'en-cours') {
                $order->setStatus('validée');
                $this->addFlash('success', 'Livreur assigné — la commande est maintenant validée.');
            }
        }
        $em->flush();

        return $this->redirectToRoute('orders_index');
    }
}


