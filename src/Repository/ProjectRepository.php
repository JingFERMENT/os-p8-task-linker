<?php

namespace App\Repository;

use App\Entity\Employee;
use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

         public function findByEmployee(Employee $employee): Array
       {
           return $this->createQueryBuilder('p')
           //employees is an entity in relation with project
               ->join('p.employees', 'e')
               ->where('e = :employees')
               ->setParameter('employees', $employee)
               ->getQuery()
               ->getResult()
           ;
       }

       public function findByTasksAssignedTo(Employee $employee): Array
       {
           return $this->createQueryBuilder('p')
               ->join('p.task', 't')
               ->where('t.employee = :employee')
               ->setParameter('employee', $employee)
               ->getQuery()
               ->getResult()
           ;
       }

           //    /**
    //     * @return Project[] Returns an array of Project objects
    //     */
    //    public function findNonArchivedProject(): array
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.is_archived = :isArchived')
    //            ->setParameter('isArchived', true)
    //            ->getQuery()
    //            ->getResult();
    //    }

    //    public function findOneBySomeField($value): ?Project
    //    {
    //        return $this->createQueryBuilder('p')
    //            ->andWhere('p.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }
}
