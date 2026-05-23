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
}
