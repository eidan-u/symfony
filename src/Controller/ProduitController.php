<?php

namespace App\Controller;

use App\Entity\Panier;
use App\Entity\Produit;
use App\Form\PanierType;
use App\Form\ProduitType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class ProduitController extends AbstractController
{
    /**
     * @Route("/produits", name="produit")
     */
    public function index(Request $request, TranslatorInterface $translator)
    {
        $produit = new Produit;
        $pdo = $this->getDoctrine()->getManager();
        $form = $this->createForm(ProduitType::class, $produit);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid() ){

         $photo = $form->get('photo')->getData();

         if($photo){
            $nomPhoto = uniqid().'.'.$photo->guessExtension();

            try{
                $photo->move(
                    $this->getParameter('upload_dir'),
                    $nomPhoto 
                );
            }
            catch(FileException $e){
                $this->addFlash('danger', $translator->trans("flash.Prod.impPic"));
                return $this->redirectToRoute('produit');
            }

            $produit->setPhoto($nomPhoto);
        }

        $pdo->persist($produit); 
        $pdo->flush();      
        $this->addFlash("success", $translator->trans("flash.Prod.del")); 
    
        }

$produits = $pdo->getRepository(Produit::class)->findAll();
        return $this->render('produit/index.html.twig', [
            'produits' => $produits,
            'Form_Product' => $form->createView()
        ]);
     
    }

    /**
     * @Route("/produits/produit{id}", name="Un_Produit")
     */
    public function produit(Produit $produit=null, Request $request, TranslatorInterface $translator){
        if ($produit!=null){

            $panier = new Panier();
            $pdo = $this->getDoctrine()->getManager();
            $form = $this->createForm(PanierType::class, $panier);
            $form->handleRequest($request);
            if($form->isSubmitted() && $form->isValid()){

                $panier->setDateAjout(new \DateTime('now'));
                $panier->setEtat(False);
                $panier->setProduit($produit);
                $pdo->persist($panier);
                $pdo->flush();
                $this->addFlash("success", $translator->trans("panier.addBasket"));
               

            }

          return $this->render('produit/produit.html.twig', [
            'produit' => $produit,
            'Form_Panier' => $form->createView()
          ]);
        }
        else{
            $this->addFlash('danger', $translator->trans('flash.Prod.noProd'));
            return $this->redirectToRoute('produits');
        }

    }
    /**
     * @Route("/produits/produit{id}/delete", name="delete")
     */
    public function delete(Produit $produit=null, TranslatorInterface $translator){
        if($produit != null){

        $em = $this->getDoctrine()->getManager();
        $em->remove($produit);
        $em->flush();
        $this->addFlash('success', $translator->trans('flash.Prod.del'));
        }
        else{
            $this->addFlash('danger', $translator->trans('flash.Prod.noProd'));
        }
    return $this->redirectToRoute('produit');

    }
}
