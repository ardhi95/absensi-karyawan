<?= $this->extend('layouts/app') ?>

<?= $this->section('content') ?>
    <div class="container-fluid">
        <div class="d-sm-flex flex-column mb-4">
            <h1 class="h3 mb-3 text-gray-800">QR</h1>
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="/admin/dashboard">Home</a></li>
                    <li class="breadcrumb-item active" aria-current="page">QR
                    </li>
                </ol>
            </nav>
        </div>

        <?php if (session()->getFlashData('success_job')) : ?>
            <div class="alert alert-success" role="alert">
                <?php echo session("success_job") ?>
            </div>
        <?php endif; ?>

        <!-- DataTales Example -->
        <div class="card shadow mb-4">
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped" id="table">
                        <thead>
                        <tr>
                            <th>No.</th>
                            <th>Content</th>
                            <th>File</th>
                            <th>Action</th>
                        </tr>
                        </thead>

                        <tbody>
                        <?php $i = 1; ?>
                        <?php foreach ($qr as $q) : ?>
                            <tr>
                                <td><?= $i++ ?></td>
                                <td><?= $q['content'] ?></td>
                                <td><?= $q['file'] ?></td>
                                <td>
                                    <button class="btn btn-warning btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#editModal<?= $q['qrId'] ?>">
                                        <i class="bi bi-pen"></i>
                                    </button>
                                    <button class="btn btn-danger btn-sm" data-bs-toggle="modal"
                                        data-bs-target="#deleteModal<?= $q['qrId'] ?>">
                                        <i class="bi bi-trash-fill"></i>
                                    </button>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Modal Edit -->
        <?php foreach ($qr as $q) : ?>
            <div class="modal fade" id="editModal<?= $q['qrId'] ?>" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h3>Are you sure?</h3>
                            Edit
                            </br>
                            <div class="pt-3">
                                <button type="button" class="btn btn-secondary m-2" data-bs-dismiss="modal">No</button>
                                <form class="d-inline" method="post"
                                      action="<?= base_url(); ?>/admin/job/delete/<?= $q['qrId'] ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger m-2">Yes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

        <!-- Modal Delete -->
        <?php foreach ($qr as $q) : ?>
            <div class="modal fade" id="deleteModal<?= $q['qrId'] ?>" data-bs-backdrop="static" tabindex="-1">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-body text-center">
                            <h3>Are you sure?</h3>
                            Do you really delete this data?
                            </br>
                            <div class="pt-3">
                                <button type="button" class="btn btn-secondary m-2" data-bs-dismiss="modal">No</button>
                                <form class="d-inline" method="post"
                                      action="<?= base_url(); ?>/admin/job/delete/<?= $q['qrId'] ?>">
                                    <?= csrf_field() ?>
                                    <input type="hidden" name="_method" value="DELETE">
                                    <button type="submit" class="btn btn-danger m-2">Yes</button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>

    </div>
<?= $this->endSection() ?>

<?= $this->section('scripts') ?>
    <script type="text/javascript">
        $(document).ready(function () {
            $('#table').DataTable({
                "dom": `Q
                <'row mt-3'
                    <'col-sm-12 col-md-4'l>
                    <'col-sm-12 col-md-8'
                        <'row'
                            <'col-sm-12 col-md-9'f>
                            <'col-sm-12 col-md-3'B>
                        >
                    >
                >
                ` +
                    "<'row'<'col-sm-12'tr>>" +
                    "<'row'<'col-sm-12 col-md-5'i><'col-sm-12 col-md-7'p>>",
                "responsive": true,
                "paging": true,
                "ordering": true,
                "info": true,
                "buttons": [
                    'excel',
                    {
                        text: 'Create',
                        action: function (e, dt, node, config) {
                            window.location.href = '/admin/qr/form'
                        }
                    }
                ]
            });
        });

        $(".alert").fadeTo(2000, 500).slideUp(500, function () {
            $(".alert").slideUp(500);
        });
    </script>
<?= $this->endSection() ?>