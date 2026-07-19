<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Resident Messaging';
$_SESSION['Active_Navigate'] = 'Resident Messaging';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<div class="container-fluid pt-2">
    <div class="row">
        <div class="col-12 ">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center m-0">MESSAGING TARGET & TEMPLATES</h4>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-4">
                                <div class="card">
                                    <div class="card-header fw-bold">MESSAGE TEMPLATES</div>
                                    <div class="card-body">
                                        <form id="submitTemplateAlert">
                                            <input type="hidden" name="template_id" id="template_id" />
                                            <div class="mb-3">
                                                <label for="TemplateName" class="form-label">Template Name </i> <span class="text-danger">*</span></span></label>
                                                <input type="text" class="form-control text-uppercase" id="TemplateName" name="TemplateName" required>
                                            </div>
                                            <div class="mb-3">
                                                <label for="" class="form-label">Category <span class="text-danger">*</span></label>
                                                <select class="form-select" id="categoryID" name="categoryID">
                                                    <option disabled selected>Select Category Name</option>
                                                    <?php
                                                    $fetchCategory = new fetch();
                                                    $fetchCategory_res = $fetchCategory->getCategoryAll();
                                                    if ($fetchCategory_res->rowCount()) {
                                                        while ($fetchCategory_rows = $fetchCategory_res->fetch()) {
                                                    ?>
                                                            <option value="<?= $fetchCategory_rows["category_id"] ?>"><?= $fetchCategory_rows["category_name"] ?></option>
                                                    <?php
                                                        }
                                                    }
                                                    ?>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="" class="form-label">Category Level <span class="text-danger">*</span></span></label>
                                                <select class="form-select" id="CategoryLevel" name="CategoryLevel">
                                                    <option disabled selected> Select Category Level</option>
                                                </select>
                                            </div>
                                            <div class="mb-3">
                                                <label for="TemplateMessage" class="form-label">Compose Message </i> <span class="text-danger">*</span></label>
                                                <textarea class="form-control text-uppercase" id="TemplateMessage" name="TemplateMessage" rows="3" required></textarea>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="reset" class="btn btn-danger  btn-rounded" id="btdnResetForm"><i class="fas fa-redo"></i> Reset</button>
                                                <button type="submit" class="btn btn-success btn-rounded" id="btnData"><i class="fas fa-plus"></i> Add Template</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="card">
                                    <div class="card-header fw-bold">LIST OF TEMPLATE</div>
                                    <div class="card-body">
                                        <table class="table table-striped table-bordered" id="listTemplateTable">
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-12 pt-md-5 pt-1">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center m-0">RESIDENT MESSAGING LOGS & HISTORY</h4>

                </div>
                <div class="card-body">
                    <div class="d-flex justify-content-end">
                        <button class="btn btn-success btn-rounded btn-sm" data-bs-toggle="modal" data-bs-target="#sendingModal"><i class="fas fa-paper-plane"></i> Send Notification</button>
                    </div>
                    <table class="table table-striped table-bordered" id="smsNotificationHistory"></table>

                </div>
            </div>
        </div>
    </div>
</div>

<!-- Modal -->
<div class="modal" id="sendingModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="sendingModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <!-- <div class="modal-header">
                <h1 class="modal-title fs-5" id="staticBackdropLabel">Send</h1>
            </div> -->
            <form>
                <div class="modal-body m-2">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="form-group p-0">
                                <label for="drpCategories">Categories</label>
                                <select class="form-select" id="drpCategories" name="drpCategories">
                                    <option disabled selected>Select Category Name</option>
                                    <?php
                                    $fetchCategory = new fetch();
                                    $fetchCategory_res = $fetchCategory->getCategoryAll();
                                    if ($fetchCategory_res->rowCount()) {
                                        while ($fetchCategory_rows = $fetchCategory_res->fetch()) {
                                    ?>
                                            <option value="<?= $fetchCategory_rows["category_id"] ?>"><?= $fetchCategory_rows["category_name"] ?></option>
                                    <?php
                                        }
                                    }
                                    ?>
                                </select>
                            </div>
                            <div class="col-12 card mt-2 d-none" id="contentCategoriesLevel">
                                <div class="row p-2" style="max-height: 150px; overflow-x:auto" id="listCategoriesLevel">
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success"><i class="fas fa-paper-plane"></i> send</button>
                </div>
            </form>

        </div>
    </div>
</div>
<script src="js/resident-messaging.js" defer></script>

<?php include_once './includes/footer.php';
?>