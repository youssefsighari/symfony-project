<?php

namespace App\Controller;

use DateTime;
use App\Entity\Livres;
use App\Form\LivreType;
use App\Repository\LivresRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


class LivresController extends AbstractController
{
    #[Route('/panier', name: 'panier_livre')]

    public function panier(SessionInterface $session ,LivresRepository $rep)
    {
     $panier = $session->get('panier', []);

        $data = [];
        $total = 0;

 

        foreach($panier as $id => $quantite){
            $livre = $rep->find($id);

            $data [] = [
                'livre' => $livre,
                'quantite' => $quantite
            ];
            $total += $livre->getPrix() * $quantite;


        }
        
        return $this->render('livres/panier.html.twig', compact('data','total'));
        
    }

    #[Route('/ajouter/{id}', name: 'ajouter_livres')]

    public function ajouter1(Livres $livre, SessionInterface $session)
    {
        
        $id = $livre->getId();


        $panier = $session->get('panier', []);

        if(empty($panier[$id])){
            $panier[$id] = 1 ;
        } else{
            $panier[$id]++;
        }

        $session->set('panier', $panier);
       return $this->redirectToRoute('panier_livre');
    }



    
    #[Route('/remove/{id}', name: 'remove_livres')]

    public function remove(Livres $livre, SessionInterface $session)
    {
        
        $id = $livre->getId();


        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){
            if($panier[$id] > 1){
                $panier[$id]--;
            }
        } else{
            unset($panier[$id]);
        }

        $session->set('panier', $panier);
       return $this->redirectToRoute('panier_livre');
    }

    #[Route('/supp/{id}', name: 'supp_livres')]

    public function supp(Livres $livre, SessionInterface $session)
    {
        
        $id = $livre->getId();


        $panier = $session->get('panier', []);

        if(!empty($panier[$id])){

            unset($panier[$id]);
        }

        $session->set('panier', $panier);
       return $this->redirectToRoute('panier_livre');
    }
#[Route('/vider', name: 'vider_livres')]

    public function vider(SessionInterface $session)
    {
        
       

        $session->remove('panier');
       return $this->redirectToRoute('panier_livre');
    }









    #[Route('/livres', name: 'admin_livres')]

    public function ajouter(LivresRepository $rep): Response
    {
        $livres = $rep->findAll();
        //dd($livres);
        return $this->render('Livres/index.html.twig', ['livres' => $livres]);
    }
    #[Route('/admin/livres/show/{id}', name: 'admin_livres_show')]
    public function show(Livres $livre): Response
    {

        return $this->render('Livres/show.html.twig', ['livre' => $livre]);
    }
    #[Route('/admin/livres/create', name: 'app_admin_livres_create')]
    public function create(EntityManagerInterface $em): Response
    {
        $livre1 = new Livres();
        $livre1->setAuteur('auteur 1')
            ->setEditedAt(new \DateTimeImmutable('01-01-2023'))
            ->setTitre('Titre 4')
            ->setQte(100)
            ->setResume('jhgkjhkjhlhdjfjfdgpghkgmgbkmgblkgm')
            ->setSlug('titre-4')
            ->setPrix(200)
            ->setEditeur('Eni')
            ->setISBN('111.1111.1111.1115')
            ->setImage('https://picsum.photos/300');
        $livre2 = new Livres();
        $livre2->setAuteur('auteur 3')
            ->setEditedAt(new \DateTimeImmutable('01-01-2023'))
            ->setTitre('Titre 4')
            ->setQte(100)
            ->setResume('jhgkjhkjhlhdjfjfdgpghkgmgbkmgblkgm')
            ->setSlug('titre-4')
            ->setPrix(200)
            ->setEditeur('Eni')
            ->setISBN('111.1111.1111.1115')
            ->setImage('https://picsum.photos/300');
        $em->persist($livre1);
        $em->persist($livre2);
        $em->flush();
        dd($livre1);
    }
    #[Route('/admin/livres/delete/{id}', name: 'app_admin_livres_delete')]
    public function delete(EntityManagerInterface $em, Livres $livre): Response
    {

        $em->remove($livre);
        $em->flush();
        dd($livre);
    }
    #[Route('/admin/livres/add', name: 'admin_livres_add')]
    public function add(EntityManagerInterface $em, Request $request): Response
    {
        $livre = new Livres();
        //construction de l'objet formulaire
        $form = $this->createForm(LivreType::class, $livre);
        // recupéretaion et traitement de données
        $form->handleRequest($request);
        if ($form->isSubmitted() and $form->isValid()) {
            $em->persist($livre);
            $em->flush();
            return $this->redirectToRoute('admin_livres');
        }

        return $this->render('livres/add.html.twig', [
            'f' => $form

        ]);
    }
}
