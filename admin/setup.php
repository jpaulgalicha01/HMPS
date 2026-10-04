<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Setup';
$_SESSION['Active_Navigate'] = 'Setup';

include_once './includes/header.php';
include_once './includes/navbar.php';

$modules = [
    [
        'id' => 'barangay-setup',
        'name' => 'Barangay Setup',
        'page' => 'barangay-setup.php',
        'scripts' => [
            [
                'src' => '../js/barangay-setup.js',
                'type' => 'module'
            ]
        ]
    ],
    [
        'id' => 'purok-sitio',
        'name' => 'Purok/Sitio List',
        'page' => 'purok-list.php',
        'scripts' => [
            [
                'src' => './js/purok/sitio-name.js',
                'type' => 'normal'
            ]
        ]
    ],
    [
        'id' => 'category',
        'name' => 'Category Setup',
        'page' => 'category.php',
        'scripts' => [
            [
                'src' => './js/category.js',
                'type' => 'normal'
            ]
        ]
    ],

    [
        'id' => 'area',
        'name' => 'Area Setup',
        'page' => 'area-setup.php',
        'scripts' => [
            [
                'src' => '../js/area-setup.js',
                'type' => 'module'
            ],
            [
                'src' => 'https://unpkg.com/@turf/turf/turf.min.js',
                'type' => 'normal'
            ]
        ]
    ]
];

?>


<div class="container-fluid pt-0">
    <div class="card-body">
        <ul class="nav nav-tabs" id="moduleTabs">

            <?php foreach ($modules as $index => $module): ?>

                <li class="nav-item">

                    <button
                        class="nav-link <?= $index === 0 ? 'active' : '' ?>"
                        data-bs-toggle="tab"
                        data-bs-target="#<?= $module['id'] ?>"
                        data-module="<?= $module['id'] ?>"
                        data-page="<?= $module['page'] ?>"
                        data-scripts="<?= htmlspecialchars(
                                            json_encode($module['scripts']),
                                            ENT_QUOTES,
                                            'UTF-8'
                                        ) ?>"
                        type="button">

                        <?= htmlspecialchars($module['name']) ?>

                    </button>

                </li>

            <?php endforeach; ?>

        </ul>
        <div class="tab-content mt-3">

            <?php foreach ($modules as $index => $module): ?>

                <div
                    class="tab-pane fade <?= $index === 0 ? 'show active' : '' ?>"
                    id="<?= $module['id'] ?>">
                </div>

            <?php endforeach; ?>

        </div>
    </div>

    <script src="js/setup.js" defer></script>
    <script src="https://unpkg.com/@turf/turf/turf.min.js"></script>

    <?php include './includes/footer.php'; ?>