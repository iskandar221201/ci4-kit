<?= $this->extend('_layouts/main') ?>

<?= $this->section('content') ?>

<?= $this->include('_partials/page_header') ?>

<div x-data="dataTable('/api/users')">

    <div class="mb-4">
        <?= $this->include('_partials/search_bar', ['placeholder' => 'Cari nama atau email...']) ?>
    </div>

    <?= $this->include('_partials/datatable', [
        'columns' => [
            ['key' => 'username', 'label' => 'Nama'],
            ['key' => 'email', 'label' => 'Email'],
        ],
        'actions' => [
            ['label' => 'Detail', 'url' => "'/users/' + row.id"],
        ],
    ]) ?>

</div>
<?= $this->endSection() ?>
