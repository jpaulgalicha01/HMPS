<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Family List';
$_SESSION['Active_Navigate'] = 'Family List';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<div class="container-fluid pt-2">
    <div class="row gap-lg-0 gap-3">
        <div class="col-lg-4 col-12 order-lg-1 order-2">
            <div class="card" style="height: 250px;">
                <h5 class="card-header">Add Family List</h5>
                <div class="card-body">
                    <form id="frmSubmitFamilyList">
                        <input id="FamilyUniqueId" name="FamilyUniqueId" type="hidden" />
                        <div class="mb-3">
                            <label for="FamilyName" class="form-label">Family Name</label>
                            <input type="text" class="form-control" id="FamilyName" name="FamilyName" placeholder="Enter Family Name" required>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-danger  btn-rounded" id="btdnResetFamilyForm"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded" id="btnAddFamily"><i class="fas fa-plus"></i> Add Family</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12 order-lg-2 order-1">
            <div class="card">
                <h5 class="card-header">Family List</h5>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="familyTable">
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <script src="js/family-list.js" defer></script>
    <?php include_once './includes/footer.php'; ?>