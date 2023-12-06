<!DOCTYPE html>
<html>
<head>
    <title>Белите Мечки</title>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">

    <link href="//cdn.jsdelivr.net/npm/bootstrap@5.3.1/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-4bw+/aepP/YC94hEpVNVgiZdgIC5+VKNBQNGCHeKRQN+PtmoHDEXuppvnDJzQIu9" crossorigin="anonymous">
    <link href="<?= base_url('css/fontawesome-free/css/all.min.css') ?>" rel="stylesheet" type="text/css">
    <link href="//fonts.googleapis.com/css?family=Nunito:200,200i,300,300i,400,400i,600,600i,700,700i,800,800i,900,900i" rel="stylesheet">
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/select2@4.0.13/dist/css/select2.min.css" />
    <link rel="stylesheet" href="//cdn.jsdelivr.net/npm/select2-bootstrap-5-theme@1.3.0/dist/select2-bootstrap-5-theme.min.css" />
    <link href="<?= base_url('css/libraries/datatables/dataTables.bootstrap4.min.css') ?>" rel="stylesheet">
    <link rel="stylesheet" href="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/css/bootstrap-datepicker.min.css" />
    <link rel="stylesheet" href="<?= base_url('libraries/bootstrap-select/css/bootstrap-select.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/sb-admin-2.css'); ?>">
    <link rel="stylesheet" href="<?= base_url('css/custom.css'); ?>">
</head>
<body>

<div id="wrapper">

    <!-- Content Wrapper -->
    <div id="content-wrapper" class="d-flex flex-column">

        <!-- Main Content -->
        <div id="content">

            <!-- Begin Page Content -->
            <div class="container-fluid" style="color: #1b1b1b">
                <?= $this->renderSection("content"); ?>
            </div>
            <!-- End Page Content -->

        </div>
        <!-- End of Main Content -->

    </div>
    <!-- End of Content Wrapper -->
</div>

<script src="<?= base_url('js/libraries/jquery/jquery.min.js'); ?>"></script>
<script src="<?= base_url('js/libraries/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<script src="//cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.10.0/js/bootstrap-datepicker.min.js"></script>
<script src="//cdn.jsdelivr.net/npm/select2@4.0.13/dist/js/select2.full.min.js"></script>
<script src="<?= base_url('js/libraries/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('js/libraries/datatables/dataTables.bootstrap4.min.js'); ?>"></script>
<script src="<?= base_url('js/libraries/jquery-validation/jquery.validate.min.js'); ?>"></script>
<script src="<?= base_url('libraries/bootstrap-select/js/bootstrap-select.js'); ?>"></script>
<script src="<?= base_url('js/sb-admin-2.js'); ?>"></script>
<script src="<?= base_url('js/core.js'); ?>"></script>

<?php if(isset($assets['js'])){ ?>
    <script src="<?= base_url('js/' . $assets['js']); ?>"></script>
<?php } ?>
</body>
</html>