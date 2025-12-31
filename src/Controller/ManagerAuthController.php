<?php

namespace App\Controller;

use App\Entity\Gestionnaire;
use Doctrine\DBAL\Exception\TableNotFoundException;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class ManagerAuthController extends AbstractController
{
    #[Route('/manager/login', name: 'manager_login', methods: ['GET', 'POST'])]
    public function login(Request $request, ManagerRegistry $doctrine): Response
    {
        if ($request->getSession()->get('manager_logged_in')) {
            return $this->redirectToRoute('dashboard_index');
        }

        $error = null;

        if ($request->isMethod('POST')) {
            $email = (string) $request->request->get('email');
            $password = (string) $request->request->get('password');

            try {
                // Use the entity repository directly to ensure we always query `gestionnaires`
                $gestionnaireRepository = $doctrine->getRepository(Gestionnaire::class);
                $gestionnaire = $gestionnaireRepository->findOneBy(['email' => $email]);
            } catch (TableNotFoundException $e) {
                // Friendly error if table is missing (common cause of the app_user error)
                $error = 'Erreur de configuration : la table des gestionnaires est introuvable. Merci d’appliquer les migrations.';
                return $this->render('manager/login.html.twig', ['error' => $error]);
            }

            if ($gestionnaire && $password !== '') {
                $stored = $gestionnaire->getPassword();

                // First, support already-hashed passwords using password_verify
                if (is_string($stored) && password_verify($password, $stored)) {
                    // OK - password matches
                    $request->getSession()->set('manager_logged_in', true);
                    $request->getSession()->set('manager_email', $email);

                    return $this->redirectToRoute('dashboard_index');
                }

                // Backward compatibility: if password was stored in plain text, update it to a secure hash
                if ($stored === $password) {
                    $hashed = password_hash($password, PASSWORD_DEFAULT);
                    $gestionnaire->setPassword($hashed);
                    $em = $doctrine->getManager();
                    $em->persist($gestionnaire);
                    $em->flush();

                    $request->getSession()->set('manager_logged_in', true);
                    $request->getSession()->set('manager_email', $email);

                    return $this->redirectToRoute('dashboard_index');
                }
            }

            $error = 'Identifiants invalides';
        }

        return $this->render('manager/login.html.twig', [
            'error' => $error,
        ]);
    }

    #[Route('/manager/logout', name: 'manager_logout', methods: ['POST'])]
    public function logout(Request $request): RedirectResponse
    {
        $request->getSession()->invalidate();
        return $this->redirectToRoute('manager_login');
    }
}


