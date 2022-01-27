<?php

namespace App\Controller;

use App\Entity\Voiture;
use App\Repository\VoitureRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/voiture')]
class VoitureController extends AbstractController
{
    #[Route('/creer', name: 'voiture_creer', methods: ['POST'])]
    public function index(Request $request, SerializerInterface $serializer, EntityManagerInterface $em): Response
    {
        //RECEPTION DES DONNÉES PAR LE FRONT
        $jsonRecu = $request->getContent();
        //CONVERSION DES DONNÉES EN CLASS USER
        $carToCreate = $serializer->deserialize($jsonRecu, Voiture::class, "json");

        $em->persist($carToCreate);
        $em->flush();

        //RENVOI DE L'OBJET POUR CONFIRMATION D'ENREGISTREMENT
        return $this->json([
            "marque" => $carToCreate->getMarque(),
            "vitesse" => $carToCreate->getVitesse()
        ], 201, []);
    }

    #[Route('/afficher_toutes_les_voitures', name: 'voitures_afficher', methods: ['GET'])]
    public function showCars(VoitureRepository $repo): Response
    {
        //APPEL DE LA METHODE DANS LE REPOSITORY
        $cars = $repo->findAll();
        //RENVOI DU JSON
        return $this->json($cars, 200);
    }

    #[Route('/afficher_la_voiture/{id}', name: 'voiture_afficher', methods: ['GET'])]
    public function showCar(VoitureRepository $repo, $id): Response
    {
        //APPEL DE LA METHODE DANS LE REPOSITORY
        $car = $repo->findOneBy(["id" => $id]);

        //RENVOI DU JSON
        return $this->json($car, 200);
    }

    #[Route('/modifier_la_voiture/{id}', name: 'voiture_modifier', methods: ['POST'])]
    public function modifyCar(VoitureRepository $repo, Request $request, $id, SerializerInterface $serializer): Response
    {
        //RECUPERATION DE LA DATA ENVOYÉE
        $data = $request->getContent();

        //TRANSFORMER LA DATA EN TABLEAU INDICÉ
        $dataTransformed = $serializer->deserialize($data, Voiture::class, "json");

        //APPEL DE LA METHODE DANS LE REPOSITORY
        $car = $repo->findOneAndUpdate($id, $dataTransformed);

        //RENVOI DU JSON
        return $this->json($car, 200);
    }

    #[Route('/supprimerer_la_voiture/{id}', name: 'voiture_supprimer', methods: ['POST'])]
    public function deleteCar(VoitureRepository $repo, $id)
    {
        //APPEL DE LA METHODE DANS LE REPOSITORY
        $repo->findOneAndDelete($id);
    }
}
