<?php

namespace App\Documents\Excel;

use App\Helpers\Collections\Banks\BankCollection;
use App\Helpers\Collections\Config\ConfigCollection;
use App\Helpers\Collections\Investors\InvestorCollection;
use App\Models\Investors\Investor;
use App\Models\Investors\InvestorBank;
use App\Models\Masters\Bank;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Support\Facades\DB;
use PhpOffice\PhpSpreadsheet\Shared\Date;

class ImportInvestor
{

    /* @var InvestorCollection[] */
    protected $items = [];

    /* @var Investor|Relation */
    protected $investor;

    /* @var InvestorBank|Relation */
    protected $investorBank;

    /* @var Bank|Relation */
    protected $bank;

    public function __construct(array $arrays)
    {
        $this->items = collect($arrays)->except(0);

        $this->investor = new Investor();
        $this->investorBank = new InvestorBank();
        $this->bank = new Bank();

        $this->map();
    }

    private function map()
    {
        $items = [];

        if(count($this->items->get(1)) < 21)
            throw new \Exception("Format template tidak sesuai. Cek kembali format excel anda agar data dapat diproses oleh sistem", \DBCodes::authorizedError);

        $parentConfig = findConfig()->in([
            \DBTypes::gender,
            \DBTypes::religion,
            \DBTypes::relationship
        ]);

        $configs = [];
        $banks = [];

        foreach($this->items->toArray() as $item) {
            list(
                $id, $investorName, $email, $phone, $phoneAlternative, $address,
                $noKTP, $npwp, $pob, $dob, $gender, $religion, $relationship, $jobName,
                $bankCode, $bankName, $branchName, $noRekening, $onBehalf,
                $emergencyName, $emergencyPhone, $emergencyRelationship
            ) = $item;

            if(!empty($bankCode)) {
                if(!array_key_exists($bankCode, $banks)) {
                    $queryBank = $this->bank->where(DB::raw('TRIM(LOWER(bank_code))'), trim(strtolower($bankCode)))
                        ->first();

                    $bank = new BankCollection($queryBank);
                    if (is_null($queryBank)) {
                        $bank = BankCollection::create([
                            'bank_code' => $bankCode,
                            'bank_name' => $bankName,
                        ]);
                    }

                    $banks[$bankCode] = $bank;
                }
            }

            $genderKey = configKey($gender);
            if(!empty($genderKey) && !array_key_exists($genderKey, $configs)) {
                $configs[$genderKey] = findConfig()->in($genderKey)
                    ->get($genderKey, function() use ($parentConfig, $genderKey, $gender) {
                        return ConfigCollection::create([
                            'parent_id' => $parentConfig->get(\DBTypes::gender)->getId(),
                            'slug' => $genderKey,
                            'name' => preg_replace('/\s+/', '', $gender),
                        ]);
                    });
            } else if (!empty($gender)) $configs[$genderKey] = new ConfigCollection();

            $religionKey = configKey($religion);
            if(!empty($religionKey) && !array_key_exists($religionKey, $configs)) {
                $configs[$religionKey] = findConfig()->in($religionKey)
                    ->get($religionKey, function() use ($parentConfig, $religionKey, $religion) {
                        return ConfigCollection::create([
                            'parent_id' => $parentConfig->get(\DBTypes::religion)->getId(),
                            'slug' => $religionKey,
                            'name' => trim($religion),
                        ]);
                    });
            } else if (!empty($gender)) $configs[$religionKey] = new ConfigCollection();

            $relationshipKey = configKey($relationship);
            if(!empty($relationshipKey) && !array_key_exists($relationshipKey, $configs)) {
                $configs[$relationshipKey] = findConfig()->in($relationshipKey)
                    ->get($relationshipKey, function() use ($parentConfig, $relationshipKey, $relationship) {
                        return ConfigCollection::create([
                            'parent_id' => $parentConfig->get(\DBTypes::relationship)->getId(),
                            'slug' => $relationshipKey,
                            'name' => trim($relationship),
                        ]);
                    });
            } else if (!empty($gender)) $configs[$relationshipKey] = new ConfigCollection();

            if(!empty($investorName)) {
                $items[] = new InvestorCollection([
                    'id' => $id,
                    'investor_name' => $investorName,
                    'email' => $email,
                    'phone_number' => $phone,
                    'phone_number_alternative' => $phoneAlternative,
                    'address' => $address,
                    'no_ktp' => $noKTP,
                    'npwp' => $npwp,
                    'place_of_birth' => $pob,
                    'date_of_birth' => dbDate($dob),
                    'gender_id' => $configs[$genderKey]->getId(),
                    'religion_id' => $configs[$religionKey]->getId(),
                    'relationship_id' => $configs[$relationshipKey]->getId(),
                    'job_name' => $jobName,
                    'emergency_name' => $emergencyName,
                    'emergency_phone_number' => $emergencyPhone,
                    'emergency_relationship' => $emergencyRelationship,
                    'banks' => [
                        [
                            'bank_id' => $banks[$bankCode]->getId(),
                            'branch_name' => $branchName,
                            'no_rekening' => $noRekening,
                            'atas_nama' => $onBehalf
                        ]
                    ]
                ]);
            }
        }

        $this->items = $items;
    }

    public function save()
    {
        foreach($this->items as $item) {

            $investorValues = [
                'investor_name' => $item->getName(),
                'email' => $item->getEmail(),
                'phone_number' => $item->getPhoneNumber(),
                'phone_number_alternative' => $item->getPhoneNumberAlternative(),
                'address' => $item->getAddress(),
                'no_ktp' => $item->getNoKTP(),
                'npwp' => $item->getNPWP(),
                'place_of_birth' => $item->getPlaceOfBirth(),
                'date_of_birth' => $item->getDateOfBirth(),
                'gender_id' => $item->getGenderId(),
                'religion_id' => $item->getReligionId(),
                'relationship_id' => $item->getRelationshipId(),
                'job_name' => $item->getJobName(),
                'emergency_name' => $item->getEmergencyName(),
                'emergency_phone_number' => $item->getEmergencyPhoneNumber(),
                'emergency_relationship' => $item->getEmergencyRelationship(),
            ];

            /**
             * Create data investor
             * */
            if(is_null($item->getId())) {
                $investor = InvestorCollection::create($investorValues);

                foreach($item->getBanks()->all() as $bank) {

                    $this->investorBank->create([
                        'investor_id' => $investor->getId(),
                        'bank_id' => $bank->getBankId(),
                        'branch_name' => $bank->getBranchName(),
                        'no_rekening' => $bank->getNoRekening(),
                        'atas_nama' => $bank->getOnBehalfOf(),
                    ]);
                }
            } else {
                $investor = $this->investor->find($item->getId());
                if(!is_null($investor)) {
                    $investor->update($investorValues);
                    foreach($item->getBanks()->all() as $bank) {
                        $this->investorBank->where('investor_id', $item->getId())
                            ->update([
                                'bank_id' => $bank->getBankId(),
                                'branch_name' => $bank->getBranchName(),
                                'no_rekening' => $bank->getNoRekening(),
                                'atas_nama' => $bank->getOnBehalfOf(),
                            ]);
                    }
                }
            }
        }
    }
}
