<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class FicheProduitController extends AbstractController
{
    /**
     * @Route("/fiche/produit", name="fiche_produit")
     */
    public function index()
    {
        return $this->render('fiche_produit/index.html.twig', [
            'controller_name' => 'FicheProduitController',
        ]);
    }
}
