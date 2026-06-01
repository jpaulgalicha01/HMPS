<?php
include 'includes/autoload.inc.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["addFamilyList"])) {
        $FamilyName = secured($_POST["FamilyName"]);
        $FamilyUniqueID = secured($_POST["FamilyUniqueId"]);
        $insert = new insert();
        $insert->addFamilyList($FamilyName, $FamilyUniqueID);
    } else if (isset($_POST["deleteFamilyList"])) {
        $FamilyId = secured($_POST["deleteFamilyId"]);

        $delete = new delete();
        $delete->deleteFamilyList($FamilyId);
    } else if (isset($_POST["save_polygon"])) {
        $coordinates = $_POST["coordinates"];
        $insert = new insert();
        $insert->savePolygon($coordinates);
    } else if (isset($_POST["update_polygon"])) {
        $polygonId = secured($_POST["polygon_id"]);
        $coordinates = secured($_POST["coordinates"]);
        $updatePolygon = new update();
        $updatePolygon->updatePolygon($polygonId, $coordinates);
    } else if (isset($_POST["delete_polygon"])) {
        $polygonId = secured($_POST["polygon_id"]);
        $deletePolygon = new delete();
        $deletePolygon->deletePolygon($polygonId);
    } else if (isset($_POST["add_category"])) {
        $CategoryID = secured($_POST["CategoryID"]);
        $CategoryName = secured($_POST["CategoryName"]);
        $CategoryLevelList = $_POST["CategoryLevelList"];
        if (empty($CategoryID)) {
            $insertCategorylist = new insert();
            $insertCategorylist->InsertCategory($CategoryName, $CategoryLevelList);
        } else {
            $updateCategorylist = new update();
            $updateCategorylist->UpdateCategory($CategoryID, $CategoryName, $CategoryLevelList);
        }
    } else if (isset($_POST["getCategoryDetailsWithID"])) {
        $CategoryID = secured($_POST["CategoryID"]);
        $fetch = new fetch();
        $fetch->getCategoryDetailsWithID($CategoryID);
    } else if (isset($_POST["deleteCategory"])) {
        $CategoryID = secured($_POST["CategoryID"]);
        $delete = new delete();
        $delete->deleteCategory($CategoryID);
    } else {
        return http_response_code(404);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["getFamilyList"])) {
        $fetch = new fetch();
        $fetch->getFamilyList();
    } else if (isset($_GET["fetch_polygons"])) {
        $fetch = new fetch();
        $fetch->getPolygon();
    } else if (isset($_GET["getCategoryList"])) {
        $fetch = new fetch();
        $fetch->getCategoryList();
        // } else if(){
    } else {
        return http_response_code(404);
    }
} else {
    ob_end_flush(header("Location: index.php"));
}
