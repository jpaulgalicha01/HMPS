<?php
$response = [];
class insert extends controller
{

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

    public function InsertAreaSetup($polygon_id, $category_coords, $CategoryID, $CategoryLevel)
    {
        $stmt = $this->insert_area_setup($polygon_id, $category_coords, $CategoryID, $CategoryLevel);
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
                'message' => 'There was an error while adding area setup.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function addPurokSitio($PurokName)
    {
        $stmt = $this->add_purok_sitio($PurokName);
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
                'message' => 'There was an error while adding purok/sitio.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function addPersonalInformation(
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
        $stmt = $this->add_personal_information(
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
                    'status' => 409,
                    'message' => $stmt['message'],
                ];
            }
        } else {
            $response = [
                'status' => 500,
                'message' => 'There was an encountered error.',
            ];
        }
        echo json_encode($response);
        return false;
    }

    public function addHouseHoldMember($HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember)
    {
        $stmt = $this->add_household_member($HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember);
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
                'message' => 'There was an encountered error.',
            ];
        }
        echo json_encode($response);
        return false;
    }
}
