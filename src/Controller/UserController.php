<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;

#[Route('/user')]
class UserController extends AbstractController
{
    #[Route('/new', name: 'new.user', methods: ["POST"])]
    public function index(Request $request, SerializerInterface $serializer, UserPasswordHasherInterface $hasher, EntityManagerInterface $em): Response
    {
        //CREATION D'UNE INSTANCE USER
        $user = new User();
        //RECEPTION DES DONNÉES PAR LE FRONT
        $jsonRecu = $request->getContent();
        //CONVERSION DES DONNÉES EN CLASS USER
        $userToCreate = $serializer->deserialize($jsonRecu, User::class, "json");
        //HASHAGE DU PSW RECU
        $pswhashed = $hasher->hashPassword($user, $userToCreate->getPassword());
        //INSERTION DU PSW HASHÉ DANS LE USER
        $userToCreate->setPassword($pswhashed);

        $em->persist($userToCreate);
        $em->flush();

        //CONVERSION DU USER CREE EN OBJET JSON
        $userToJson = $serializer->serialize($userToCreate, 'json');
        //RENVOI DE L'OBJET POUR CONFIRMATION D'ENREGISTREMENT
        return new JsonResponse($userToJson, 201, [], true);
    }



    #[Route('/logout', name: 'deconnexion')]
    public function logout()
    {
    }
}
