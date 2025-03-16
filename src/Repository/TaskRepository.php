<?php

namespace App\Repository;

use App\Entity\Project;
use App\Entity\Statut;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Task>
 */
class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
    }

   /**
    * @return Task[] Returns an array of Task objects
    */
   public function findByProjectAndStatut(Project $project, Statut $statut): array
   {
       return $this->createQueryBuilder('task')
           ->leftJoin('task.project', 'project')
           ->leftJoin('task.statut', 'statut')
           ->andWhere('project.id = :projectId')
           ->andWhere('statut.id = :statutId')
           ->setParameter('projectId', $project->getId())
           ->setParameter('statutId', $statut->getId())
           ->orderBy('task.deadline', 'ASC')
           ->getQuery()
           ->getResult()
       ;
   }

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
