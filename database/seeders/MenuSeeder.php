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
        /* MASTER */
        $sequence = 0;
        $masters = $this->menus->create([
            'name' => 'Master Data',
            'icon' => '<i class="fa fa-database"></i>',
            'slug' => \DBMenus::master,
            'sequence' => $sequence + 10,
        ]);
        $this->feature($masters, [DBFeature::view]);

        $sequenceMaster = 0;
        $this->feature(
            $this->menus->create([
                'parent_id' => $masters->id,
                'name' => 'Bank',
                'slug' => \DBMenus::masterBank,
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

        /* MASTER ADDRESSES */
        $sequenceAddresses = 0;
        $addresses = $this->menus->create([
            'parent_id' => $masters->id,
            'name' => 'Alamat',
            'slug' => \DBMenus::addresses,
            'sequence' => $sequenceMaster + 10,
        ]);
        $this->feature($addresses, [DBFeature::view]);

        $this->feature(
            $this->menus->create([
                'parent_id' => $addresses->id,
                'name' => 'Provinsi',
                'slug' => \DBMenus::addressesProvince,
                'sequence' => $sequenceAddresses + 10,
            ])
        );

        $this->feature(
            $this->menus->create([
                'parent_id' => $addresses->id,
                'name' => 'Kota / Kabupaten',
                'slug' => \DBMenus::addressesCity,
                'sequence' => $sequenceAddresses + 10,
            ])
        );
        /* END OF MASTER ADDRESSES */
        /* END OF MASTER */

        /* MANAGEMENT USERS */
        $sequenceUsers = 0;
        $users = $this->menus->create([
            'name' => 'Manajemen Pengguna',
            'icon' => '<i class="fa fa-database"></i>',
            'slug' => \DBMenus::users,
            'sequence' => $sequence + 10,
        ]);
        $this->feature($users, [DBFeature::view]);

        $this->feature(
            $this->menus->create([
                'parent_id' => $users->id,
                'name' => 'Pengguna',
                'slug' => \DBMenus::usersUser,
                'sequence' => $sequenceUsers + 10,
            ])
        );

        $this->feature(
            $this->menus->create([
                'parent_id' => $users->id,
                'name' => 'Role',
                'slug' => \DBMenus::usersRole,
                'sequence' => $sequenceUsers + 10,
            ]),
        );
        /* END OF MANAGEMENT USERS */

        /* INVESTOR */
        $investor = $this->menus->create([
            'name' => 'Investor',
            'icon' => '<i class="fa fa-database"></i>',
            'slug' => \DBMenus::investor,
            'sequence' => $sequence + 10,
        ]);
        $this->feature($investor);
        /* END OF INVESTOR */

        /* PROJECT */
        $project = $this->menus->create([
            'name' => 'Proyek',
            'icon' => '<i class="fa fa-database"></i>',
            'slug' => \DBMenus::project,
            'sequence' => $sequence + 10,
        ]);
        $this->feature($project);
        /* END OF PROJECT */

        /* SETTINGS */
        $security = $this->menus->create([
            'name' => 'Pengaturan',
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
            ]),
            null,
            function($menu) {
                return [
                    ['menu_id' => $menu->id, 'title' => 'Tambah Fitur', 'slug' => 'create-feature', 'description' => 'User dapat menambahkan fitur dari menu', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                    ['menu_id' => $menu->id, 'title' => 'Ubah Fitur', 'slug' => 'update-feature', 'description' => 'User dapat mengubah fitur dari menu', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                    ['menu_id' => $menu->id, 'title' => 'Hapus Fitur', 'slug' => 'delete-feature', 'description' => 'User dapat menghapus fitur dari menu', 'created_at' => currentDate(), 'updated_at' => currentDate()],
                ];
            }
        );
        /* END OF SETTINGS */
    }

    public function feature($menu, $features = null, $callback = null)
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

        if(is_callable($callback)) {
            $insertFeatures = array_merge($insertFeatures, call_user_func_array($callback, [$menu]));
        }

        $this->menusFeature->insert($insertFeatures);
    }
}
