<?php
$response = [];
class insert extends controller
{

    public function addFamilyList($FamilyName, $FamilyUniqueID)
    {
        $stmt = $this->add_family($FamilyName, $FamilyUniqueID);
        if ($stmt) {
            if (isset($stmt['status']) && $stmt['status'] == 200) {
                $response = [
                    'status' => 200,
                    'message' => $stmt['message'],
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt['message'],
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while adding family list.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function savePolygon($coordinates)
    {
        $stmt = $this->save_polygon($coordinates);
        if ($stmt) {
            if (isset($stmt['status']) && $stmt['status'] == 200) {
                $response = [
                    'status' => 200,
                    'message' => $stmt['message'],
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt['message'],
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while saving polygon.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function InsertCategory($CategoryName, $CategoryLevelList)
    {
        $stmt = $this->insert_category($CategoryName, $CategoryLevelList);
        if ($stmt) {
            if (isset($stmt['status']) && $stmt['status'] == 200) {
                $response = [
                    'status' => 200,
                    'message' => $stmt['message'],
                ];
            } else {
                $response = [
                    'status' => 409,
                    'message' => $stmt['message'],
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an error while adding category.',
            ];
        }
        echo json_encode($response);
        return false;
    }
}
