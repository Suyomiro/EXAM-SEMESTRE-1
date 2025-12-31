<?php

namespace App\Controller;

use App\Entity\Livreur;
use App\Repository\LivreurRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LivreurController extends AbstractController
{
    #[Route('/manager/livreurs', name: 'livreurs_index', methods: ['GET'])]
    public function index(Request $request, LivreurRepository $livreurRepository): Response
    {
        if (!$request->getSession()->get('manager_logged_in')) {
            return $this->redirectToRoute('manager_login');
        }

        $livreurs = $livreurRepository->findAll();

        return $this->render('livreurs/index.html.twig', [
            'livreurs' => $livreurs,
        ]);
    }

    #[Route('/manager/livreurs/nouveau', name: 'livreurs_create', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em): RedirectResponse
    {
        $livreur = new Livreur();
        $livreur
            ->setNom((string) $request->request->get('nom'))
            ->setPrenom((string) $request->request->get('prenom'))
            ->setTelephone((string) $request->request->get('telephone'));

        $em->persist($livreur);
        $em->flush();

        $this->addFlash('success', 'Livreur ajouté avec succès.');

        return $this->redirectToRoute('livreurs_index');
    }

    #[Route('/manager/livreurs/{id}/modifier', name: 'livreurs_edit', methods: ['GET','POST'])]
    public function edit(Request $request, Livreur $livreur, EntityManagerInterface $em): Response
    {
        if (!$request->getSession()->get('manager_logged_in')) {
            return $this->redirectToRoute('manager_login');
        }

        if ($request->isMethod('POST')) {
            $livreur
                ->setNom((string) $request->request->get('nom'))
                ->setPrenom((string) $request->request->get('prenom'))
                ->setTelephone((string) $request->request->get('telephone'));

            $em->flush();
            $this->addFlash('success', 'Livreur mis à jour.');
            return $this->redirectToRoute('livreurs_index');
        }

        return $this->render('livreurs/edit.html.twig', ['livreur' => $livreur]);
    }

    #[Route('/manager/livreurs/{id}/supprimer', name: 'livreurs_delete', methods: ['POST'])]
    public function delete(Livreur $livreur, EntityManagerInterface $em): RedirectResponse
    {
        $em->remove($livreur);
        $em->flush();
        $this->addFlash('success', 'Livreur supprimé.');
        return $this->redirectToRoute('livreurs_index');
    }
}
