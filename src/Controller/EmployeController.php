<?php

namespace App\Controller;

use App\Entity\Employe;
use App\Repository\EmployeRepository;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EmployeController extends AbstractController
{
    #[Route('/employe', name: 'app_employe')]
    public function index(EmployeRepository $employeRepository): Response
    {
        // SELECT * FROM employe ORDER BY nom ASC
        $employes = $employeRepository->findBy([], ["nom" => "ASC"]);
       // renvoie les informations dans la vue
        return $this->render('employe/index.html.twig', [
            // on passe la variable $employes à la vue
            'employes' => $employes
        ]);
    }

    // on veux afficher le détail d'un employé
    // pas de même URL/nom ! on utilise l'id pour cibler un employé
    #[Route('/employe/{id}', name: 'show_employe')]
    // param converter, il s'agit de mettre le nom de la classe en argument de show pour que l'id soit trouvé comme clé primaire
    public function show(Employe $employe): Response
    {
        // renvoie les informations dans la vue show
        return $this->render('employe/show.html.twig', [
            // on passe la variable $employe à la vue (pour un employe)
            // vue à créer dans templates/employe
            'employe' => $employe
        ]);
    }
}
