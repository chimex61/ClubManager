<?php
// src\FB\PlayerManagerBundle\Entity\PlayerRepository.php
namespace FB\PlayerManagerBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * PlayerRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PlayerRepository extends EntityRepository
{
    public function findAllPlayers()
    {
        return $this->createQueryBuilder('Player')
            ->where('Player.firstname != :fname ')
            ->andWhere('Player.lastname != :lname')
            ->setParameter('fname', 'club')
            ->setParameter('lname', 'ucv')
            ->getQuery()
            ->getResult();
    }
}
