<?php

class delete extends controller
{

    public function deleteFamilyList($FamilyId)
    {
        $stmt = $this->delete_family_list($FamilyId);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Family List Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
            echo json_encode($response);
            return false;
        }
    }
    public function deletePolygon($polygonId)
    {
        $stmt = $this->delete_polygon($polygonId);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Polygon Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
            echo json_encode($response);
            return false;
        }
    }

    public function deleteCategory($CategoryID)
    {
        $stmt = $this->delete_category($CategoryID);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Category Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
            echo json_encode($response);
            return false;
        }
    }
    public function DeleteAreaSetup($category_area_id)
    {
        $stmt = $this->delete_area_setup($category_area_id);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Area Setup Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
            echo json_encode($response);
            return false;
        }
    }
}
