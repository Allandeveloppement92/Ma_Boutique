<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route; // permet de lire les anotations
use App\Entity\Produit; //On importe l'élément
use App\Form\ProduitType; //Importation du formulaire
use Symfony\Component\HttpFoundation\Request;


class ProduitController extends AbstractController
{   
    
    /**
     * @Route("/", name="home") 
     */
    public function index(Request $request)
    //$request appartient à la class Request, c'est un objet de Request
    {   
        //Récupère Doctrine (service de gestion de BDD)
        $pdo = $this->getDoctrine()->getManager();
        //Il est responsable de l'enregistrement des objets et de leur récupération dans la base de données.

        
        $produit=new Produit();
        //Création d'un form
        $form = $this->createForm(ProduitType::class, $produit);
        
        //Analyse la requête HTTP
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid() ){
            //Le formulaire à été envoyé, on le sauvegarde
            //On récupère le fichier du formulaire
            $fichier = $form->get('photo')->getData();
            //Si un fichier à été uploadé

            if($fichier){
                $nomFichier = uniqid().'.'. $fichier->guessExtension();
                //génère une chaine de caractère unique qui donnera le nom du fichier

                try{
                    $fichier->move(
                        $this->getParameter('upload_dir'),
                        $nomFichier
                    );
                }
                catch(FileException $e){
                    $this->addFlash("danger","Le fichier n'a pas pu être téléversé");
                    return $this->redirectToRoute('home');
                }

                $produit->setPhoto($nomFichier);
            }

            $pdo->persist($produit); //prepare et enregistre le produit 
            $pdo->flush();            //execute celui-ci
        }

        //Récupère tous les produits 
        $produits = $pdo->getRepository(Produit::class)->findAll();


        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'form_produit_new'=>$form->createView()
            // "=>" Pour un tableau associatif 
            //"->" Pour un objet: la flèche va récupérer quelque chose à l'intérieur d'un objet
            //https://www.youtube.com/watch?v=xmoOvoiPNhU
            //https://www.youtube.com/user/grafikarttv/playlists
        ]);
    }

     /**
    * @Route("/produit/{id}", name="mon_produit")
    */

    public function produit(Request $request, Produit $produit=null){ //Création d'une méthods

        if($produit !=null){
            //Vérifie si produit est affilié à un ID
            $form = $this->createForm(ProduitType::class, $produit);
            $form->handleRequest($request);

                if($form->isSubmitted() && $form->isValid()){
                    $pdo = $this->getDoctrine()->getManager();
                    $pdo->persist($produit);
                    $pdo->flush();
                }

            return $this->render('produit/produit.html.twig',[
                'produit'=>$produit,
                'form' => $form->createView()
            ]);

        }
        else{
            $this-> addFlash("danger", "Catégorie introuvable");
            return $this->redirectToRoute('home');
        }

    }

     /**
     * @Route("/produit/delete/{id}", name="delete_produit")
     */
    public function delete(Produit $produit=null){ // methode pour supprimer un produit 
        if($produit != null){
            $pdo = $this->getDoctrine()->getManager();
            $pdo -> remove($produit); //Suppression;
            $pdo -> flush();

            $this-> addFlash("success", "Produit supprimé"); //message flash comme les alert
        }
        else{
            $this-> addFlash("danger", "Produit introuvable");
        }

        return $this->redirectToRoute('home');
    }
}
// "::"accéder aux membres static ou constant, ainsi qu'aux propriétés ou méthodes surchargées d'une classe.

