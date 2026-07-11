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
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h4 class="text-center m-0">MESSAGING TARGET & TEMPLATES</h4>
                </div>
                <div class="card-body">
                    <div class="container-fluid">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="card">
                                    <div class="card-header fw-bold">MESSAGE TEMPLATES</div>
                                    <div class="card-body">
                                        <form id="submitTemplateAlert">
                                            <input type="hidden" name="template_id" name="template_id" />
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
                                                <textarea class="form-control text-uppercase" id="TemplateMessage" rows="3" required></textarea>
                                            </div>
                                            <div class="d-flex justify-content-end gap-2">
                                                <button type="reset" class="btn btn-danger  btn-rounded" id="btdnResetForm"><i class="fas fa-redo"></i> Reset</button>
                                                <button type="submit" class="btn btn-success btn-rounded" id="btnData"><i class="fas fa-plus"></i> Add Template</button>
                                            </div>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<script src="js/resident-messaging.js" defer></script>

<?php include_once './includes/footer.php';
?>