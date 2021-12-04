<?php

namespace Database\Seeders;

use App\Models\Authorization\Privilege;
use App\Models\Menus\MenuFeature;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;

class PrivilegesSeeder extends Seeder
{

    /* @var MenuFeature|Relation */
    protected $menuFeature;

    /* @var Privilege|Relation */
    protected $privileges;

    public function __construct()
    {
        $this->menuFeature = new MenuFeature();
        $this->privileges = new Privilege();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        $types = findConfig()->in([\DBTypes::roleSuperuser]);

        $features = $this->menuFeature->get();

        $insertPrivileges = [];
        foreach($features as $feature) {
            $insertPrivileges[] = [
                'role_id' => $types->get(\DBTypes::roleSuperuser)->getId(),
                'menu_id' => $feature->menu_id,
                'menu_feature_id' => $feature->id,
                'has_access' => true,
                'created_at' => currentDate(),
                'updated_at' => currentDate(),
            ];
        }

        $this->privileges->insert($insertPrivileges);
    }
}
