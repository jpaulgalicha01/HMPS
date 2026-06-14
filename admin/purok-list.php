<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Purok/Sitio List';
$_SESSION['Active_Navigate'] = 'Purok/Sitio List';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>



<div class="container-fluid pt-2">
    <div class="row gap-lg-0 gap-3">
        <div class="col-lg-4 col-12 order-lg-1 order-2">
            <div class="card" style="height: 250px;">
                <h5 class="card-header">Add Purok/Sitio</h5>
                <div class="card-body">
                    <form id="frmSubmitPurok">
                        <input id="PurokUniqueId" name="PurokUniqueId" type="hidden" />
                        <div class="mb-3">
                            <label for="PurokName" class="form-label">Purok Name/Sitio Name</label>
                            <input type="text" class="form-control text-uppercase" id="PurokName" name="PurokName" placeholder="Enter Purok/Sitio Name" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-danger  btn-rounded" id="btdnResetPurokForm"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded" id="btnAddPurok"><i class="fas fa-plus"></i> Add Purok/Sitio</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12 order-lg-2 order-1">
            <div class="card">
                <h5 class="card-header">Purok List</h5>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="PurokTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script src="js/purok/sitio-name.js" defer></script>
    <?php include_once './includes/footer.php'; ?>