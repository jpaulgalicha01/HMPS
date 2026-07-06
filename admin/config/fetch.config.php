<?php
class fetch extends controller
{

    public function CountPopulation()
    {

        $stmt = $this->count_population();
        if ($stmt->rowCount()) {
            echo $stmt->rowCount();
        } else {
            echo "0";
        }
    }

    public function CountHouseHold()
    {

        $stmt = $this->count_household();
        if ($stmt->rowCount()) {
            echo $stmt->rowCount();
        } else {
            echo "0";
        }
    }


    // public function getFamilyList()
    // {
    //     $stmt = $this->get_family_list();

    //     $response = [
    //         'status' => 200,
    //         'data' => $stmt,
    //     ];

    //     echo json_encode($response);
    //     return false;
    // }
    public function getPolygon()
    {
        $stmt = $this->get_polygon();
        $polygons = [];
        if ($stmt) {
            foreach ($stmt as $row) {
                $polygons[] = [
                    'id' => $row['polygon_id'],
                    'coordinates' => json_decode($row['coordinates']),
                ];
            }
        }
        echo json_encode($polygons);
        return false;
    }
    public function getCategoryList()
    {
        $stmt = $this->get_category_list();
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }


    public function getCategoryDetailsWithID($CategoryID)
    {
        $stmt = $this->get_category_details_with_id($CategoryID);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }

    public function getCategoryAll()
    {
        $stmt  = $this->get_category_all();
        return $stmt;
    }

    public function getCategoryLevelId($categoryID)
    {
        $stmt = $this->get_category_level_id($categoryID);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }
    public function getAreaSetupList($mainLayerId)
    {
        $stmt = $this->get_area_setup_list($mainLayerId);
        if (isset($stmt['status'])) {
            $response = [
                'status' => $stmt["status"],
                'message' => $stmt["message"]
            ];
        } else {
            $response = [
                'status' => 200,
                'data' => $stmt,
            ];
        }

        echo json_encode($response);
        return false;
    }
    public function getAllPurokSitioList()
    {
        $stmt = $this->get_all_purok_sitio_list();
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }

    public function getAllPersonalRecords($term)
    {
        $stmt = $this->get_all_personal_records($term);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }
    public function getRecordsWithID($PersonUniqueID)
    {
        $stmt = $this->get_records_with_id($PersonUniqueID);
        echo json_encode($stmt);
        return false;
    }
    public function getAllSitioPurok()
    {
        $stmt = $this->get_all_sitio_purok();
        return $stmt;
    }

    public function getHousholdList()
    {
        $stmt = $this->get_houshold_list(null, null, null);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }

    public function getHousholdListWithCategory($categoryId, $filterType, $keyword)
    {
        $stmt = $this->get_houshold_list_with_category($categoryId, $filterType, $keyword);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }

    public function loadMarkers($HouseHoldID)
    {
        $stmt = $this->load_markers($HouseHoldID);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }
    public function fetchHousholdInfo($HouseholdID)
    {

        $stmt = $this->fetch_houshold_info($HouseholdID);
        if (isset($stmt['status'])) {
            $response = [
                'status' => $stmt["status"],
                'message' => $stmt["message"]
            ];
        } else {
            $response = [
                'status' => 200,
                'data' => $stmt,
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function fetchingHouseholdCoords()
    {
        $stmt = $this->fetching_household_coords();
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }


    public function fetchingHouseHoldInfo($CategoryID, $CategoryLevelID)
    {
        $stmt = $this->fetching_house_hold_info($CategoryID, $CategoryLevelID);
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }
}
