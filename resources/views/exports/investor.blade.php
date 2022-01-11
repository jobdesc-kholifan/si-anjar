<?php

/**
 * @var \App\Helpers\Collections\Investors\InvestorCollection[] $data
 * */
?>
<table style="border: 1px solid #000000;border-collapse: collapse">
    <thead>
    <tr>
        <th style="border: 1px solid #000000;font-weight: bold;background-color: red">ID</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Nama Investor</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Email</th>
        <th style="border: 1px solid #000000;font-weight: bold;">No Handphone 1</th>
        <th style="border: 1px solid #000000;font-weight: bold;">No Handphone 2</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Alamat</th>
        <th style="border: 1px solid #000000;font-weight: bold;">No. KTP</th>
        <th style="border: 1px solid #000000;font-weight: bold;">NPWP</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Tempat Lahir</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Tanggal Lahir</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Jenis Kelamin</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Agama</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Status Perkawinan</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Pekerjaan</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Kode Bank</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Nama Bank</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Cabang</th>
        <th style="border: 1px solid #000000;font-weight: bold;">No. Rekening</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Atas Nama</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Nama (Darurat)</th>
        <th style="border: 1px solid #000000;font-weight: bold;">No. HP (Darurat)</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Hub Keluarga</th>
    </tr>
    </thead>
    <tbody>
    @foreach($data as $d)
        <tr>
            <td style="border: 1px solid #000000;background-color: red">{{ $d->getId() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getEmail() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getPhoneNumber() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getPhoneNumberAlternative() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getAddress() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getNoKTP() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getNPWP() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getPlaceOfBirth() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getDateOfBirth() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getGender()->getName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getReligion()->getName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getRelationship()->getName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getJobName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getBanks()->first()->getBank()->getBankCode() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getBanks()->first()->getBank()->getBankName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getBanks()->first()->getBranchName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getBanks()->first()->getNoRekening() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getBanks()->first()->getOnBehalfOf() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getEmergencyName() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getEmergencyPhoneNumber() }}</td>
            <td style="border: 1px solid #000000;">{{ $d->getEmergencyRelationship() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
