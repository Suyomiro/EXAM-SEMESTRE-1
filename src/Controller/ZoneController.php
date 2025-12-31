<?php

namespace App\Controller;

use App\Entity\Zone;
use App\Repository\ZoneRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ZoneController extends AbstractController
{
    #[Route('/manager/zones', name: 'zones_index', methods: ['GET'])]
    public function index(Request $request, ZoneRepository $zoneRepository): Response
    {
        if (!$request->getSession()->get('manager_logged_in')) {
            return $this->redirectToRoute('manager_login');
        }

        $zones = $zoneRepository->findAll();

        $quartierCount = function($q): int {
            if (is_countable($q)) {
                return count($q);
            }
            if (is_string($q)) {
                if ($q === '') {
                    return 0;
                }
                $parts = array_values(array_filter(array_map('trim', explode(',', $q))));
                return count($parts);
            }
            return 0;
        };

        $totalQuartiers = array_reduce($zones, function(int $sum, Zone $z) use ($quartierCount): int {
            return $sum + $quartierCount($z->getQuartiers());
        }, 0);
        $averagePrice = count($zones) > 0
            ? (int) round(array_reduce($zones, fn(int $sum, Zone $z) => $sum + $z->getPrice(), 0) / count($zones))
            : 0;

        return $this->render('zones/index.html.twig', [
            'zones' => $zones,
            'totalQuartiers' => $totalQuartiers,
            'averagePrice' => $averagePrice,
        ]);
    }

    #[Route('/manager/zones/nouvelle', name: 'zones_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $zone = new Zone();
        $quartiersArr = array_values(array_filter(array_map('trim', explode(',', (string) $request->request->get('quartiers')))));
        $zone
            ->setName((string) $request->request->get('name'))
            ->setPrice((int) $request->request->get('price'))
            ->setQuartiers(count($quartiersArr) > 0 ? implode(',', $quartiersArr) : null);

        $em->persist($zone);
        $em->flush();

        return $this->redirectToRoute('zones_index');
    }

    #[Route('/manager/zones/{id}/modifier', name: 'zones_update', methods: ['POST'])]
    public function update(Zone $zone, Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $quartiersArr = array_values(array_filter(array_map('trim', explode(',', (string) $request->request->get('quartiers')))));
        $zone
            ->setName((string) $request->request->get('name'))
            ->setPrice((int) $request->request->get('price'))
            ->setQuartiers(count($quartiersArr) > 0 ? implode(',', $quartiersArr) : null);

        $em->flush();

        return $this->redirectToRoute('zones_index');
    }

    #[Route('/manager/zones/{id}/supprimer', name: 'zones_delete', methods: ['POST'])]
    public function delete(Zone $zone, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($zone);
        $em->flush();

        return $this->redirectToRoute('zones_index');
    }
}


