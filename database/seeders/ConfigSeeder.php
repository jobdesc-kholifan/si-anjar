<?php

namespace Database\Seeders;

use App\Models\Masters\Config;
use DBTypes;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;

class ConfigSeeder extends Seeder
{

    /* @var Config|Relation */
    protected $config;

    public function __construct()
    {
        $this->config = new Config();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $type = $this->config->create([
            'slug' => DBTypes::status,
            'name' => 'Status Data',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::statusActive, 'name' => 'Aktif', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::statusNonactive, 'name' => 'Tidak Aktif', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::role,
            'name' => 'Role',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::roleAdministrator, 'name' => 'Administrator', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::categoryProject,
            'name' => 'Kategori Proyek',
        ]);
    }
}
