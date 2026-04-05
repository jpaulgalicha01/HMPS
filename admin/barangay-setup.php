<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Barangay Setup';
$_SESSION['Active_Navigate'] = 'Barangay Setup';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<div class="container-fluid pt-2">
    <div class="row gap-lg-0 gap-3">
        <div class="col-12">
            <div id="map" style="height: 90vh;"></div>
        </div>
    </div>
</div>
<script src="./js/barangay-setup.js"></script>
<?php include_once './includes/footer.php'; ?>
