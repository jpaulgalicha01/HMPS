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
                    <form>
                        <div class="mb-3">
                            <label for="CategoryName" class="form-label">Category Name</label>
                            <input type="text" class="form-control" id="CategoryName" placeholder="Enter Category Name">
                        </div>
                        <div class="mb-3">
                            <label for="CategoryLevel" class="form-label">Category Level</label>
                            <table class="table table-bordered" id="categoryLevelTable">
                                <thead>
                                    <tr>
                                        <th>Level Name</th>
                                        <th class="text-center">Color</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr>
                                        <td><input type="text" class="form-control" placeholder="Enter Level Name"></td>
                                        <td class="d-flex align-items-center justify-content-center"><input type="color" class="form-control form-control-color" value="#563d7c" title="Choose your color"></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm  btn-rounded"><i class="fas fa-trash"></i> </button></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-danger  btn-rounded"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded"><i class="fas fa-plus"></i> Add Category</button>
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
                        <thead>
                            <tr>
                                <th>Category Name</th>
                                <th>Category Level List</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample Data -->
                            <tr>
                                <td width="250">Poverty Incidence</td>
                                <td>
                                    <ul class="list-unstyled  d-flex  gap-1">
                                        <li><span class="badge bg-primary">Low</span></li>
                                        <li><span class="badge bg-warning text-dark">Moderate</span></li>
                                        <li><span class="badge bg-danger">High</span></li>
                                    </ul>
                                <td width="150"><button class="btn btn-primary btn-sm"><i class="far fa-edit"></i> Update</button></td>
                            </tr>
                            <!-- More rows can be added here -->
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </div>
    <?php include_once './includes/footer.php'; ?>