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
            ['parent_id' => $type->id, 'slug' => DBTypes::roleSuperuser, 'name' => 'Superuser', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::roleAdministrator, 'name' => 'Administrator', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::categoryProject,
            'name' => 'Kategori Proyek',
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::gender,
            'name' => 'Jenis Kelamin',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::genderMan, 'name' => 'Laki-Laki', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::genderWoman, 'name' => 'Perempuan', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::relationship,
            'name' => 'Status Perkawinan',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::relationshipMarried, 'name' => 'Menikah', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::relationshipSingle, 'name' => 'Belum Menikah', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::religion,
            'name' => 'Agama',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'name' => 'Islam', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'name' => 'Kristen', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'name' => 'Hindu', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'name' => 'Buddha', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'name' => 'Katolik', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'name' => 'Konghucu', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::file,
            'name' => 'File',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::fileInvestorKTP, 'name' => 'Dokumen Investor KTP', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::fileInvestorNPWP, 'name' => 'Dokumen Investor NPWP', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::fileProjectProposal, 'name' => 'Dokumen Proposal Proyek', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::fileProjectBuktiTransfer, 'name' => 'Dokumen Bukti Transfer Proyek', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::fileProjectAttachment, 'name' => 'Dokumen Lampiran Proyek', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::fileSurkasAttachment, 'name' => 'Dokumen Lampiran Surkas', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::categoryProject,
            'name' => 'Kategori Proyek',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'name' => 'Podcast', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'name' => 'Hiburan', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::projectValue,
            'name' => 'Nilai Proyek',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::projectValueNominal, 'name' => 'Rp', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::projectValuePercentage, 'name' => '%', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::statusSK,
            'name' => 'Status SK',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::statusSKWaiting, 'name' => 'Menunggu Konfirmasi', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::statusSKApproved, 'name' => 'Telah Disetujui', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);

        $type = $this->config->create([
            'slug' => DBTypes::statusSurkas,
            'name' => 'Status Surkas',
        ]);

        $this->config->insert([
            ['parent_id' => $type->id, 'slug' => DBTypes::statusSurkasWaiting, 'name' => 'Menunggu Konfirmasi', 'created_at' => currentDate(), 'updated_at' => currentDate()],
            ['parent_id' => $type->id, 'slug' => DBTypes::statusSurkasApproved, 'name' => 'Telah Disetujui', 'created_at' => currentDate(), 'updated_at' => currentDate()],
        ]);
    }
}
