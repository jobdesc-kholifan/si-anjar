<?php

class DBMenus
{

    const master = '#master';
    const masterUsers = 'masters/users';
    const masterRole = 'masters/type/' . DBTypes::roleAdministrator;
    const masterCategoryProject = 'masters/type/' . DBTypes::categoryProject;
    const masterBank = 'masters/bank';

    const addresses = '#addresses';
    const addressesProvince = 'addresses/province';
    const addressesCity = 'addresses/city';

    const security = '#security';
    const securityMenu = 'security/menu';
    const securityPrivileges = 'security/privileges';
}
