<?php

namespace Divante\Bundle\AdventureBundle\Entity\Data;

/**
 * Interface ContractOwner is used to find contract type in Employee and PotentialEmployee.
 * @package Divante\Bundle\AdventureBundle\Entity\Data
 */
interface ContractOwner
{
    public function getContractType() : ?string;
    public function getName() : string;
    public function getLastName() : string;
}
