<?php

namespace App\Repository;

use App\Entity\Voiture;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Voiture|null find($id, $lockMode = null, $lockVersion = null)
 * @method Voiture|null findOneBy(array $criteria, array $orderBy = null)
 * @method Voiture[]    findAll()
 * @method Voiture[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class VoitureRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Voiture::class);
    }

    public function findOneAndUpdate(int $id, Voiture $data): Voiture
    {
        return $this->createQueryBuilder('v')
            ->update(
                'SET v.marque=:marque, v.vitesse=:vitesse, v.carburant=:carburant, v.kilometrage=:kilometrage,
                v.nbr_porte=:nbrPorte, v.boite_de_vitesse=:boite, v.couleur=:couleur'
            )
            ->where('v.id=:id')
            ->setParameters([
                "marque" => $data->getMarque(),
                "vitesse" => $data->getVitesse(),
                "carburant" => $data->getCarburant(),
                "kilometrage" => $data->getKilometrage(),
                "nbrPorte" => $data->getNbrPorte(),
                "boite" => $data->getBoiteDeVitesse(),
                "couleur" => $data->getCouleur(),
                "id" => $id
            ])
            ->getQuery()
            ->getResult();
    }

    public function findOneAndDelete($id)
    {
        $this->createQueryBuilder('v')
            ->delete()
            ->where('v.id=:id')
            ->setParameter('id', $id);
    }

    // /**
    //  * @return Voiture[] Returns an array of Voiture objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('v.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Voiture
    {
        return $this->createQueryBuilder('v')
            ->andWhere('v.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
