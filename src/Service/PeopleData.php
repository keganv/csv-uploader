<?php

namespace Service;

use Doctrine\ORM\EntityManager;

class PeopleData
{
    public function __construct(EntityManager $em)
    {
        $this->em = $em;
    }

    public function getPeopleData($groupId = 0)
    {
        $groups = [];
        $q = $this->em
            ->getRepository('Entity\Person')
            ->createQueryBuilder('p')
            ->select('p.firstName, p.lastName, p.emailAddress, p.state, pgroup.id AS groupId, pgroup.name AS groupName')
            ->leftJoin('p.group', 'pgroup')
            ->orderBy('pgroup.id', 'ASC');

        if ($groupId) {
            return $q
                ->where('pgroup.id = :groupId')
                ->setParameter('groupId', $groupId)
                ->getQuery()
                ->getArrayResult();
        }

        $people = $q->getQuery()->getArrayResult();

        foreach($people as $person) {
            $groups[$person['groupName']][] = $person;
        }

        return $groups;
    }
}