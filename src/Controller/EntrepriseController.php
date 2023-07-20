<?php

namespace App\Controller;

// il faut importer les classes Entreprise et EntityManagerInterface (et autres) en faisant clic droit
// sur leur noms où elles sont appelées puis import Class (attention au bon package ! dans le bon namespace)
use App\Entity\Entreprise;
use App\Form\EntrepriseType;
use Doctrine\ORM\EntityManager;
use App\Repository\EntrepriseRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EntrepriseController extends AbstractController
{
    // /entreprise est l'URL et son nom app_entreprise (pour les liens)
    #[Route('/entreprise', name: 'app_entreprise')]
    // EntityManagerInterface va permettre d'accéder à un certain nombre de méthodes (notament getRepository)
    // public function index(EntityManagerInterface $entityManager): Response

    // autre méthode avec entrepriseRepository directement (importer la class !)
    public function index(EntrepriseRepository $entrepriseRepository): Response
    
    {
        // pour afficher les entreprises (getRepository se base sur l'entité Entreprise)
    // $entreprises = $entityManager->getRepository(Entreprise::class)->findAll();

        // suite de l'autre méthode 
        // findBy avec critères (premier choix)
        // findBy et trier (en deuxième choix (orderBy) -> voir la fonction dans EntrepriseRepository)
        // Equivalent SQL = SELECT * FROM entreprise WHERE ville = 'Strasbourg' ORDER BY raisonSociale (par défaut ASC)
    // $entreprises = $entrepriseRepository->findBy(["ville" => "STRASBOURG"], ["raisonSociale" => "ASC"]);
       $entreprises = $entrepriseRepository->findBy([], ["raisonSociale" => "ASC"]);
       // renvoie les informations dans la vue index
        return $this->render('entreprise/index.html.twig', [
            // on passe la variable $entreprises à la vue (pour le tableau des entreprises)
            'entreprises' => $entreprises
        ]);
    }
    
    // création du formulaire pour créer une nouvelle entreprise
    #[Route('/entreprise/new', name: 'new_entreprise')]
    // on utilise le même formulaire pour éditer une entreprise
    #[Route('/entreprise/{id}/edit', name: 'edit_entreprise')]
    public function new_edit(Entreprise $entreprise = null, Request $request, EntityManagerInterface $entityManager): Response
    {    
        // on ajoute la condition si une entreprise n'existe pas
        if(!$entreprise) {
            // on crée un nouveau objet entreprise  
            $entreprise = new Entreprise();
        }


        // on crée un formulaire à partir de EntrepriseType
        $form = $this->createForm(EntrepriseType::class, $entreprise);

        // on prend en charge la requête demandée de part le formulaire
        $form->handleRequest($request);

        // si le formulaire est soumi et qu'il est valide
        if ($form->isSubmitted() && $form->isValid()) {

            // récupération des données du formulaire
            $entreprise = $form->getData();
            // équivalent de prepare en PDO (prepare l'objet pour l'insertion en BDD)
            $entityManager->persist($entreprise);
            // équivalent d'execute en PDO (execute la requête) 
            $entityManager->flush();

            // alors on redirige vers la liste d'entreprise
            return $this->redirectToRoute('app_entreprise');
        }

        // sinon on reste sur le formulaire d'ajout d'entreprise
        return $this->render('entreprise/new.html.twig', [
            'formAddEntreprise'=>$form,
            // edit va envoyer l'id de l'entreprise a modifier ou sinon faux dans le cas d'une nouvelle entreprise
            'edit' => $entreprise->getId()
        ]);
    }

    // on veux afficher le détail d'une entreprise
    // pas de même URL/nom ! on utilise l'id pour cibler une entreprise
    #[Route('/entreprise/{id}', name: 'show_entreprise')]
    // param converter, il s'agit de mettre le nom de la classe en argument de show pour que l'id soit trouvé comme clé primaire
    public function show(Entreprise $entreprise): Response
    {
        // renvoie les informations dans la vue show
        return $this->render('entreprise/show.html.twig', [
            // on passe la variable $entreprise à la vue (pour une entreprise)
            // vue à créer dans templates/entreprise
            'entreprise' => $entreprise
        ]);
    }

}
