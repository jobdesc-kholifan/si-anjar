<?php

/**
 * @var \App\Helpers\Collections\Projects\ProjectSurkasCollection $surkas
 * @var \App\Helpers\Collections\Projects\ProjectCollection $project
 * */

?>
<table style="border: 1px solid #000000;border-collapse: collapse">
    <thead>
    <tr>
        <th style="border: 1px solid #000000;font-weight: bold;">No</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Bank Tujuan</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Nomor Rekening</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Nominal</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Berita Transfer (Opsional)</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Email Penerima (Opsional)</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Nama Penerima (Opsional)</th>
        <th style="border: 1px solid #000000;font-weight: bold;">ID Unik Transaksi (Opsional)</th>
        <th style="border: 1px solid #000000;font-weight: bold;">Berita Transfer Tambahan (Opsional)</th>
    </tr>
    </thead>
    <tbody>
    @foreach($project->getInvestors()->all() as $i => $investor)
        <tr>
            <td style="border: 1px solid #000000;">{{ $i + 1 }}</td>
            <td style="border: 1px solid #000000;">{{ $investor->getInvestor()->getBanks()->first()->getBank()->getBankCode() }}</td>
            <td style="border: 1px solid #000000;">{{ $investor->getInvestor()->getBanks()->first()->getNoRekening() }}</td>
            <td style="border: 1px solid #000000;">{{ $investor->getSharesPercentage()/100 * $surkas->getSurkasValue() }}</td>
            <td style="border: 1px solid #000000;">{{ $surkas->getDesc() }}</td>
            <td style="border: 1px solid #000000;">{{ $investor->getInvestor()->getEmail() }}</td>
            <td style="border: 1px solid #000000;">{{ $investor->getInvestor()->getName() }}</td>
            <td style="border: 1px solid #000000;"></td>
            <td style="border: 1px solid #000000;">{{ $surkas->getOtherDesc() }}</td>
        </tr>
    @endforeach
    </tbody>
</table>
