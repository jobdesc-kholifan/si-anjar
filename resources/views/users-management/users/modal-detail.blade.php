<?php

use App\Helpers\Collections\Config\ConfigCollection;
use App\Helpers\Collections\Users\UserCollection;

/* @var UserCollection $user */
/* @var ConfigCollection $type */

?>
<div class="modal-header">
    <h3 class="card-title">Informasi Detail</h3>
    <span class="close" data-dismiss="modal">&times;</span>
</div>
<div class="modal-body">
    <div class="list-information">
        <div class="row">
            <div class="col-sm-8">
                <dl class="row dt-bold">
                    <dt class="col-sm-4">Nama Lengkap</dt>
                    <dd class="col-sm-8">{{ $user->getFullName() }}</dd>
                    <dt class="col-sm-4">No. Telp</dt>
                    <dd class="col-sm-8">{{ $user->getPhoneNumber() }}</dd>
                    <dt class="col-sm-4">Email</dt>
                    <dd class="col-sm-8">{{ $user->getEmailAddress() }}</dd>
                    <dt class="col-sm-4">ID Pengguna</dt>
                    <dd class="col-sm-8">{{ $user->getUserName() }}</dd>
                    <dt class="col-sm-4">Role</dt>
                    <dd class="col-sm-8">{{ $user->getRole()->getName() }}</dd>
                    <dt class="col-sm-4">Deskripsi</dt>
                    <dd class="col-sm-8">{{ $user->getDesc() }}</dd>
                </dl>
            </div>
        </div>
    </div>
</div>
