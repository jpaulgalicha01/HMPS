<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Dashboard';
$_SESSION['Active_Navigate'] = 'Dashboard';

include_once './includes/header.php';
include_once './includes/navbar.php';
?>

<style>
  .leaflet-modal {
    /* hidden by default */
    display: none;
    position: absolute;
    top: 250px;
    left: 25px;
    right: auto;
    width: 350px;
    background: white;
    border: 1px solid #ccc;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.3);
    z-index: 1000;
    /* above map tiles */
    padding: 10px;
    border-radius: 6px;
  }

  .leaflet-modal-content #closeModal {
    float: right;
    cursor: pointer;
    font-weight: bold;
  }
</style>
<div class="container-fluid pt-2">
  <div class="row">
    <div class="col-12">
      <div class="row gap-lg-0 gap-3">
        <div class="col-lg-7 col-12 ">

          <div id="map" style="height: 550px;"></div>
          <div id="mapModal" class="leaflet-modal">
            <div class="leaflet-modal-content">
              <span id="closeModal">&times;</span>
              <div id="sidebar"></div>
            </div>
          </div>
        </div>
        <div class="col-lg-5 col-12">
          <div class="container-fluid ">

            <div class="col-12 px-0 pb-3">
              <div class="row">
                <div class="col-6 px-0">
                  <div class="card h-100">
                    <h5 class="card-header" style="font-size: clamp(.2rem, 0vw + 1rem, 1.5rem)">TOTAL NUMBER OF POPULATION</h5>
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

                <div class="col-6 px-0">
                  <div class="card h-100">
                    <h5 class="card-header" style="font-size: clamp(.2rem, 0vw + 1rem, 1.5rem)">TOTAL NUMBER OF HOUSEHOLD</h5>
                    <div class=" card-body">
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

            <div class="row gap-3">
              <div class="col-12 px-0 shadow-lg">
                <div class="card" style="min-height: 350px;">
                  <h5 class="card-header text-uppercase " style="font-size: clamp(.2rem, 0vw + 1rem, 1.5rem)">List of Household Affected in Different Categories</h5>
                  <div class="card-body">
                    <div class="row g-2">
                      <div class="col-12">
                        <form class="d-lg-flex d-grid  gap-1" id="frmFilterList">
                          <div class="col-md-3 px-0 ">
                            <label for="drpCategories" class="form-label">Category</label>
                            <select class="form-select form-select-sm" aria-label="Default select example" id="drpCategories">

                            </select>
                          </div>

                          <div class="col-md-4 px-0">
                            <label for="drpFilterType" class="form-label">Filter Type</label>
                            <select class="form-select form-select-sm" aria-label="Default select example" id="drpFilterType">
                              <option value="1">Household Number</option>
                              <option value="2">Full Name</option>
                            </select>
                          </div>
                          <div class="flex-fill px-0">
                            <label for="keyword" class="form-label">Keyword</label>
                            <input type="text" class="form-control form-control-sm" placeholder="search..." id="keyword" />
                          </div>
                          <div class="col-md-auto px-0 align-self-md-end text-md-end">
                            <button class="btn btn-success btn-sm form-control" type="submit"><i class="fas fa-search"></i></button>
                          </div>
                        </form>
                        <div class="border rounded mt-3">
                          <table class="table table-sm  table-hover mb-0" aria-label="Household list" id="householdTable"></table>
                        </div>
                      </div>
                      <!-- <div class="col-md-4 col-12">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <div class="fw-semibold">Categories & Levels</div>
                          <span class="text-muted small">Select to filter</span>
                        </div>
                        <div class="border rounded" style="overflow-y: auto">
                          <ul id="categoryLevelList" class="list-group list-group-flush">
                            <li class="list-group-item text-muted">Loading...</li>
                          </ul>
                        </div>
                      </div>

                      <div class="col-md-8 col-12" style="height: 250px;">
                        <div class="d-flex align-items-center justify-content-between mb-2">
                          <div class="fw-semibold" id="selectedCategoryLevelHeader">Households</div>
                          <span class="text-muted small" id="householdCountLabel"></span>
                        </div> -->
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
<script type="module" src="./js/index.js" defer></script>
<script src="./js/chart/index-chart.js" defer></script>
<script src="https://unpkg.com/@turf/turf/turf.min.js"></script>
<script src="./js/dashboard/category-household.js" defer></script>
<script src="../assets/apex-chart/apexcharts.js" defer></script>

<?php include_once './includes/footer.php';
?>