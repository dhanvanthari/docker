<?php

namespace AppBundle\Repository;

use AppBundle\Collection\AdherentCollection;
use AppBundle\Collection\CitizenProjectMembershipCollection;
use AppBundle\Entity\Adherent;
use AppBundle\Entity\CitizenProject;
use AppBundle\Entity\CitizenProjectMembership;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use Ramsey\Uuid\UuidInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class CitizenProjectMembershipRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, CitizenProjectMembership::class);
    }

    /**
     * Creates the query builder to fetch the membership relationship between
     * an adherent and a citizen project.
     */
    private function createMembershipQueryBuilder(Adherent $adherent, CitizenProject $citizenProject): QueryBuilder
    {
        return $this
            ->createQueryBuilder('cpm')
            ->where('cpm.adherent = :adherent')
            ->andWhere('cpm.citizenProject = :citizenProject')
            ->setParameter('adherent', $adherent)
            ->setParameter('citizenProject', $citizenProject)
        ;
    }

    public function findCitizenProjectMembershipsForAdherent(Adherent $adherent): CitizenProjectMembershipCollection
    {
        $query = $this
            ->createQueryBuilder('cpm')
            ->where('cpm.adherent = :adherent')
            ->setParameter('adherent', $adherent)
            ->getQuery()
        ;

        return new CitizenProjectMembershipCollection($query->getResult());
    }

    public function findCitizenProjectMembership(
        Adherent $adherent,
        CitizenProject $citizenProject
    ): ?CitizenProjectMembership {
        return $this
            ->createMembershipQueryBuilder($adherent, $citizenProject)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }

    /**
     * Returns whether or not the given adherent is already an administrator of at least
     * one citizen project.
     */
    public function administrateCitizenProject(Adherent $adherent, CitizenProject $citizenProject = null): bool
    {
        $qb = $this->createQueryBuilder('cpm');

        $qb
            ->select('COUNT(cpm.uuid)')
            ->where('cpm.privilege = :privilege')
            ->andWhere('cpm.adherent = :adherent')
            ->setParameters([
                'adherent' => $adherent,
                'privilege' => CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR,
            ])
        ;

        if ($citizenProject) {
            $qb
                ->andWhere('cpm.citizenProject = :citizenProject')
                ->setParameter('citizenProject', $citizenProject)
            ;
        }

        return (int) $qb->getQuery()->getSingleScalarResult() >= 1;
    }

    public function countAdministratorMembers(CitizenProject $citizenProject): int
    {
        return $this->createQueryBuilder('cpm')
            ->select('COUNT(cpm.uuid)')
            ->where('cpm.citizenProject = :citizenProject')
            ->andWhere('cpm.privilege = :privilege')
            ->setParameters([
                'citizenProject' => $citizenProject,
                'privilege' => CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR,
            ])
            ->getQuery()
            ->getSingleScalarResult()
        ;
    }

    /**
     * Returns the list of all administrator memberships of a citizen project.
     */
    public function findAdministrators(CitizenProject $citizenProject): AdherentCollection
    {
        return $this->findPrivilegedMembers($citizenProject, [CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR]);
    }

    /**
     * Returns the list of all privileged members of a citizen project.
     *
     * @param CitizenProject $citizenProject The citizen project
     * @param array          $privileges     An array of privilege constants (see {@link : CitizenProjectMembership}
     */
    public function findPrivilegedMembers(CitizenProject $citizenProject, array $privileges): AdherentCollection
    {
        $qb = $this->createQueryBuilder('cpm');

        $query = $qb
            ->select('cpm', 'adherent')
            ->leftJoin('cpm.adherent', 'adherent')
            ->where('cpm.citizenProject = :citizenProject')
            ->andWhere($qb->expr()->in('cpm.privilege', $privileges))
            ->orderBy('cpm.joinedAt', 'ASC')
            ->setParameter('citizenProject', $citizenProject)
            ->getQuery()
        ;

        return $this->createAdherentCollection($query);
    }

    public function findFollowers(
        CitizenProject $citizenProject,
        bool $includeAdministrators = true
    ): AdherentCollection {
        $privileges = [CitizenProjectMembership::CITIZEN_PROJECT_FOLLOWER];

        if ($includeAdministrators) {
            $privileges[] = CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR;
        }

        return $this->findPrivilegedMembers($citizenProject, $privileges);
    }

    /**
     * Returns the list of all privileged memberships of a citizen project.
     *
     * @param CitizenProject $citizenProject The citizen project
     * @param array          $privileges     An array of privilege constants (see {@link : CitizenProjectMembership}
     */
    public function findPrivilegedMemberships(
        CitizenProject $citizenProject,
        array $privileges
    ): CitizenProjectMembershipCollection {
        $qb = $this->createQueryBuilder('cpm');

        $query = $qb
            ->where('cpm.citizenProject = :citizenProject')
            ->andWhere($qb->expr()->in('cpm.privilege', $privileges))
            ->orderBy('cpm.joinedAt', 'ASC')
            ->setParameter('citizenProject', $citizenProject)
            ->getQuery()
        ;

        return new CitizenProjectMembershipCollection($query->getResult());
    }

    /**
     * Returns the list of all members of a citizen project.
     */
    public function findMembers(CitizenProject $citizenProject): AdherentCollection
    {
        return $this->createAdherentCollection($this->createCitizenProjectMembershipsQueryBuilder($citizenProject)->getQuery());
    }

    /**
     * @return string[]
     */
    public function findCitizenProjectsUuidByAdministratorFirstName(string $firstName): array
    {
        $qb = $this->createQueryBuilder('cpm');

        $query = $qb
            ->select('cp.uuid')
            ->innerJoin('cpm.adherent', 'a')
            ->innerJoin('cpm.citizenProject', 'cp')
            ->where('LOWER(a.firstName) LIKE :firstName')
            ->andWhere('cpm.privilege = :privilege')
            ->setParameters([
                'firstName' => '%'.strtolower($firstName).'%',
                'privilege' => CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR,
            ])
            ->getQuery()
        ;

        return array_map(function (UuidInterface $uuid) {
            return $uuid->toString();
        }, array_column($query->getArrayResult(), 'uuid'));
    }

    /**
     * @return string[]
     */
    public function findCitizenProjectsUuidByAdministratorLastName(string $lastName): array
    {
        $qb = $this->createQueryBuilder('cpm');

        $query = $qb
            ->select('cp.uuid')
            ->innerJoin('cpm.adherent', 'a')
            ->innerJoin('cpm.citizenProject', 'cp')
            ->where('LOWER(a.lastName) LIKE :lastName')
            ->andWhere('cpm.privilege = :privilege')
            ->setParameters([
                'lastName' => '%'.strtolower($lastName).'%',
                'privilege' => CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR,
            ])
            ->getQuery()
        ;

        return array_map(function (UuidInterface $uuid) {
            return $uuid->toString();
        }, array_column($query->getArrayResult(), 'uuid'));
    }

    /**
     * @return string[]
     */
    public function findCitizenProjectsUuidByAdministratorEmailAddress(string $emailAddress): array
    {
        $qb = $this->createQueryBuilder('cpm');

        $query = $qb
            ->select('cp.uuid')
            ->innerJoin('cpm.adherent', 'a')
            ->innerJoin('cpm.citizenProject', 'cp')
            ->where('LOWER(a.emailAddress) LIKE :emailAddress')
            ->andWhere('cpm.privilege = :privilege')
            ->setParameters([
                'emailAddress' => '%'.strtolower($emailAddress).'%',
                'privilege' => CitizenProjectMembership::CITIZEN_PROJECT_ADMINISTRATOR,
            ])
            ->getQuery()
        ;

        return array_map(function (UuidInterface $uuid) {
            return $uuid->toString();
        }, array_column($query->getArrayResult(), 'uuid'));
    }

    /**
     * Creates a QueryBuilder instance to fetch memberships of a citizen project.
     *
     * @param CitizenProject $citizenProject The citizen project for which the memberships to fetch belong
     * @param string         $alias          The custom root alias for the query
     */
    private function createCitizenProjectMembershipsQueryBuilder(
        CitizenProject $citizenProject,
        string $alias = 'cpm'
    ): QueryBuilder {
        return $this->createQueryBuilder($alias)
            ->leftJoin($alias.'.adherent', 'a')
            ->where($alias.'.citizenProject = :citizenProject')
            ->orderBy($alias.'.privilege', 'DESC')
            ->addOrderBy('a.firstName', 'ASC')
            ->setParameter('citizenProject', $citizenProject)
        ;
    }

    /**
     * Returns the list of all citizen project memberships of a citizen project.
     */
    public function findCitizenProjectMemberships(CitizenProject $citizenProject): CitizenProjectMembershipCollection
    {
        $query = $this
            ->createCitizenProjectMembershipsQueryBuilder($citizenProject)
            ->addSelect('a')
            ->addSelect('st')
            ->leftJoin('a.subscriptionTypes', 'st')
            ->getQuery()
        ;

        return new CitizenProjectMembershipCollection($query->getResult());
    }

    /**
     * Creates an AdherentCollection instance with the results of a Query.
     *
     * The query must return a list of CitizenProjectMembership entities.
     *
     * @param Query $query The query to execute
     */
    private function createAdherentCollection(Query $query): AdherentCollection
    {
        return new AdherentCollection(
            array_map(
                function (CitizenProjectMembership $membership) {
                    return $membership->getAdherent();
                },
                $query->getResult()
            )
        );
    }
}
