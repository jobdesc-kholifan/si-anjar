<?php

namespace App\Helpers\Collections\Users;

use App\Helpers\Collections\Collection;
use App\Helpers\Collections\Config\ConfigCollection;
use App\Models\Authorization\User;

class UserCollection extends Collection
{

    /**
     * @return UserCollection
     * */
    static public function session()
    {
        $user = new User();
        $row = $user->defaultWith($user->defaultSelects)
            ->find(auth()->id());

        return new UserCollection($row);
    }

    public function getId()
    {
        return $this->get('id');
    }

    public function getFullName()
    {
        return $this->get('full_name');
    }

    public function getEmailAddress()
    {
        return $this->get('email');
    }

    public function getPhoneNumber()
    {
        return $this->get('phone_number');
    }

    public function getDesc()
    {
        return $this->get('description');
    }

    public function getUserName()
    {
        return $this->get('user_name');
    }

    public function getRoleId()
    {
        return $this->get('role_id');
    }

    /**
     * @return ConfigCollection
     * */
    public function getRole()
    {
        if($this->hasNotEmpty('role'))
            return new ConfigCollection($this->get('role'));

        return new ConfigCollection();
    }

    /**
     * @return ConfigCollection
     * */
    public function getStatus()
    {
        if($this->hasNotEmpty('status'))
            return new ConfigCollection($this->get('status'));

        return new ConfigCollection();
    }
}
