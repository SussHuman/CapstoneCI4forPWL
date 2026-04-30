<?= $this->extend('layout') ?>
<?= $this->section('content') ?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">Profile Information</h5>

        <table class="table table-borderless">
        <tbody>
            <tr>
                <th class="text-primary" style="width: 200px">Username</th>
                    
                <td>
                    <?= session()->get('username') ?>
                    <span class="badge bg-danger">
                        <?= session()->get('role') ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th class="text-primary">Email</th>
                <td class="text-primary"><?= session()->get('email') ?></td>
            </tr>
            <tr>
                <th class="text-primary">Login Time</th>
                <td><?= session()->get('login_time') ?></td>
            </tr>
            <tr>
                <th class="text-primary">Status</th>
                <td>
                    <span class="badge bg-success">
                        <i class="bi bi-check-circle me-1"></i>
                        <?= session()->get('status') ?>
                    </span>
                </td>
            </tr>
        </tbody>

        </table>
    </div>
</div>

<?= $this->endSection() ?>