<?php

namespace App\Helpers\Collections\Investors;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Config\ConfigCollection;
use App\Models\Investors\Investor;
use Illuminate\Database\Eloquent\Relations\Relation;

class InvestorCollection extends Collection
{

    /**
     * @param array $values
     * @return InvestorCollection
     * */
    static public function create($values)
    {
        /* @var Investor|Relation $investor */
        $investor = new Investor();
        return new InvestorCollection($investor->create($values));
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getName()
    {
        return $this->get('investor_name');
    }

    public function getEmail()
    {
        return $this->get('email');
    }

    public function getPhoneNumber()
    {
        return $this->get('phone_number');
    }

    public function getPhoneNumberAlternative()
    {
        return $this->get('phone_number_alternative');
    }

    public function getAddress()
    {
        return $this->get('address');
    }

    public function getNoKTP()
    {
        return $this->get('no_ktp');
    }

    public function getNPWP()
    {
        return $this->get('npwp');
    }

    public function getPlaceOfBirth()
    {
        return $this->get('place_of_birth');
    }

    public function getDateOfBirth()
    {
        return $this->get('date_of_birth');
    }

    public function getGenderId()
    {
        return $this->get('gender_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getGender()
    {
        if($this->hasNotEmpty('gender'))
            return new ConfigCollection($this->get('gender'));

        return new ConfigCollection();
    }

    public function getReligionId()
    {
        return $this->get('religion_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getReligion()
    {
        if($this->hasNotEmpty('religion'))
            return new ConfigCollection($this->get('religion'));

        return new ConfigCollection();
    }

    public function getRelationshipId()
    {
        return $this->get('relationship_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getRelationship()
    {
        if($this->hasNotEmpty('relationship'))
            return new ConfigCollection($this->get('relationship'));

        return new ConfigCollection();
    }

    public function getJobName()
    {
        return $this->get('job_name');
    }

    public function getEmergencyName()
    {
        return $this->get('emergency_name');
    }

    public function getEmergencyPhoneNumber()
    {
        return $this->get('emergency_phone_number');
    }

    public function getEmergencyRelationship()
    {
        return $this->get('emergency_relationship');
    }

    /**
     * @return InvestorBankArray
     * */
    public function getBanks()
    {
        if($this->hasNotEmpty('banks')) {
            return new InvestorBankArray($this->get('banks'));
        }

        return new InvestorBankArray([]);
    }
}
