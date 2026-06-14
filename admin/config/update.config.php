<?php

class update extends controller
{
    public function updatePolygon($polygonId, $coordinates)
    {
        $stmt = $this->update_polygon($polygonId, $coordinates);
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
                'message' => 'There was an error while updating polygon.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function UpdateCategory($CategoryID, $CategoryName, $CategoryLevelList)
    {
        $stmt = $this->update_category($CategoryID, $CategoryName, $CategoryLevelList);
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
                'message' => 'There was an error while updating category.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function UpdateAreaSetup($category_area_id, $polygon_id, $category_coords, $CategoryID, $CategoryLevel)
    {
        $stmt = $this->update_area_setup($category_area_id, $polygon_id, $category_coords, $CategoryID, $CategoryLevel);
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
                'message' => 'There was an error while updating area setup.',
            ];
        }
        echo json_encode($response);
        return false;
    }
    public function updatePurokSitio($PurokUniqueId, $PurokName)
    {
        $stmt = $this->update_purok_sitio($PurokUniqueId, $PurokName);
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
                'message' => 'There was an error while updating purok/sitio.',
            ];
        }
        echo json_encode($response);
        return false;
    }
    public function updatePersonalInformdation(
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
    ) {
        $stmt = $this->update_personal_informdation(
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
        if ($stmt) {
            if (isset($stmt['status']) && $stmt['status'] == 200) {
                $response = [
                    'status' => 200,
                    'message' => $stmt['message'],
                ];
            } else {
                $response = [
                    'status' => $stmt["status"],
                    'message' => $stmt['message'],
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an during updating data.',
            ];
        }
        echo json_encode($response);
        return false;
    }
    public function updateHouseHoldMember($HouseHoldID, $HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember)
    {
        $stmt = $this->update_household_member($HouseHoldID, $HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember);
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
                'message' => 'There was an during updating data.',
            ];
        }
        echo json_encode($response);
        return false;
    }
}
