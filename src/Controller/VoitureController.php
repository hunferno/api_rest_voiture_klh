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
        $selectedCar = $repo->findOneBy(["id" => $id]);

        //RENVOI DU JSON
        return $this->json($selectedCar, 200);
    }

    #[Route('/modifier_la_voiture/{id}', name: 'voiture_modifier', methods: ['POST'])]
    public function modifyCar(VoitureRepository $repo, Request $request, EntityManagerInterface $em, $id): Response
    {
        //RECUPERATION DE LA DATA ENVOYÉE
        $dataFromForm = $request->getContent();
        //TRANSFORMER LA DATA JSON EN TABLEAU INDICÉ
        $dataTransformed = json_decode($dataFromForm, true);
        //SELECTION DE LA VOITURE A MODIFIER
        $selectedCar = $repo->findOneBy(["id" => $id]);
        //MESSAGE D'ERREUR SI LA VOITURE N'EXISTE PAS
        if (!$selectedCar) {
            return $this->json('Pas de voiture existante pour l\'id :' . $id, 404);
        }
        //MISE A JOUR DE LA VOITURE SELECTIONNÉE
        $updatedCar = $selectedCar
            ->setMarque($dataTransformed["marque"])
            ->setVitesse($dataTransformed["vitesse"])
            ->setCarburant($dataTransformed["carburant"])
            ->setKilometrage($dataTransformed["kilometrage"])
            ->setNbrPorte($dataTransformed["nbr_porte"])
            ->setBoiteDeVitesse($dataTransformed["boite_de_vitesse"])
            ->setCouleur($dataTransformed["couleur"]);
        //FAIRE LE FLUSH
        $em->flush();
        //RENVOI DU JSON
        return $this->json($updatedCar, 201);
    }

    #[Route('/supprimer_la_voiture/{id}', name: 'voiture_supprimer', methods: ['POST'])]
    public function deleteCar(VoitureRepository $repo, $id, EntityManagerInterface $em): Response
    {
        //SELECTION DE LA VOITURE A SUPPRIMER
        $selectedCar = $repo->findOneBy(["id" => $id]);
        //SUPPRESSION DE LA VOITURE
        $em->remove($selectedCar);
        $em->flush();
        //RETOUR D'UN MSG DE CONFIRMATION
        return $this->json("Véhicule correctement supprimé", 204);
    }
}
