<?php

class delete extends controller
{

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
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while deleting polygon.',
            ];
        }
        echo json_encode($response);
        return false;
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
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while deleting category.',
            ];
        }
        echo json_encode($response);
        return false;
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
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while deleting Area Setup.',
            ];
        }
        echo json_encode($response);
        return false;
    }
    public function deletePurokSitio($PurokUniqueId)
    {
        $stmt = $this->delete_purok_sitio($PurokUniqueId);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Purok/Sitio Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while deleting Purok/Sitio.',
            ];
        }
        echo json_encode($response);
        return false;
    }
    public function deleteIndividualPersonInfo($PersonUniqueID)
    {
        $stmt = $this->delete_individual_personInfo($PersonUniqueID);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while deleting Purok/Sitio.',
            ];
        }
        echo json_encode($response);
        return false;
    }
    public function deleteHouseHold($HouseHoldID)
    {

        $stmt = $this->delete_household($HouseHoldID);
        if ($stmt) {
            if ($stmt == 1) {
                $response = [
                    'status' => 200,
                    'message' => 'Household Deleted Successfully.',
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt,
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while deleting category.',
            ];
        }
        echo json_encode($response);
        return false;
    }
}
