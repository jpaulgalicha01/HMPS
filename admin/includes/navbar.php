<div class="bg-primary p-2 text-center text-white" style="max-height:90px; min-height:5px; height:auto;">
  <p class="fs-3 fw-bold">Household Mapping and Profiling System</p>
</div>
<nav class="navbar navbar-expand-lg bg-body-tertiary shadow mb-3 bg-body-tertiary rounded">
  <div class="container-fluid">
    <div class="mx-auto" style="width: 50px; height: 50px; border-radius: 18px;">
      <a class="navbar-brand text-center " href="#">
        <img src="../assets/img/Logo.png" alt="logo" class="img-fluid d-flex justify-content-center align-self-center">
      </a>
    </div>
    <!-- HMPS -->
    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div class="container-fluid d-lg-flex d-grid align-items-center">
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-1">
          <li class="nav-item">
            <a class="nav-link <?= $_SESSION['Active_Navigate'] ==
                                  'Dashboard'
                                  ? 'active'
                                  : '' ?>" aria-current="page" href="index.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
          </li>
          <li class="nav-item dropdown">
            <a class="nav-link secondary dropdown-toggle 
                  <?= $_SESSION['Active_Navigate'] == 'Individual Records of Barangay Inhabitants' ||
                    $_SESSION['Active_Navigate'] ==
                    'Household & Household Members'
                    ? 'active'
                    : '' ?>"
              href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-list"></i> List of Household
            </a>
            <ul class="dropdown-menu">
              <li><a class="dropdown-item <?= $_SESSION['Active_Navigate'] == 'Individual Records of Barangay Inhabitants'
                                            ? 'active'
                                            : '' ?>" href="individual-records.php">Individual Records of Barangay Inhabitants</a></li>
              <li><a class="dropdown-item <?= $_SESSION['Active_Navigate'] == 'Household & Household Members'
                                            ? 'active'
                                            : '' ?>" href="household-member.php">Household & Household Members</a></li>
            </ul>
          </li>


          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle 
                  <?= $_SESSION['Active_Navigate'] == 'Barangay Setup'
                    ? ($_SESSION['Active_Navigate'] == 'Categories Setup'
                      ? ($_SESSION['Active_Navigate'] == 'Area Setup'
                        ? 'active'
                        : '')
                      : '')
                    : '' ?>
                  
                  " href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-cog"></i> Setup
            </a>
            <ul class="dropdown-menu ">
              <li><a class="dropdown-item <?= $_SESSION['Active_Navigate'] == 'Barangay Setup'
                                            ? 'active'
                                            : '' ?>" href="barangay-setup.php">Barangay Setup</a></li>
              <li><a class="dropdown-item <?= $_SESSION['Active_Navigate'] == 'Purok/Sitio List'
                                            ? 'active'
                                            : '' ?>" href="purok-list.php">Purok/Sitio List</a></li>
              <li><a class="dropdown-item <?= $_SESSION['Active_Navigate'] == 'Categories Setup'
                                            ? 'active'
                                            : '' ?>" href="category.php">Categories Setup</a></li>
              <li><a class="dropdown-item <?= $_SESSION['Active_Navigate'] == 'Area Setup'
                                            ? 'active'
                                            : '' ?>" href="area-setup.php">Area</a></li>
            </ul>
          </li>

          <li class="nav-item">
            <a class="nav-link <?= $_SESSION['Active_Navigate'] == 'Resident Messaging' ? 'active' : '' ?>" aria-current="page" href="resident-messaging.php"><i class="fas fa-envelope"></i> Resident Messaging</a>
          </li>

          <!-- <li class="nav-item">
            <a class="nav-link" href="#"><i class="fas fa-chart-bar"></i> Reports</a>
          </li> -->

          <div class="align-items-center d-lg-none d-block">
            <div class="flex-shrink-0 dropdown">
              <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
              </a>
              <ul class="dropdown-menu dropdown-menu-end text-small shadow">
                <li><a class="dropdown-item" href="#">Profile</a></li>
                <li>
                  <hr class="dropdown-divider">
                </li>
                <li><a class="dropdown-item" href="#">Sign out</a></li>
              </ul>
            </div>
          </div>
        </ul>
      </div>
      <div class=" align-items-center d-lg-block d-none">
        <div class="flex-shrink-0 dropdown">
          <a href="#" class="d-block link-body-emphasis text-decoration-none dropdown-toggle" data-bs-toggle="dropdown" aria-expanded="false">
            <img src="https://github.com/mdo.png" alt="mdo" width="32" height="32" class="rounded-circle">
          </a>
          <ul class="dropdown-menu dropdown-menu-end text-small shadow">
            <li><a class="dropdown-item" href="#">Profile</a></li>
            <li>
              <hr class="dropdown-divider">
            </li>
            <li><a class="dropdown-item" href="#">Sign out</a></li>
          </ul>
        </div>
      </div>
    </div>
</nav>

<body class="bg-light mb-5">