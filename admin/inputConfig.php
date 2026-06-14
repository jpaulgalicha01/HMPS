<?php
include 'includes/autoload.inc.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST["save_polygon"])) {
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
    } else if (isset($_POST["submit_area_setup"])) {
        $category_area_id = secured($_POST["category_area_id"]);
        $polygon_id = secured($_POST["polygon_id"]);
        $category_coords = $_POST["category_coords"];
        $CategoryID = secured($_POST["categoryID"]);
        $CategoryLevel = secured($_POST["CategoryLevel"]);

        if (empty($category_area_id)) {
            $insertAreaSetup = new insert();
            $insertAreaSetup->InsertAreaSetup($polygon_id, $category_coords, $CategoryID, $CategoryLevel);
        } else {
            $updateAreaSetup = new update();
            $updateAreaSetup->UpdateAreaSetup($category_area_id, $polygon_id, $category_coords, $CategoryID, $CategoryLevel);
        }
    } else if (isset($_POST["delete_area_polygon"])) {
        $category_area_id = secured($_POST["category_area_id"]);
        $deleteAreaSetup = new delete();
        $deleteAreaSetup->DeleteAreaSetup($category_area_id);
    } else if (isset($_POST["submitPurokSitio"])) {
        $PurokUniqueId = secured($_POST["PurokUniqueId"]);
        $PurokName = secured($_POST["PurokName"]);

        if (empty($PurokUniqueId)) {
            $insert = new insert();
            $insert->addPurokSitio($PurokName);
        } else {
            $update = new update();
            $update->updatePurokSitio($PurokUniqueId, $PurokName);
        }
    } else if (isset($_POST["deletePurokSitio"])) {
        $PurokUniqueId = secured($_POST["PurokUniqueId"]);
        $delete = new delete();
        $delete->deletePurokSitio($PurokUniqueId);
    } else if (isset($_POST["adding_update_individual_info"])) {
        $PersonUniqueID = secured($_POST["person_unique_id"]);
        $PhilSysID = secured($_POST["phil_sys_id"]);
        $LastName = secured($_POST["last_name"]);
        $FirstName = secured($_POST["first_name"]);
        $MiddleName = secured($_POST["middle_name"]);
        $Suffix = secured($_POST["suffix"]);
        $Birdthdate = secured($_POST["birdthdate"]);
        $BirthPlace = secured($_POST["birth_place"]);
        $Sex = secured($_POST["sex"]);
        $CivilStatus = secured($_POST["civil_status"]);
        $Religion = secured($_POST["religion"]);
        $ResidentialAddress = secured($_POST["residential_address"]);
        $Citizenship = secured($_POST["citizenship"]);
        $Profession = secured($_POST["profession"]);
        $ContactNo = secured($_POST["contact_no"]);
        $EmailAddress = secured($_POST["email_address"]);
        $HighestAttainmentEducation = secured($_POST["highest_attainment_education"]);
        $HighestAttainmentEducationSpecific = secured($_POST["highest_attainment_education_specific"]);
        $TypeOfDisability = secured($_POST["type_of_disability"]);
        $TypeOfDisabilityOthers = secured($_POST["type_of_disability_others"]);

        if (empty($PersonUniqueID)) {
            // Inserting
            $insert = new insert();
            $insert->addPersonalInformation(
                $PhilSysID,
                $LastName,
                $FirstName,
                $MiddleName,
                $Suffix,
                $Birdthdate,
                $BirthPlace,
                $Sex,
                $CivilStatus,
                $Religion,
                $ResidentialAddress,
                $Citizenship,
                $Profession,
                $ContactNo,
                $EmailAddress,
                $HighestAttainmentEducation,
                $HighestAttainmentEducationSpecific,
                $TypeOfDisability,
                $TypeOfDisabilityOthers
            );
        } else {
            //Updating
            $update = new update();
            $update->updatePersonalInformdation(
                $PersonUniqueID,
                $PhilSysID,
                $LastName,
                $FirstName,
                $MiddleName,
                $Suffix,
                $Birdthdate,
                $BirthPlace,
                $Sex,
                $CivilStatus,
                $Religion,
                $ResidentialAddress,
                $Citizenship,
                $Profession,
                $ContactNo,
                $EmailAddress,
                $HighestAttainmentEducation,
                $HighestAttainmentEducationSpecific,
                $TypeOfDisability,
                $TypeOfDisabilityOthers
            );
        }
    } else if (isset($_POST['deleteIndividualRecord'])) {
        $PersonUniqueID = secured($_POST["person_unique_id"]);
        $delete = new delete();
        $delete->deleteIndividualPersonInfo($PersonUniqueID);
    } else if (isset($_POST["get_records_person"])) {
        $PersonUniqueID = secured($_POST["person_unique_id"]);
        $fetch = new fetch();
        $fetch->getRecordsWithID($PersonUniqueID);
    } else if (isset($_POST["addHouseHoldMember"])) {
        $HouseHoldID = secured($_POST["houshold_id"]);
        $HouseHoldCoord = secured($_POST["household_coord"]);
        $HouseHoldNumber = secured($_POST["household_number"]);
        $HouseHoldPurokSitio = secured($_POST["purok_sitio_id"]);
        $HouseHoldMember = $_POST["household_member"];
        if (empty($HouseHoldID)) {
            $insert = new insert();
            $insert->addHouseHoldMember($HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember);
        } else {
            $update = new update();
            $update->updateHouseHoldMember($HouseHoldID, $HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember);
        }
    } else if (isset($_POST["deleteHouseHold"])) {
        $HouseHoldID = secured($_POST["deleteHouseHoldId"]);
        $delete = new delete();
        $delete->deleteHouseHold($HouseHoldID);
    } else if (isset($_POST["fetchingHouseHoldInfo"])) {
        $CategoryID = secured($_POST["category_id"]);
        $CategoryLevelID = secured($_POST["category_level_id"]);


        $fetch = new fetch();
        $fetch->fetchingHouseHoldInfo($CategoryID, $CategoryLevelID);
    } else {
        return http_response_code(404);
    }
} else if ($_SERVER["REQUEST_METHOD"] == "GET") {
    if (isset($_GET["fetch_polygons"])) {
        $fetch = new fetch();
        $fetch->getPolygon();
    } else if (isset($_GET["getCategoryList"])) {
        $fetch = new fetch();
        $fetch->getCategoryList();
    } else if (isset($_GET["getCategoryLevel"])) {
        $fetch = new fetch();
        $fetch->getCategoryLevelId(secured($_GET["categoryID"]));
    } else if (isset($_GET["getAreaSetupList"])) {
        $mainLayerId = secured($_GET["mainAreaID"]);
        $fetch = new fetch();
        $fetch->getAreaSetupList($mainLayerId);
    } else if (isset($_GET["getAllPurokSitioList"])) {
        $fetch = new fetch();
        $fetch->getAllPurokSitioList();
    } else if (isset($_GET["getAllPersonalRecords"])) {
        // $term = secured(isset($_GET["term"])  || "");
        $term = "";
        if (isset($_GET["term"])) {
            $term = secured($_GET["term"]);
        }
        $fetch = new fetch();
        $fetch->getAllPersonalRecords($term);
    } else if (isset($_GET["getHousholdList"])) {
        $fetch = new fetch();
        $fetch->getHousholdList();
    } else if (isset($_GET["loadMarkers"])) {
        $HouseHoldID = secured($_GET["houseHoldId"]);
        $fetch = new fetch();
        $fetch->loadMarkers($HouseHoldID);
    } else if (isset($_GET["fetchHousholdInfo"])) {
        $HouseholdID = secured($_GET["HouseHoldId"]);
        $fetch = new fetch();
        $fetch->fetchHousholdInfo($HouseholdID);
    } else if (isset($_GET["fetchingHouseholdCoords"])) {
        $fetch = new fetch();
        $fetch->fetchingHouseholdCoords();
    } else {
        return http_response_code(404);
    }
} else {
    ob_end_flush(header("Location: index.php"));
}
