<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 *
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

    public function save(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Task $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

       /**
    * @return Task[] Returns an array of Task objects
    */
   public function getHorasMensuales( $value, $value2): array
   {
        $tot = $value . '-' . $value2 . '%';
       return $this->createQueryBuilder('t')
           ->andWhere("t.start_time LIKE :val")
           ->setParameter('val', $tot)
           ->orderBy('t.id', 'ASC')
           ->select('t.id as id , t.start_time as st , t.end_time as et, t.extra_time as ext')
           ->getQuery()
           ->getResult()
       ;
   }
   public function getHorasRealizadas($arr){
        $salida = [];
    foreach($arr as $task) {
            $time = $task['st']->diff($task['et']);
            $salida = [$task['id'], $time->days*24+$time->h+($task['ext']*60)/(3600)];
            
            var_dump($salida);
        }
        return $salida;  
   }

//    /**
//     * @return Task[] Returns an array of Task objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Task
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
