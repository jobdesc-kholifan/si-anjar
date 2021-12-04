<?php

namespace Database\Seeders;

use App\Models\Menus\Menu;
use App\Models\Menus\MenuFeature;
use DBFeature;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Database\Seeder;

class MenuSeeder extends Seeder
{

    /* @var Menu|Relation */
    protected $menus;

    /* @var MenuFeature|Relation */
    protected $menusFeature;

    public function __construct()
    {
        $this->menus = new Menu();
        $this->menusFeature = new MenuFeature();
    }

    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $sequence = 0;
        $masters = $this->menus->create([
            'name' => 'Master',
            'icon' => '<i class="fa fa-database"></i>',
            'slug' => \DBMenus::master,
            'sequence' => $sequence + 10,
        ]);
        $this->feature($masters, [DBFeature::view]);

        $sequenceMaster = 0;
        $this->feature(
            $this->menus->create([
                'parent_id' => $masters->id,
                'name' => 'Role',
                'slug' => \DBMenus::masterRole,
                'sequence' => $sequenceMaster + 10,
            ])
        );

        $this->feature(
            $this->menus->create([
                'parent_id' => $masters->id,
                'name' => 'Users',
                'slug' => \DBMenus::masterUsers,
                'sequence' => $sequenceMaster + 10,
            ])
        );

        $this->feature(
            $this->menus->create([
                'parent_id' => $masters->id,
                'name' => 'Kategori Proyek',
                'slug' => \DBMenus::masterCategoryProject,
                'sequence' => $sequenceMaster + 10,
            ])
        );

        $security = $this->menus->create([
            'name' => 'Keamanan',
            'icon' => '<i class="fa fa-database"></i>',
            'slug' => \DBMenus::security,
            'sequence' => $sequence + 10,
        ]);
        $this->feature($security, [DBFeature::view]);

        $sequenceSecurity = 0;
        $this->feature(
            $this->menus->create([
                'parent_id' => $security->id,
                'name' => 'Menu',
                'slug' => \DBMenus::securityMenu,
                'sequence' => $sequenceSecurity + 10,
            ])
        );

        $this->feature(
            $this->menus->create([
                'parent_id' => $security->id,
                'name' => 'Hak Akses',
                'slug' => \DBMenus::securityPrivileges,
                'sequence' => $sequenceSecurity + 10,
            ]),
            [DBFeature::view, DBFeature::update]
        );
    }

    public function feature($menu, $features = null)
    {
        $insertFeatures = [];

        if(!is_null($features)) {
            if (in_array(DBFeature::view, $features))
                $insertFeatures[] = ['menu_id' => $menu->id, 'title' => 'Tampil', 'slug' => DBFeature::view, 'description' => 'User dapat melihat data', 'created_at' => currentDate(), 'updated_at' => currentDate()];

            if (in_array(DBFeature::create, $features))
                $insertFeatures[] = ['menu_id' => $menu->id, 'title' => 'Tambah', 'slug' => DBFeature::create, 'description' => 'User dapat melihat data', 'created_at' => currentDate(), 'updated_at' => currentDate()];

            if (in_array(DBFeature::update, $features))
                $insertFeatures[] = ['menu_id' => $menu->id, 'title' => 'Ubah', 'slug' => DBFeature::update, 'description' => 'User dapat melihat data', 'created_at' => currentDate(), 'updated_at' => currentDate()];

            if (in_array(DBFeature::delete, $features))
                $insertFeatures[] = ['menu_id' => $menu->id, 'title' => 'Hapus', 'slug' => DBFeature::delete, 'description' => 'User dapat melihat data', 'created_at' => currentDate(), 'updated_at' => currentDate()];
        }

        else {
            $insertFeatures = [
                ['menu_id' => $menu->id, 'title' => 'Tampil', 'slug' => DBFeature::view, 'description' => 'User dapat melihat data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                ['menu_id' => $menu->id, 'title' => 'Tambah', 'slug' => DBFeature::create, 'description' => 'User dapat melakukan penambahan data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                ['menu_id' => $menu->id, 'title' => 'Ubah', 'slug' => DBFeature::update, 'description' => 'User dapat melakukan perubahan data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                ['menu_id' => $menu->id, 'title' => 'Hapus', 'slug' => DBFeature::delete, 'description' => 'User dapat menghapus data', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ];
        }

        $this->menusFeature->insert($insertFeatures);
    }
}
