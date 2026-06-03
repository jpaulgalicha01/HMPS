<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Dashboard';
$_SESSION['Active_Navigate'] = 'Dashboard';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>
<div class="container-fluid pt-2">
  <div class="row">
    <div class="col-12">
      <div class="row gap-lg-0 gap-3">
        <div class="col-lg-6 col-12" style="height: 500px;">
          <div id="map" style="height: 500px;"></div>
        </div>
        <div class="col-lg-6 col-12" style="height: 500px;">
          <div class="container-fluid ">
            <div class="row gap-3">
              <div class="col-12">
                <div class="card" style="height: 250px;">
                  <h5 class="card-header">LEGEND</h5>
                  <div class="card-body">
                    <h5 class="card-title">List of Categories</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>

                  </div>
                </div>
              </div>

              <div class="col-12" style="height:250px">
                <div class="row">


                  <div class="col-6">
                    <div class="card h-100">
                      <h5 class="card-header">TOTAL NUMBER OF FAMILIES</h5>
                      <div class="card-body">
                        <!-- <h5 class="card-title"><i class="bi bi-people-fill"></i> 1,234</h5> -->
                        <p class="card-text fs-1"><i class="fas fa-users"></i> 1,234</p>
                      </div>
                    </div>
                  </div>

                  <div class="col-6">
                    <div class="card h-100">
                      <h5 class="card-header">TOTAL NUMBER OF HOUSEHOLD</h5>
                      <div class="card-body">
                        <p class="card-text fs-1"><i class="fas fa-home"></i> 1,234</p>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="col-12 pt-lg-5 pt-1">
        <div class="card" style="height: 250px;">
          <h5 class="card-header">HOUSEHOLD LIST</h5>
          <div class="card-body">
            <div class="table-responsive">
              <table class="table table-bordered table-striped">
                <thead>
                  <tr>
                    <th>Household ID</th>
                    <th>Head of Household</th>
                    <th>Number of Members</th>
                    <th>Address</th>
                  </tr>
                </thead>
                <tbody>
                  <tr>
                    <td>HH001</td>
                    <td>Juan Dela Cruz</td>
                    <td>5</td>
                    <td>123 Main St, Barangay 1</td>
                  </tr>
                  <tr>
                    <td>HH002</td>
                    <td>Maria Santos</td>
                    <td>3</td>
                    <td>456 Elm St, Barangay 2</td>
                  </tr>
                  <!-- More rows as needed -->
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>


    </div>
  </div>
  <script type="module" src="./js/index.js"></script>
  <script src="https://unpkg.com/@turf/turf/turf.min.js"></script>

  <?php include_once './includes/footer.php';
  ?>