<?php
include 'includes/autoload.inc.php';

unset($_SESSION['title']);
unset($_SESSION['Active_Navigate']);
$_SESSION['title'] = 'Household Information';
$_SESSION['Active_Navigate'] = 'Household Information';

include_once './includes/header.php';
include_once './includes/navbar.php';

$modules = [
    [
        'id' => 'individual-records',
        'name' => 'Individual Records of Barangay Inhabitants',
        'page' => 'individual-records.php',
        'scripts' => [
            [
                'src' => './js/family-list.js',
                'type' => 'normal'
            ],
        ]
    ],
    [
        'id' => 'household-member',
        'name' => 'Household & Household Members',
        'page' => 'household-member.php',
        'scripts' => [
            [
                'src' => '../js/household-member.js',
                'type' => 'module'
            ],
            [
                'src' => '../assets/select-js/select2.min.js',
                'type' => 'normal'
            ],
            [
                'src' => '../js/InitializeSelect.js',
                'type' => 'normal'
            ],
        ]
    ],
];
?>

<div class="container-fluid pt-2">
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
    <?php include './includes/footer.php'; ?>