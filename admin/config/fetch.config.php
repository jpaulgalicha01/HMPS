<?php
class fetch extends controller
{

    public function getFamilyList()
    {
        $stmt = $this->get_family_list();

        $response = [
            'status' => 200,
            'data' => $stmt,
        ];

        echo json_encode($response);
        return false;
    }
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
        $response = [
            'status' => 200,
            'data' => $stmt,
        ];
        echo json_encode($response);
        return false;
    }
}
