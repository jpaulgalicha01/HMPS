<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Area Setup';
$_SESSION['Active_Navigate'] = 'Area Setup';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>



<div class="container-fluid pt-2">
    <div class="card">
        <h5 class="card-header">Area Setup</h5>
        <div class="card-body">
            <div class="container-fluid">
                <div class="row" id="AreaSetup">
                    <div class="col-md-9">
                        <div id="sidebar" class="d-none" style="height:500px; width:30%; float:right; overflow:auto; border:1px solid #ccc; padding:10px;">
                            <form id="SubmitCategoryArea">
                                <h4>Category Information</h4></br>
                                <div class="mb-3">
                                    <input id="mainLayer_id" name="mainLayer_id" />
                                    <input id="category_coords" name="category_coords" />
                                    <label for="FatherName" class="form-label">Category Name:</label>
                                    <select class="form-select" id="CategoryName">
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
                                    <label for="FatherName" class="form-label">Category Level:</label>
                                    <select class="form-select" id="CategoryLevel">
                                        <option disabled selected>Select Family Name</option>
                                        <option value="1" style="color:#ff0000;">asdd</option>
                                    </select>
                                </div>
                                <div class="text-end">
                                    <button class="btn btn-success btn-sm btn-round"><i class="fas fa-save"></i> Save</button>
                                </div>
                            </form>
                        </div>
                        <div id="map" style="height: 500px;"></div>
                    </div>
                    <div class="col-md-3">
                        <fieldset class="border p-2">
                            <legend class="w-auto">Legend</legend>
                            <ul class="list-group" id="legendList">
                                <!-- Area items will be dynamically added here -->
                            </ul>
                        </fieldset>
                    </div>
                </div>

            </div>
        </div>

    </div>
</div>

<script type="module" src="js/area-setup.js" defer></script>
<script src="https://unpkg.com/@turf/turf/turf.min.js"></script>
<?php include_once './includes/footer.php'; ?>