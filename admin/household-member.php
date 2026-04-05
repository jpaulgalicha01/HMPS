<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Household & Household Members';
$_SESSION['Active_Navigate'] = 'Household & Household Members';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<div class="container-fluid">
    <div class="row gap-lg-0 gap-3">
        <div class="col-lg-4 col-12 order-lg-1 order-2">
            <div class="card">
                <h5 class="card-header">Household & Household Members</h5>
                <div class="card-body">
                    <form>
                        <div class="mb-3">
                            <label for="FatherName" class="form-label">Family Name</label>
                            <select class="form-select" id="FamilyName">
                                <option disabled selected>Select Family Name</option>
                                <option value="1">Dela Cruz</option>
                                <option value="2">Smith</option>
                                <option value="3">Johnson</option> 
                            </select>
                        </div>
                        <div class="mb-3">
                            <label for="FatherName" class="form-label">Father Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="FatherName" placeholder="Please enter N/A if not applicable">
                        </div>
                        <div class="mb-3">
                           <label for="MotherName" class="form-label">Mother Name <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="MotherName" placeholder="Please enter N/A if not applicable">
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" class="form-check-input" id="exampleCheck1">
                            <label class="form-check-label" for="exampleCheck1">Have siblings ? </label>
                        </div>
                        <div>
                            <table class="table table-bordered" id="siblingTable">
                                <thead>
                                    <tr>
                                        <th>Sibling Name</th>
                                        <th>Age</th>
                                        <th>Family Order</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody >
                                    <tr>
                                        <td><input type="text" class="form-control" placeholder="Enter Sibling Name"></td>
                                        <td width="150"><input type="number" class="form-control" placeholder="Enter Age"></td>
                                        <td width="150"><input type="number" class="form-control" placeholder="Enter Family Order"></td>
                                        <td class="text-center"><button type="button" class="btn btn-danger btn-sm  btn-rounded"><i class="fas fa-trash"></i> </button></td>
                                    </tr>
                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm  btn-rounded form-control"><i class="fas fa-plus"></i> Add Sibling</button>
                        </div>
                        <div class="pt-3">
                            <div id="map" style="height: 300px;"></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3">
                            <button type="reset" class="btn btn-danger  btn-rounded"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded"><i class="fas fa-plus"></i> Add Household</button> 
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-8 col-12 order-lg-2 order-1">
            <div class="card" style="height: 500px;">
                <h5 class="card-header">Household & Household Members List</h5>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="householdTable">
                        <thead>
                            <tr>
                                <th>Family Name</th>
                                <th>Father Name</th>
                                <th>Mother Name</th>
                                <th>Siblings</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <!-- Sample Data -->
                            <tr>
                                <td width="250">Dela Cruz</td>
                                <td>Juan Dela Cruz</td>
                                <td>Maria Dela Cruz</td>
                                <td>2 Siblings</td>
                                <td width="150"><button class="btn btn-success btn-sm"><i class="far fa-edit"></i> Update</button></td>
                            </tr>
                            <!-- More rows can be added here -->
                        </tbody>
                    </table>
                </div>
            </div>
    </div>
</div>
<script src="./js/household-member.js"></script>
<?php include_once './includes/footer.php'; ?>
