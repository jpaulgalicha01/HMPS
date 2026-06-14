<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Household & Household Members';
$_SESSION['Active_Navigate'] = 'Household & Household Members';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>
<link href="../assets/select-js/select2.min.css" rel="stylesheet" />
</script>

<div class="container-fluid">
    <div class="row gap-lg-0 gap-3">
        <div class="col-lg-5 col-12 order-lg-1 order-2">
            <div class="card">
                <h5 class="card-header">Household & Household Members</h5>
                <div class="card-body">
                    <ul class="list-unstyled">
                        <li>NOTE: <u>FAMILY ORDER EQUIVALENT VALUE</u>
                            <ul>
                                <li>1 is represent as Father of the Family</li>
                                <li>2 is represent as Mother of the family</li>
                                <li>3,4, and above is represent the children/count of household</li>
                            </ul>
                        </li>
                    </ul>
                    <form id="frmHouseHoldList">
                        <input type="hidden" name="houshold_id" id="houshold_id" />
                        <input type="hidden" name="household_coord" id="household_coord" />
                        <div class="mb-3">
                            <label for="household_number" class="form-label">Household Number <span class="text-danger">*</span></label>
                            <input type="tel" class="form-control" id="household_number" name="household_number"
                                placeholder="*****-****-****-****"
                                maxlength="20"
                                pattern="^\d{5}-\d{4}-\d{4}-\d{4}$" oninput="houseHoldNumberFormat(this)" required />
                        </div>
                        <div class="mb-3">
                            <label for="purok_sitio" class="form-label">Purok/Sitio <span class="text-danger">*</span></label>
                            <select class="form-select form-control select-basic-single" id="purok_sitio_id" name="purok_sitio_id" required>
                                <option value="" selected disabled>------Please Select Fisrt ------</option>
                                <?php
                                $fetch_sitio_pruok = new fetch();
                                $fetch_sitio_pruok_res = $fetch_sitio_pruok->getAllSitioPurok();
                                if ($fetch_sitio_pruok_res->rowCount()) {
                                    while ($fetch_sitio_pruok_row = $fetch_sitio_pruok_res->fetch()) {
                                ?>
                                        <option value="<?= $fetch_sitio_pruok_row["purok_sitio_id"] ?>"><?= $fetch_sitio_pruok_row["purok_sitio_name"] ?></option>

                                <?php
                                    }
                                }
                                ?>
                            </select>
                        </div>
                        <div>
                            <table class="table table-bordered">
                                <thead>
                                    <tr>
                                        <th>Full Name <span class="text-danger">*</span></th>
                                        <th width="30%">Family Order <span class="text-danger">*</span></th>
                                        <th width="10%">Action</th>
                                    </tr>
                                </thead>
                                <tbody id="HousholdMemberList">

                                </tbody>
                            </table>
                            <button type="button" class="btn btn-primary btn-sm  btn-rounded form-control" id="AddHouseHoldMembers"><i class="fas fa-plus"></i> Add Members</button>
                        </div>
                        <div class="pt-3">
                            <div id="map" style="height: 300px;"></div>
                        </div>
                        <div class="d-flex justify-content-end gap-2 pt-3">
                            <button type="reset" class="btn btn-danger  btn-rounded" id="btnReset"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded" id="btnSubmit"><i class="fas fa-home"></i> Add Household</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-7 col-12 order-lg-2 order-1">
            <div class="card" style="height: 500px;">
                <h5 class="card-header">Household & Household Members List</h5>
                <div class="card-body">
                    <table class="table table-striped table-bordered" id="householdTable">

                    </table>
                </div>
            </div>
        </div>
    </div>
    <script type="module" src="./js/household-member.js" defer></script>
    <script src="../assets/select-js/select2.min.js" defer></script>
    <script src="../js/InitializeSelect.js" defer></script>

    <?php include_once './includes/footer.php'; ?>