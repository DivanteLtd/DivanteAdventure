<?php
/**
 * Created by PhpStorm.
 * User: pawel
 * Date: 15.03.19
 * Time: 10:06
 */

namespace Divante\Bundle\AdventureBundle\Mappers;

use Divante\Bundle\AdventureBundle\Message\Employee\UpdateEmployee;
use Symfony\Component\HttpFoundation\Request;

class EmployeeRequestMapper
{

    protected Request $request;

    /**
     * @param Request $request
     * @return UpdateEmployee
     * @throws \Exception
     */
    public function mapToMessage(Request $request) : UpdateEmployee
    {
        $this->request = $request;
        return new UpdateEmployee(
            $this->getIdFromRequest(),
            $this->getFirstNameFromRequest(),
            $this->getLastNameFromRequest(),
            $this->getEmailFromRequest(),
            $this->getPhotoUrlFromRequest(),
            $this->getPrivatePhoneFromRequest(),
            $this->getPhoneFromRequest(),
            $this->getCityFromRequest(),
            $this->getPostalCodeFromRequest(),
            $this->getStreetFromRequest(),
            $this->getCountryFromRequest(),
            $this->getContractIdFromRequest(),
            $this->getHiredAtFromRequest(),
            $this->getLicencePlatFromRequest(),
            $this->getManagerFromRequest(),
            $this->getPositionIdFromRequest(),
            $this->getLevelIdFromRequest(),
            $this->getWorkModeFromRequest(),
            $this->getTribeIdFromRequest(),
            $this->getFriendFirstNameFromRequest(),
            $this->getFriendLastNameFromRequest(),
            $this->getFriendAddressFromRequest(),
            $this->getFriendPhoneFromRequest(),
            $this->getGenderFromRequest(),
            $this->getRoleFromRequest(),
            $this->getJobTimeValueFromRequest(),
            $this->getPin(),
            $this->getOldPin(),
            $this->getDateOfBirth(),
            $this->getChildCare(),
            $this->getPreferredLanguage(),
            $this->getNip(),
            $this->getFirmName(),
            $this->getFirmAddress(),
            $this->getDataUpdate(),
            $this->getEmployeeCode(),
            $this->isStudent(),
            $this->getTaxDeductibleCosts(),
            $this->getWorkStreet(),
            $this->getWorkCity(),
            $this->getWorkPostalCode(),
            $this->getWorkCountry(),
            $this->getFinanceCode(),
            $this->getSuperiorEmail(),
            $this->getShoeSize(),
            $this->getSweatshirtSize(),
            $this->getShirtSize(),
            $this->getSubTypeContract()
        );
    }

    protected function getCityFromRequest() :?string
    {
        return $this->request->get('city', null);
    }

    protected function getPostalCodeFromRequest() :?string
    {
        return $this->request->get('postalCode', null);
    }

    protected function getStreetFromRequest() :?string
    {
        return $this->request->get('street', null);
    }

    protected function getCountryFromRequest() :?string
    {
        return $this->request->get('country', null);
    }

    protected function getContractIdFromRequest() :?int
    {
        $contract = $this->request->get('contract', null);
        if (is_int($contract)) {
            return $contract;
        } elseif (!is_null($contract) && isset($contract['id'])) {
            return (integer)$contract['id'];
        }
        return null;
    }

    protected function getEmailFromRequest() :?string
    {
        return $this->request->get('email', null);
    }

