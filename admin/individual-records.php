<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Individual Records of Barangay Inhabitants';
$_SESSION['Active_Navigate'] = 'Individual Records of Barangay Inhabitants';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<div class="container-fluid pt-2">
    <div class="row gap-lg-0 gap-3">
        <div class="col-lg-6 col-12 order-lg-1 order-2">
            <div class="card">
                <h5 class="card-header">Personal Information</h5>
                <div class="card-body">
                    <form id="frmSubmitPersonalInfo">
                        <input id="person_unique_id" name="person_unique_id" type="hidden" />
                        <div class="mb-3">
                            <label for="phil_sys_id" class="form-label">PhilSys Card No <i>(If not applicable put N/A)</i> <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="phil_sys_id" name="phil_sys_id" placeholder="Enter PhilSys Card No." required>
                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid  gap-1">
                                <div class="flex-fill px-0">
                                    <label for="last_name" class="form-label">Last Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="last_name" name="last_name" placeholder="Enter Last Name" required>
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="first_name" name="first_name" placeholder="Enter First Name" required>
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="middle_name" class="form-label">Middle Name</label>
                                    <input type="text" class="form-control" id="middle_name" name="middle_name" placeholder="Enter Middle Name">
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="suffix" class="form-label">Suffix</label>
                                    <select class="form-select" id="suffix" name="suffix">
                                        <option value="" selected>None</option>
                                        <option value="Jr.">Jr.</option>
                                        <option value="Sr.">Sr.</option>
                                        <option value="I">I</option>
                                        <option value="II">II</option>
                                        <option value="III">III</option>
                                        <option value="IV">IV</option>
                                        <option value="V">V</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid  gap-1">
                                <div class="flex-fill px-0">
                                    <label for="birdthdate" class="form-label">Birth Date <span class="text-danger">*</span></label>
                                    <input type="date" class="form-control" id="birdthdate" name="birdthdate" required>
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="birth_place" class="form-label">Birth Place <i>(If not applicable put N/A)</i><span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="birth_place" name="birth_place" placeholder="Enter Birth Place" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid  gap-1">
                                <div class=" px-0 col-2">
                                    <label for="sex" class="form-label">Sex <span class="text-danger">*</span></label>
                                    <select class="form-select" id="sex" name="sex" required>
                                        <option value="" selected disabled>Select Sex</option>
                                        <option>Male</option>
                                        <option>Female</option>
                                    </select>
                                </div>
                                <div class=" px-0 col-3">
                                    <label for="civil_status" class="form-label">Civil Status <span class="text-danger">*</span></label>
                                    <select class="form-select" id="civil_status" name="civil_status" required>
                                        <option value="" selected disabled>Select Civil Status</option>
                                        <option>Single</option>
                                        <option>Married</option>
                                        <option>Widowed</option>
                                        <option>Divorced</option>
                                    </select>
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="religion" class="form-label">Religion <i>(If not applicable put N/A)</i><span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="religion" name="religion" placeholder="Enter Religion" required>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid  gap-1">
                                <div class="flex-fill px-0">
                                    <label for="residential_address" class="form-label">Residential Address <i>(If not applicable put N/A)</i><span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="residential_address" name="residential_address" placeholder="Enter Residential Address" required>
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="citizenship" class="form-label">Citizenship <i>(If not applicable put N/A)</i><span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="citizenship" name="citizenship" placeholder="Enter Citizenship" required>
                                </div>
                            </div>

                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid  gap-1">
                                <div class="flex-fill px-0">
                                    <label for="profession" class="form-label">Profession/Occupation</label>
                                    <input type="text" class="form-control" id="profession" name="profession" placeholder="Enter Profession/Occupation">
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="contact_no" class="form-label">Contact No. </label>
                                    <input type="tel" class="form-control" id="contact_no" name="contact_no"
                                        maxlength="16"
                                        placeholder="+639-XXX-XXXXXX"
                                        pattern="^\+639-\d{3}-\d{3}-\d{3}$" oninput="phoneNumberFormat(this)" />
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="email_address" class="form-label">Email Address </label>
                                    <input type="email" class="form-control" id="email_address" name="email_address" placeholder="juan_delacruz@email.com">
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid  gap-1">
                                <div class="flex-fill px-0">
                                    <label for="highest_attainment_education" class="form-label">Highest Educational Attainment <span class="text-danger">*</span></label>
                                    <select class="form-select" id="highest_attainment_education" name="highest_attainment_education" required>
                                        <option>Elementary</option>
                                        <option>High School</option>
                                        <option>College</option>
                                        <option>Post Grad</option>
                                        <option>Vocational</option>

                                    </select>
                                </div>
                                <div class="flex-fill px-0">
                                    <label for="highest_attainment_education_specific" class="form-label">Please Specify <span class="text-danger">*</span></label>
                                    <select class="form-select" id="highest_attainment_education_specific" name="highest_attainment_education_specific" required>
                                        <option>Under Graduate</option>
                                        <option>Graduate</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                        <div class="mb-3">
                            <div class="d-lg-flex d-grid gap-1">
                                <div class="col-lg-6">
                                    <label for="type_of_disability" class="form-label">Type Of Disability <span class="text-danger">*</span></label>
                                    <select class="form-select" id="type_of_disability" name="type_of_disability" required>
                                        <option>N/A</option>
                                        <option>Psychosocial Disability</option>
                                        <option>Chronic Illness</option>
                                        <option>Learning Disability</option>
                                        <option>Visual Disability</option>
                                        <option>Orthopedic / Physical Disability</option>
                                        <option>Mental Disability / Intellectual Disability</option>
                                        <option>Hearing Disability (Deaf / Hard of Hearing)</option>
                                        <option>Speech and Language Impairment</option>
                                        <option>Cancer and Rare Diseases</option>
                                        <option>Others</option>
                                    </select>
                                </div>
                                <div class="col-lg-6 d-none" id="TypeOfDisabilitySpecificDrpDwn">
                                    <label for="type_of_disability_others" class="form-label">Please Specify <span class="text-danger">*</span></label>
                                    <input class="form-control" type="text" id="type_of_disability_others" name="type_of_disability_others" />
                                </div>
                            </div>
                        </div>
                        <div class="d-flex justify-content-end gap-2">
                            <button type="reset" class="btn btn-danger  btn-rounded" id="btdnResetForm"><i class="fas fa-redo"></i> Reset</button>
                            <button type="submit" class="btn btn-success btn-rounded" id="btnData"><i class="fas fa-plus"></i> Add Data</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        <div class="col-lg-6 col-12 order-lg-2 order-1">
            <div class="card">
                <h5 class="card-header">Individual Records of Barangay Inhabitants</h5>
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