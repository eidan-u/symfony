<?php

namespace App\Controller;

use App\Entity\Panier;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\Routing\Annotation\Route;
use Symfony\Contracts\Translation\TranslatorInterface;

class PanierController extends AbstractController
{
    /**
     * @Route("/panier", name="panier")
     */
    public function index()
    {
     
            $pdo = $this->getDoctrine()->getManager();
            $paniers = $pdo->getRepository(Panier::class)->findAll();

        return $this->render('panier/index.html.twig', [
            'panier' => $paniers
        ]);
    }
     /**
     * @Route("/panier/panier{id}_delete", name="panier_delete")
     */
    public function delete(Panier $panier=null, TranslatorInterface $translator){
        if($panier != null){
            $em = $this->getDoctrine()->getManager();
            $em->remove($panier);
            $em->flush();
            
        $this->addFlash('success', $translator->trans('flash.Bask.del'));
    }
return $this->redirectToRoute('panier');

}
}