    /**
     * @return \DateTime|null
     * @throws \Exception
     */
    protected function getHiredAtFromRequest() :?\DateTime
    {
        $hiredAt = $this->request->get('hiredAt', null);
        if (!is_null($hiredAt)) {
            return new \DateTime($hiredAt);
        }

        return null;
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function getIdFromRequest() :?int
    {
        $id = $this->request->get('id');
        if (is_null($id)) {
            throw new \Exception('"id" is required');
        }
        return (int)$id;
    }

    protected function getFirstNameFromRequest() :?string
    {
        $fname = $this->request->get('name', null);
        if (!is_null($fname)) {
            return trim($fname);
        }
        return null;
    }

    protected function getLastNameFromRequest() :?string
    {
        $lname = $this->request->get('lastName', null);
        if (!is_null($lname)) {
            return trim($lname);
        }
        return null;
    }

    protected function getLicencePlatFromRequest() :?string
    {
        $lplate = $this->request->get('licencePlate', null);
        if (!is_null($lplate)) {
            return trim($lplate);
        }
        return null;
    }

    protected function getPhoneFromRequest() :?string
    {
        $phone = $this->request->get('phone', null);
        if (!is_null($phone)) {
            return trim($phone);
        }
        return null;
    }

    protected function getPhotoUrlFromRequest() :?string
    {
        $url = $this->request->get('photo', null);
        if (!is_null($url)) {
            return trim($url);
        }
        return null;
    }

    protected function getPositionIdFromRequest() :?int
    {
        $position = $this->request->get('position', null);
        if (is_int($position)) {
            return $position;
        } elseif (!is_null($position) && isset($position['id'])) {
            return (int)$position['id'];
        }
        return null;
    }

    protected function getLevelIdFromRequest() : ?int
    {
        $level = $this->request->get('level', null);
        if (is_int($level)) {
            return $level;
        } elseif (!is_null($level) && isset($level['id'])) {
            return (int)$level['id'];
        }
        return null;
    }

    protected function getPrivatePhoneFromRequest() :?string
    {
        $privatePhone = $this->request->get('privatePhone', null);
        if (!is_null($privatePhone)) {
            return trim($privatePhone);
        }
        return null;
    }

    protected function getWorkModeFromRequest() :?int
    {
        return $this->request->get('workMode', null);
    }

    protected function getTribeIdFromRequest() :?int
    {
        $tribe = $this->request->get('tribe', null);
        if (is_int($tribe)) {
            return $tribe;
        } elseif (!is_null($tribe) && isset($tribe['id'])) {
            return (int)$tribe['id'];
        }
        return null;
    }

    protected function getManagerFromRequest() :?bool
    {
        $manager = $this->request->get('manager', null);
        if (!is_null($manager)) {
            return (bool)$manager;
        }
        return null;
    }

    protected function getGenderFromRequest() :?int
    {
        $gender = $this->request->get('gender', null);
        if (!is_null($gender)) {
            return (int)$gender;
        }
        return null;
    }

    protected function getFriendFirstNameFromRequest() :?string
    {
        $emergencyName = $this->request->get('emergencyFirstName', null);
        if (!is_null($emergencyName)) {
            return trim($emergencyName);
        }
        return null;
    }

    protected function getFriendLastNameFromRequest() :?string
    {
        $emergencyLastName = $this->request->get('emergencyLastName', null);
        if (!is_null($emergencyLastName)) {
            return trim($emergencyLastName);
        }
        return null;
    }

    protected function getFriendAddressFromRequest() :?string
    {
        $address =  $this->request->get('emergencyAddress', null);
        if (!is_null($address)) {
            return trim($address);
        }
        return null;
    }

    protected function getFriendPhoneFromRequest() :?string
    {
        $phone = $this->request->get('emergencyPhone', null);
        if (!is_null($phone)) {
            return trim($phone);
        }
        return null;
    }

    /** @return string[]|null */
    protected function getRoleFromRequest() : ?array
    {
        /** @var string|string[]|null $perm */
        $perm = $this->request->get('roles', null);
        if (is_null($perm)) {
            return null;
        } elseif (is_array($perm)) {
            return $perm;
        } else {
            return [ $perm ];
        }
    }

    protected function getJobTimeValueFromRequest() : ?float
    {
        $jobTimeValue = $this->request->get('jobTimeValue', null);
        return is_null($jobTimeValue) ? null : (float)$jobTimeValue;
    }

    protected function getPin() : ?string
    {
        /** @var string|null $pin */
        $pin = $this->request->get('pin', null);
        if (!is_null($pin)) {
            return trim($pin);
        }
        return $pin;
    }

    protected function getOldPin() : ?string
    {
        /** @var string|null $oldPin */
        $oldPin = $this->request->get('oldPin', null);
        if (!is_null($oldPin)) {
            return trim($oldPin);
        }
        return $oldPin;
    }

    /**
     * @return \DateTime|null
     * @throws \Exception
     */
    protected function getDateOfBirth() : ?\DateTime
    {
        $dateOfBirth = $this->request->get('dateOfBirth', null);
        if (!is_null($dateOfBirth)) {
            return new \DateTime($dateOfBirth);
        }
        return null;
    }

    protected function getChildCare() : ?int
    {
        /** @var int|null $childCare */
        $childCare = $this->request->get('childCare');
        return $childCare;
    }

    protected function getPreferredLanguage() : ?string
    {
        return $this->request->get('language', null);
    }

    protected function getNip() :?string
    {
        return $this->request->get('nip', null);
    }

    protected function getFirmName() :?string
    {
        return $this->request->get('firmName', null);
    }

    protected function getFirmAddress() :?string
    {
        return $this->request->get('firmAddress', null);
    }

    /**
     * @return \DateTime|null
     * @throws \Exception
     */
    protected function getDataUpdate() :?\DateTime
    {
        $dataUpdate = $this->request->get('dataUpdate', null);
        if (!is_null($dataUpdate)) {
            return new \DateTime($dataUpdate);
        }
        return null;
    }

    protected function getEmployeeCode() :?string
    {
        return $this->request->get('employee_code', null);
    }

    protected function isStudent() :?bool
    {
        return $this->request->get('student', null);
    }

    protected function getTaxDeductibleCosts() :?int
    {
        return $this->request->get('taxDeductibleCosts', null);
    }

    protected function getWorkStreet() :?string
    {
        return $this->request->get('workStreet', null);
    }

    protected function getWorkCity() :?string
    {
        return $this->request->get('workCity', null);
    }

    protected function getWorkPostalCode() :?string
    {
        return $this->request->get('workPostalCode', null);
    }

    protected function getWorkCountry() :?string
    {
        return $this->request->get('workCountry', null);
    }

    protected function getFinanceCode() :?string
    {
        return $this->request->get('financeCode', null);
    }

    protected function getSuperiorEmail() :?string
    {
        return $this->request->get('superiorEmail', null);
    }

    public function getShoeSize(): ?string
    {
        return $this->request->get('shoeSize', null);
    }


    public function getSweatshirtSize(): ?string
    {
        return $this->request->get('sweatshirtSize', null);
    }

    public function getShirtSize(): ?string
    {
        return $this->request->get('shirtSize', null);
    }

    public function getSubTypeContract() :?string
    {
        return $this->request->get('subTypeContract', null);
    }
}
