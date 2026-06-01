<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Categories Setup';
$_SESSION['Active_Navigate'] = 'Categories Setup';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>
<div class="container-fluid pt-2">
    <div class="row gap-lg-0 gap-3">
        <div class="col-lg-4 col-12 order-lg-1 order-2">
            <div class="card">
                <h5 class="card-header">Add Category</h5>
                <div class="card-body">
                    <form id="frmSubmitCatergoryList">
                        <div class="mb-3">
                            <input type="hidden" id="CategoryID" name="CategoryID">
                            <label for="CategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="CategoryName" name="CategoryName" placeholder="ex. Poverty Incidence" required>
                        </div>
                        <div class="mb-3">
                            <label for="CategoryLevel" class="form-label">Category Level</label>
                            <table class="table table-bordered" id="categoryLevelTable">
                                <thead>
                                    <tr>
                                        <th>Level Name</th>
                                        <th class="text-center" width="10%">Color</th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="catergoryLevelList">
                                    <!-- <tr>
                                        <td><input type="text" class="form-control" placeholder="Enter Level Name"></td>
                                        <td class="d-flex align-items-center justify-content-center"><input type="color" class="form-control form-control-color" value="#563d7c" title="Choose your color"></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm  btn-rounded"><i class="fas fa-trash"></i> </button></td>
                                    </tr> -->
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm  btn-rounded form-control" onclick="fncAddLevelBtn()"><i class="fas fa-plus"></i> Add Level</button>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-danger  btn-rounded" id="btnReset"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded" id="btnSubmit"><i class="fas fa-plus"></i> Add Category</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-8 col-12 order-lg-2 order-1">
            <div class="card">
                <h5 class="card-header">Category List</h5>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="categoryTable">
                    </table>
                </div>
            </div>

        </div>
    </div>
    <script src="js/catergory.js" defer></script>
    <?php include_once './includes/footer.php'; ?>