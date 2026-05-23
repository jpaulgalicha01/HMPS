<?php

class update extends controller
{
    public function updatePolygon($polygonId, $coordinates)
    {
        $stmt = $this->update_polygon($polygonId, $coordinates);
        if ($stmt) {
            if ($stmt['status'] == 200) {
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
                'message' => 'There was an error while updating polygon.',
            ];
        }
        echo json_encode($response);
        return false;
    }
}
