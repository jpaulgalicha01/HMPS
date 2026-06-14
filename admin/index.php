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
        <div class="col-lg-7 col-12 " style="height: 500px;">
          <div id="map" style="height: 500px;"></div>
        </div>
        <div class="col-lg-5 col-12" style="height: 500px;">
          <div class="container-fluid ">
            <div class="row gap-3">
              <div class="col-12 px-0 shadow-lg ">
                <div class="card" style="height: 250px;">
                  <h5 class="card-header">LEGEND</h5>
                  <div class="card-body">
                    <h5 class="card-title">List of Categories</h5>
                    <p class="card-text">With supporting text below as a natural lead-in to additional content.</p>

                  </div>
                </div>
              </div>

              <div class="col-12 px-0 " style="height:250px">
                <div class="row">


                  <div class="col-6 ">
                    <div class="card h-100">
                      <h5 class="card-header">TOTAL NUMBER OF POPULATION</h5>
                      <div class="card-body">
                        <!-- <h5 class="card-title"><i class="bi bi-people-fill"></i> 1,234</h5> -->
                        <p class="card-text fs-1"><i class="fas fa-users"></i>
                          <?php
                          $count_total_users = new fetch();
                          $count_total_users->CountPopulation();

                          ?>
                        </p>
                      </div>
                    </div>
                  </div>

                  <div class="col-6 ">
                    <div class="card h-100">
                      <h5 class="card-header">TOTAL NUMBER OF HOUSEHOLD</h5>
                      <div class="card-body">
                        <p class="card-text fs-1"><i class="fas fa-home"></i>
                          <?php
                          $count_total_household = new fetch();
                          $count_total_household->CountHouseHold();

                          ?>

                        </p>
                      </div>
                    </div>
                  </div>

                </div>
              </div>

            </div>
          </div>
        </div>
      </div>

      <div class="row gap-lg-0 gap-5 pt-md-5 pt-1 ">
        <div class="col-md-6  shadow-lg ">
          <div class=" card h-100 ">
            <h5 class=" card-header">TOTAL NUMBER OF PWD's</h5>
            <div class="card-body">
              <div id="chart">
              </div>
            </div>
          </div>
        </div>

        <div class="col-md-6 shadow-lg ">
          <div class="card h-100">
            <h5 class="card-header">COUNT NUMBER OF AGE CATEGORIES</h5>
            <div class="card-body">
              <div id="chartAgeCat">
              </div>

            </div>

          </div>
        </div>

      </div>


    </div>
  </div>
  <script src="../assets/apex-chart/apexcharts.js" defer></script>
  <script type="module" src="./js/index.js" defer></script>
  <script src="./js/chart/index-chart.js" defer></script>
  <script src="https://unpkg.com/@turf/turf/turf.min.js"></script>


  <?php include_once './includes/footer.php';
  ?>