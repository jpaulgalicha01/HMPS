<?php


class controller extends db
{

    /// Inserting Process


    protected function save_polygon($coordinates)
    {
        try {
            $query = $this->PlsConnect()->prepare("INSERT INTO area_polygon (coordinates) VALUES (:polygon_coordinates)");
            $query->bindParam(":polygon_coordinates", $coordinates);
            if ($query->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Polygon saved successfully.',
                ];
            } else {
                return [
                    'message' => 'Failed to save polygon.',
                ];
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function insert_category($CategoryName, $CategoryLevelList)
    {
        $pdo = $this->PlsConnect();
        try {
            // Decode once here
            $CategoryLevelList = json_decode($CategoryLevelList, true);
            if (!is_array($CategoryLevelList)) {
                return ['message' => 'Invalid CategoryLevelList JSON'];
            }
            // Check if category exists
            $checking_query = $pdo->prepare(
                "SELECT 1 FROM category_name WHERE category_name = :category_name LIMIT 1"
            );
            $checking_query->bindParam(":category_name", $CategoryName);
            $checking_query->execute();
            if ($checking_query->fetch()) {
                return ['message' => 'Category Name is already used.'];
            }


            // Start transaction
            $pdo->beginTransaction();
            // Insert category
            $query = $pdo->prepare(
                "INSERT INTO category_name (category_name) VALUES (:category_name)"
            );
            $query->bindParam(":category_name", $CategoryName);
            $query->execute();
            $categoryId = $pdo->lastInsertId();
            // Insert levels
            $insertLevelQuery = $this->PlsConnect()->prepare(
                "INSERT INTO category_list_level (category_id, category_level_name, category_level_color) 
             VALUES (:category_id, :level_name, :level_color)"
            );

            foreach ($CategoryLevelList as $level) {
                $insertLevelQuery->bindParam(":category_id", $categoryId);
                $insertLevelQuery->bindParam(":level_name", $level['LevelName']);
                $insertLevelQuery->bindParam(":level_color", $level['Color']);
                $insertLevelQuery->execute();
            }
            // Commit transaction
            $pdo->commit();
            return [
                'status' => 200,
                'message' => 'Category added successfully.'
            ];
        } catch (PDOException $error) {
            // Rollback only if transaction is active
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['status' => 500, 'message' => $error->getMessage()];
        } finally {
            // Dispose/close connection if your wrapper supports it
            if (method_exists($pdo, 'dispose')) {
                $pdo->dispose();
            }
        }
    }

    protected function insert_area_setup($polygon_id, $category_coords, $CategoryID, $CategoryLevel)
    {
        try {
            $query = $this->PlsConnect()->prepare("INSERT INTO category_area (polygon_id, category_coordinates, category_id, category_level_id) VALUES (:polygon_id, :category_coords, :category_name, :category_level)");
            $query->bindParam(":polygon_id", $polygon_id);
            $query->bindParam(":category_coords", $category_coords);
            $query->bindParam(":category_name", $CategoryID);
            $query->bindParam(":category_level", $CategoryLevel);
            if ($query->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Area setup saved successfully.',
                ];
            } else {
                return [
                    'message' => 'Failed to save area setup.',
                ];
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function add_purok_sitio($PurokName)
    {
        $query = $this->PlsConnect()->prepare("SELECT 1 FROM purok_sitio_list WHERE purok_sitio_name = :purok_name LIMIT 1");
        $query->bindParam(":purok_name", $PurokName);
        if ($query->execute()) {
            if ($query->fetch()) {
                return ['message' => 'Purok/Sitio Name is already used.'];
            }
        }
        $insertQuery = $this->PlsConnect()->prepare("INSERT INTO purok_sitio_list (purok_sitio_name) VALUES (:purok_name)");
        $insertQuery->bindParam(":purok_name", $PurokName);
        if ($insertQuery->execute()) {
            return [
                'status' => 200,
                'message' => 'Purok/Sitio added successfully.',
            ];
        } else {
            return [
                'message' => 'Failed to add Purok/Sitio.',
            ];
        }
    }

    protected function add_personal_information(
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
        try {
            // 1) Check if personal information already exists by person_unique_id
            $checkByPersonId = $this->PlsConnect()->prepare(
                "SELECT 1 FROM individual_records_list WHERE person_unique_id = :person_unique_id LIMIT 1"
            );
            $checkByPersonId->bindParam(":person_unique_id", $PersonUniqueID);
            $checkByPersonId->execute();
            if ($checkByPersonId->fetch()) {
                return [
                    'message' => 'Personal information already exists for this person_unique_id.'
                ];
            }

            if (!empty($EmailAddress) || !$EmailAddress == "") {
                // 2) Check if email already exists
                $checkByEmail = $this->PlsConnect()->prepare(
                    "SELECT 1 FROM individual_records_list WHERE email_address = :email_address LIMIT 1"
                );
                $checkByEmail->bindParam(":email_address", $EmailAddress);
                $checkByEmail->execute();
                if ($checkByEmail->fetch()) {
                    return [
                        'message' => 'Email address is already registered.'
                    ];
                }
            }


            // 3) Insert data
            $insert = $this->PlsConnect()->prepare(
                "INSERT INTO individual_records_list (
                    person_unique_id,
                    phil_sys_id,
                    last_name,
                    first_name,
                    middle_name,
                    suffix,
                    birdthdate,
                    birth_place,
                    sex,
                    civil_status,
                    religion,
                    residential_address,
                    citizenship,
                    profession,
                    contact_no,
                    email_address,
                    highest_attainment_education,
                    highest_attainment_education_specific,
                    type_of_disability,
                    type_of_disability_others,
                    date_encoded
                ) VALUES (
                    UUID(),
                    :phil_sys_id,
                    :last_name,
                    :first_name,
                    :middle_name,
                    :suffix,
                    :birdthdate,
                    :birth_place,
                    :sex,
                    :civil_status,
                    :religion,
                    :residential_address,
                    :citizenship,
                    :profession,
                    :contact_no,
                    :email_address,
                    :highest_attainment_education,
                    :highest_attainment_education_specific,
                    :type_of_disability,
                    :type_of_disability_others,
                    NOW()
                )"
            );
            // $insert->bindParam(":person_unique_id", new uui());
            $insert->bindParam(":phil_sys_id", $PhilSysID);
            $insert->bindParam(":last_name", $LastName);
            $insert->bindParam(":first_name", $FirstName);
            $insert->bindParam(":middle_name", $MiddleName);
            $insert->bindParam(":suffix", $Suffix);
            $insert->bindParam(":birdthdate", $Birdthdate);
            $insert->bindParam(":birth_place", $BirthPlace);
            $insert->bindParam(":sex", $Sex);
            $insert->bindParam(":civil_status", $CivilStatus);
            $insert->bindParam(":religion", $Religion);
            $insert->bindParam(":residential_address", $ResidentialAddress);
            $insert->bindParam(":citizenship", $Citizenship);
            $insert->bindParam(":profession", $Profession);
            $insert->bindParam(":contact_no", $ContactNo);
            $insert->bindParam(":email_address", $EmailAddress);
            $insert->bindParam(":highest_attainment_education", $HighestAttainmentEducation);
            $insert->bindParam(":highest_attainment_education_specific", $HighestAttainmentEducationSpecific);
            $insert->bindParam(":type_of_disability", $TypeOfDisability);
            $insert->bindParam(":type_of_disability_others", $TypeOfDisabilityOthers);

            if ($insert->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Personal information saved successfully.'
                ];
            }

            return [
                'status' => 500,
                'message' => 'Failed to save personal information.'
            ];
        } catch (PDOException $error) {
            return [
                'status' => 500,
                'message' => $error->getMessage(),
            ];
        }
    }


    protected function add_household_member($HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember)
    {

        $pdo = $this->PlsConnect();
        try {
            $HouseHoldMember = json_decode($HouseHoldMember, true);
            if (!is_array($HouseHoldMember)) {
                return ['message' => 'Invalid HouseHoldMember JSON'];
            }

            // Check if category exists
            $checking_query = $pdo->prepare(
                "SELECT 1 FROM household_list WHERE household_number = :household_number LIMIT 1"
            );
            $checking_query->bindParam(":household_number", $HouseHoldNumber);
            $checking_query->execute();
            if ($checking_query->fetch()) {
                return ['message' => 'Hosuehold Number is already used.'];
            }
            $pdo->beginTransaction();
            $houshold_id = uniqid();
            $query = $pdo->prepare(
                "INSERT INTO household_list (`houshold_id`, `household_coord`, `household_number`, `purok_sitio_id`) VALUES (:houshold_id,:household_coord,:household_number,:purok_sitio_id)"
            );
            $query->bindParam(":houshold_id", $houshold_id);
            $query->bindParam(":household_coord", $HouseHoldCoord);
            $query->bindParam(":household_number", $HouseHoldNumber);
            $query->bindParam(":purok_sitio_id", $HouseHoldPurokSitio);
            $query->execute();
            // Insert levels
            $insertLevelQuery = $pdo->prepare(
                "INSERT INTO household_member_list (`household_number`, `person_unique_id`, `family_order`) 
             VALUES (:household_number, :person_unique_id, :family_order)"
            );

            foreach ($HouseHoldMember as $HouseHoldMemberList) {
                $insertLevelQuery->bindParam(":household_number", $houshold_id);
                $insertLevelQuery->bindParam(":person_unique_id", $HouseHoldMemberList['person_unique_id']);
                $insertLevelQuery->bindParam(":family_order", $HouseHoldMemberList['family_order']);
                $insertLevelQuery->execute();
            }

            $pdo->commit();
            return [
                'status' => 200,
                'message' => 'Household added successfully.'
            ];
        } catch (PDOException $error) {

            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            return ['status' => 500, 'message' => $error->getMessage()];
        } finally {
            // Dispose/close connection if your wrapper supports it
            if (method_exists($pdo, 'dispose')) {
                $pdo->dispose();
            }
        }
    }

    /// Inserting Process

    /// Fetching Process

    protected function get_polygon()
    {
        try {
            $query = $this->PlsConnect()->prepare("SELECT * FROM area_polygon");
            $query->execute();
            $rows = $query->fetchAll();
            return $rows;
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function get_category_list()
    {
        try {
            $stmt = $this->PlsConnect()->prepare("
            SELECT 
                cat_name.category_id,
                cat_name.category_name,
                cat_list_level.category_level_id,
                cat_list_level.category_level_name,
                cat_list_level.category_level_color
            FROM category_name cat_name
            LEFT JOIN category_list_level cat_list_level 
                ON cat_name.category_id = cat_list_level.category_id
            ORDER BY cat_name.category_id, cat_list_level.category_level_id asc
        ");
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            $categories = [];

            foreach ($rows as $row) {
                $catId = $row['category_id'];

                // Initialize category if not yet created
                if (!isset($categories[$catId])) {
                    $categories[$catId] = [
                        "CategoryID" => $row['category_id'],
                        "CategoryName" => $row['category_name'],
                        "CategoryListLevel" => []
                    ];
                }

                // Add level if exists
                if (!empty($row['category_level_name'])) {
                    $categories[$catId]["CategoryListLevel"][] = [
                        "CategoryLevelId" => $row["category_level_id"],
                        "CatLevelName" => $row['category_level_name'],
                        "Color" => $row['category_level_color']
                    ];
                }
            }
            // Return as indexed array
            return array_values($categories);
        } catch (PDOException $error) {
            return ['status' => 500, 'message' => $error->getMessage()];
        }
    }

    protected function get_category_details_with_id($CategoryID)
    {
        try {
            $stmt = $this->PlsConnect()->prepare("
            SELECT 
                cat_name.category_id,
                cat_name.category_name,
                cat_list_level.category_level_name,
                cat_list_level.category_level_color,
                cat_list_level.category_level_id
            FROM category_name cat_name
            LEFT JOIN category_list_level cat_list_level 
                ON cat_name.category_id = cat_list_level.category_id
            WHERE cat_name.category_id = :category_id
            order by cat_list_level.category_level_id asc
        ");
            $stmt->bindParam(":category_id", $CategoryID);
            $stmt->execute();
            $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

            if (empty($rows)) {
                return ['status' => 404, 'message' => 'Category not found.'];
            }

            $categoryDetails = [
                "CategoryID" => $rows[0]['category_id'],
                "CategoryName" => $rows[0]['category_name'],
                "CategoryListLevel" => []
            ];

            foreach ($rows as $row) {
                if (!empty($row['category_level_name'])) {
                    $categoryDetails["CategoryListLevel"][] = [
                        "CategoryLevelID" => $row['category_level_id'],
                        "CatLevelName" => $row['category_level_name'],
                        "Color" => $row['category_level_color']
                    ];
                }
            }

            return $categoryDetails;
        } catch (PDOException $error) {
            return ['status' => 500, 'message' => $error->getMessage()];
        }
    }

    protected function get_category_all()
    {
        $stmt = $this->PlsConnect()->prepare("
            SELECT * FROM category_name
        ");
        $stmt->execute();
        return $stmt;
    }
    protected function get_category_level_id($categoryID)
    {
        $stmt = $this->PlsConnect()->prepare("
            SELECT category_level_id,category_level_name,category_level_color FROM category_list_level WHERE category_id = :category_id
        ");
        $stmt->bindParam(":category_id", $categoryID);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }


    protected function get_area_setup_list($mainLayerId)
    {
        $stmt = $this->PlsConnect()->prepare("
            SELECT 
            category_area.category_area_id, category_area.category_id,category_name.category_name, category_list_level.category_level_id,category_list_level.category_level_name,category_list_level.category_level_color,category_area.category_coordinates
            FROM area_polygon
            inner JOIN category_area on area_polygon.polygon_id = category_area.polygon_id
            INNER JOIN category_name on category_area.category_id = category_name.category_id
            INNER JOIN category_list_level on category_area.category_level_id = category_list_level.category_level_id
            WHERE area_polygon.polygon_id = :main_layer_id
            GROUP BY
            category_area.category_area_id, category_area.category_id,category_name.category_name, category_list_level.category_level_id,category_list_level.category_level_name,category_list_level.category_level_color,category_area.category_coordinates    
        ");
        $stmt->bindParam(":main_layer_id", $mainLayerId);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        if (!$rows) {
            $response = [
                'status' => 500,
                'message' => 'No area setup found for the given main layer ID.'
            ];
            return $response;
        }
        return $rows;
    }

    protected function get_all_purok_sitio_list()
    {
        try {
            $query = $this->PlsConnect()->prepare("SELECT purok_sitio_id, purok_sitio_name FROM purok_sitio_list");
            $query->execute();
            $rows = $query->fetchAll();
            return $rows;
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function get_all_personal_records($term)
    {
        try {
            $query = $this->PlsConnect()->prepare("SELECT
                    person_unique_id,
                    CONCAT('REC-', YEAR(date_encoded), '-', LPAD(preson_id, 6, '0')) AS reference_number,
                    CONCAT(
                        first_name, ' ', 
                        middle_name, ' ', 
                        last_name, 
                        CASE 
                            WHEN suffix IS NOT NULL AND suffix <> '' THEN CONCAT(', ', suffix) 
                            ELSE '' 
                        END
                    ) AS full_name
                FROM individual_records_list 
                WHERE UPPER(
                    CONCAT(
                        first_name, ' ', 
                        middle_name, ' ', 
                        last_name, 
                        CASE 
                            WHEN suffix IS NOT NULL AND suffix <> '' THEN CONCAT(', ', suffix) 
                            ELSE '' 
                        END
                    )
                ) LIKE UPPER(:term)
            ");
            // Add wildcards here in PHP
            $likeTerm = "%{$term}%";
            $query->bindParam(":term", $likeTerm, PDO::PARAM_STR);
            $query->execute();
            $rows = $query->fetchAll();
            $modified = array_map(function ($row) {
                $row['person_unique_id'] = $row['person_unique_id'];
                $row['reference_number'] = $row['reference_number'];
                $row['full_name'] = $row['full_name'];
                return $row;
            }, $rows);
            return $modified;
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function get_records_with_id($PersonUniqueID)
    {
        $query = $this->PlsConnect()->prepare("SELECT person_unique_id,
                    phil_sys_id,
                    last_name,
                    first_name,
                    middle_name,
                    suffix,
                    birdthdate,
                    birth_place,
                    sex,
                    civil_status,
                    religion,
                    residential_address,
                    citizenship,
                    profession,
                    contact_no,
                    email_address,
                    highest_attainment_education,
                    highest_attainment_education_specific,
                    type_of_disability,
                    type_of_disability_others
                    FROM individual_records_list
                    WHERE person_unique_id = :person_unique_id
                    ");
        $query->bindParam(":person_unique_id", $PersonUniqueID);
        $query->execute();
        $rows = $query->fetchAll();

        $modified = array_map(function ($row) {
            $row['person_unique_id'] = $row['person_unique_id'];
            $row['phil_sys_id'] = $row['phil_sys_id'];
            $row['last_name'] = $row['last_name'];
            $row['first_name'] = $row['first_name'];
            $row['middle_name'] = $row['middle_name'];
            $row['suffix'] = $row['suffix'];
            $row['birdthdate'] = $row['birdthdate'];
            $row['birth_place'] = $row['birth_place'];
            $row['sex'] = $row['sex'];
            $row['civil_status'] = $row['civil_status'];
            $row['religion'] = $row['religion'];
            $row['residential_address'] = $row['residential_address'];
            $row['citizenship'] = $row['citizenship'];
            $row['profession'] = $row['profession'];
            $row['contact_no'] = $row['contact_no'];
            $row['email_address'] = $row['email_address'];
            $row['highest_attainment_education'] = $row['highest_attainment_education'];
            $row['highest_attainment_education_specific'] = $row['highest_attainment_education_specific'];
            $row['type_of_disability'] = $row['type_of_disability'];
            $row['type_of_disability_others'] = $row['type_of_disability_others'];
            return $row;
        }, $rows);

        return [
            'status' => 200,
            'data' => $modified
        ];
    }

    protected function get_all_sitio_purok()
    {
        $stmt = $this->PlsConnect()->prepare("SELECT purok_sitio_id,purok_sitio_name FROM purok_sitio_list");
        $stmt->execute();
        return $stmt;
    }

    protected function get_houshold_list()
    {
        $stmt = $this->PlsConnect()->prepare("SELECT 
                    hl.houshold_id,
                    hl.household_number,
                    ps.purok_sitio_name, 
                    CONCAT(
                COALESCE(
                    (SELECT UPPER(
                        GROUP_CONCAT(
                            CONCAT(
                                ir.first_name, ' ', 
                                ir.middle_name, ' ', 
                                ir.last_name, 
                                CASE 
                                    WHEN ir.suffix IS NOT NULL AND ir.suffix <> '' 
                                    THEN CONCAT(', ', ir.suffix) 
                                    ELSE '' 
                                END
                            )
                            SEPARATOR ' AND '
                        )
                    )
                    FROM household_member_list hm
                    left JOIN individual_records_list ir 
                        ON hm.person_unique_id = ir.person_unique_id
                    WHERE hm.household_number = hl.houshold_id 
                        AND hm.family_order IN (1,2)
                ), 'No Head')
                , ' with ',
                (SELECT COUNT(*) 
                    FROM household_member_list hm2
                    WHERE hm2.household_number = hl.houshold_id 
                    AND hm2.family_order NOT IN (1,2)),
                ' member(s)'
                ) AS FamilyMember

                FROM household_list hl
                left JOIN purok_sitio_list ps 
                    ON hl.purok_sitio_id = ps.purok_sitio_id;
        ");
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $HousholdList = array_map(function ($row) {
            $row["houshold_id"] = $row["houshold_id"];
            $row["household_number"] = $row["household_number"];
            $row["purok_sitio_name"] = $row["purok_sitio_name"];
            $row["FamilyMember"] = $row["FamilyMember"];
            return $row;
        }, $rows);

        return $HousholdList;
    }

    protected function load_markers($HouseHoldID)
    {
        if (empty($HouseHoldID) || $HouseHoldID == 0) {
            return null;
        }

        $stmt = $this->PlsConnect()->prepare("
        SELECT household_coord 
        FROM household_list 
        WHERE houshold_id = :houshold_id 
        LIMIT 1
    ");
        $stmt->bindParam(":houshold_id", $HouseHoldID);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }

    protected function fetch_houshold_info($HouseHoldID)
    {


        if (empty($HouseHoldID) || $HouseHoldID == 0) {
            return null;
        }

        $stmt = $this->PlsConnect()->prepare(" SELECT 
            household_list.houshold_id,
            household_list.household_coord,
            household_list.household_number,
            household_list.purok_sitio_id,
            individual_records_list.person_unique_id,
            CONCAT(
                        first_name, ' ', 
                        middle_name, ' ', 
                        last_name, 
                        CASE 
                            WHEN suffix IS NOT NULL AND suffix <> '' THEN CONCAT(', ', suffix) 
                            ELSE '' 
                        END
                    ) AS full_name,
            household_member_list.family_order,
            purok_sitio_list.purok_sitio_name
            FROM household_list 
            left join household_member_list on household_list.houshold_id = household_member_list.household_number
            left join individual_records_list on household_member_list.person_unique_id = individual_records_list.person_unique_id
            left join purok_sitio_list on household_list.purok_sitio_id = purok_sitio_list.purok_sitio_id

            where `houshold_id`= :houshold_id
        ");
        $stmt->bindParam(":houshold_id", $HouseHoldID);
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        if (empty($rows)) {
            return ['status' => 404, 'message' => 'Household Number not found.'];
        }

        $HouseHoldList = [
            "houshold_id" => $rows[0]['houshold_id'],
            "household_coord" => $rows[0]['household_coord'],
            "household_number" => $rows[0]['household_number'],
            "purok_sitio_id" => $rows[0]['purok_sitio_id'],
            "purok_sitio_name" => $rows[0]['purok_sitio_name'],
            "HouseHoldMemberList" => []
        ];

        foreach ($rows as $row) {
            if (!empty($row['person_unique_id'])) {
                $HouseHoldList["HouseHoldMemberList"][] = [
                    "person_unique_id" => $row['person_unique_id'],
                    "full_name" => $row["full_name"],
                    "family_order" => $row['family_order'],
                ];
            }
        }

        return $HouseHoldList;
    }


    protected function count_population()
    {
        $stmt = $this->PlsConnect()->prepare("SELECT * FROM individual_records_list");
        $stmt->execute();
        return $stmt;
    }

    protected function count_household()
    {
        $stmt = $this->PlsConnect()->prepare("SELECT * FROM household_list");
        $stmt->execute();
        return $stmt;
    }


    protected function fetching_household_coords()
    {

        $stmt = $this->PlsConnect()->prepare("SELECT `houshold_id`, `household_coord`, `household_number` FROM `household_list`");
        $stmt->execute();
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $rows;
    }


    protected function fetching_house_hold_info($CategoryID, $CategoryLevelID)
    {
        $query = " SELECT 
            hl.houshold_id AS household_id,
            hl.household_number,
            CONCAT(
                COALESCE(
                    (SELECT UPPER(
                        GROUP_CONCAT(
                            CONCAT(
                                ir.first_name, ' ', 
                                ir.middle_name, ' ', 
                                ir.last_name, 
                                CASE 
                                WHEN ir.suffix IS NOT NULL AND ir.suffix <> '' 
                                THEN CONCAT(', ', ir.suffix) 
                                ELSE '' 
                                END
                            )
                            SEPARATOR ' AND '
                        )
                    )
                    FROM household_member_list hm
                    left JOIN individual_records_list ir 
                    ON hm.person_unique_id = ir.person_unique_id
                    WHERE hm.household_number = hl.houshold_id 
                    AND hm.family_order IN (1,2)
                    ), 'No Head')
                , ' with ',
                (SELECT COUNT(*) 
                FROM household_member_list hm2
                WHERE hm2.household_number = hl.houshold_id 
                AND hm2.family_order NOT IN (1,2)),
                ' member(s)'
            ) AS FamilyMember
            FROM household_list hl
            JOIN category_area c on ST_Within( hl.location,c.boundary)
            where c.category_id = :category_id AND c.category_level_id = :category_level_id
        ";
        $stmt = $this->PlsConnect()->prepare($query);
        $stmt->bindParam(":category_id", $CategoryID);
        $stmt->bindParam(":category_level_id", $CategoryLevelID);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        return $rows;
    }


    /// Fetching Process

    /// Deleting Process
    protected function delete_polygon($polygonId)
    {
        try {
            $query = $this->PlsConnect()->prepare("DELETE FROM area_polygon WHERE polygon_id = :polygon_id");
            $query->bindParam(":polygon_id", $polygonId);
            if ($query->execute()) {
                return 1;
            } else {
                return "Failed to delete polygon.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function delete_category($CategoryID)
    {
        try {
            $query = $this->PlsConnect()->prepare("DELETE FROM category_name WHERE category_id = :category_id");
            $query->bindParam(":category_id", $CategoryID);
            if ($query->execute()) {
                $deleteLevelsQuery = $this->PlsConnect()->prepare(
                    "DELETE FROM category_list_level WHERE category_id = :category_id"
                );
                $deleteLevelsQuery->bindParam(":category_id", $CategoryID);
                $deleteLevelsQuery->execute();
                return 1;
            } else {
                return "Failed to delete category.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function delete_area_setup($category_area_id)
    {
        try {
            $query = $this->PlsConnect()->prepare("DELETE FROM category_area WHERE category_area_id = :category_area_id");
            $query->bindParam(":category_area_id", $category_area_id);
            if ($query->execute()) {
                return 1;
            } else {
                return "Failed to delete area setup.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }
    protected function delete_purok_sitio($PurokUniqueId)
    {
        try {
            $checking_query = $this->PlsConnect()->prepare("SELECT 1 FROM purok_sitio_list WHERE purok_sitio_id = :purok_sitio_id LIMIT 1");
            $checking_query->bindParam(":purok_sitio_id", $PurokUniqueId);
            if ($checking_query->execute()) {
                if (!$checking_query->fetch()) {
                    return "Purok/Sitio not found.";
                }
            }

            $query = $this->PlsConnect()->prepare("DELETE FROM purok_sitio_list WHERE purok_sitio_id = :purok_sitio_id");
            $query->bindParam(":purok_sitio_id", $PurokUniqueId);
            if ($query->execute()) {
                return 1;
            } else {
                return "Failed to delete Purok/Sitio.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function delete_individual_personInfo($PersonUniqueID)
    {
        try {
            $checking_query = $this->PlsConnect()->prepare("SELECT 1 FROM individual_records_list WHERE person_unique_id = :person_unique_id LIMIT 1");
            $checking_query->bindParam(":person_unique_id", $PersonUniqueID);
            if ($checking_query->execute()) {
                if (!$checking_query->fetch()) {
                    return "No Data Found.";
                }
            }

            $query = $this->PlsConnect()->prepare("DELETE FROM individual_records_list WHERE person_unique_id = :person_unique_id");
            $query->bindParam(":person_unique_id", $PersonUniqueID);
            if ($query->execute()) {
                return 1;
            } else {
                return "Failed to delete information.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }


    protected function delete_household($HouseHoldID)
    {
        try {
            $query = $this->PlsConnect()->prepare("DELETE FROM household_list WHERE houshold_id = :houshold_id");
            $query->bindParam(":houshold_id", $HouseHoldID);
            if ($query->execute()) {
                $deleteLevelsQuery = $this->PlsConnect()->prepare(
                    "DELETE FROM household_member_list WHERE household_number = :household_number"
                );
                $deleteLevelsQuery->bindParam(":household_number", $HouseHoldID);
                $deleteLevelsQuery->execute();
                return 1;
            } else {
                return "Failed to Household.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    /// Deleting Process


    //// Updating Process
    protected function update_polygon($polygonId, $coordinates)
    {
        try {
            $checking_query = $this->PlsConnect()->prepare(
                "SELECT 1 FROM area_polygon WHERE polygon_id = :polygon_id LIMIT 1"
            );
            if ($checking_query->execute([':polygon_id' => $polygonId])) {
                if (!$checking_query->fetch()) {
                    return [
                        'message' => 'Polygon not found.',
                    ];
                }
            }
            $query = $this->PlsConnect()->prepare("UPDATE area_polygon SET coordinates = :coordinates WHERE polygon_id = :polygon_id");
            $query->bindParam(":coordinates", $coordinates);
            $query->bindParam(":polygon_id", $polygonId);
            if ($query->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Polygon updated successfully.',
                ];
            } else {
                return [
                    'message' => 'Failed to update polygon.',
                ];
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function update_category($CategoryID, $CategoryName, $CategoryLevelList)
    {
        $pdo = $this->PlsConnect();
        try {
            // Decode once here
            $CategoryLevelList = json_decode($CategoryLevelList, true);
            if (!is_array($CategoryLevelList)) {
                return ['message' => 'Invalid CategoryLevelList JSON'];
            }
            // Check if category exists
            $checking_query = $pdo->prepare(
                "SELECT 1 FROM category_name WHERE category_id = :category_id LIMIT 1"
            );
            $checking_query->bindParam(":category_id", $CategoryID);
            $checking_query->execute();
            if (!$checking_query->fetch()) {
                return ['message' => 'Category not found.'];
            }

            // Check for duplicate category name
            $duplicate_check_query = $pdo->prepare(
                "SELECT 1 FROM category_name WHERE category_name = :category_name AND category_id != :category_id LIMIT 1"
            );
            $duplicate_check_query->bindParam(":category_name", $CategoryName);
            $duplicate_check_query->bindParam(":category_id", $CategoryID);
            $duplicate_check_query->execute();
            if ($duplicate_check_query->fetch()) {
                return ['message' => 'Category Name is already used.'];
            }

            // Start transaction
            $pdo->beginTransaction();

            // Update category name
            $updateQuery = $pdo->prepare(
                "UPDATE category_name SET category_name = :category_name WHERE category_id = :category_id"
            );
            $updateQuery->bindParam(":category_name", $CategoryName);
            $updateQuery->bindParam(":category_id", $CategoryID);
            $updateQuery->execute();

            // Delete existing levels
            $deleteLevelsQuery = $pdo->prepare(
                "DELETE FROM category_list_level WHERE category_id = :category_id"
            );
            $deleteLevelsQuery->bindParam(":category_id", $CategoryID);
            $deleteLevelsQuery->execute();

            // // Insert new levels
            $insertLevelQuery = $pdo->prepare(
                "INSERT INTO category_list_level (category_id, category_level_name, category_level_color) 
             VALUES (:category_id, :level_name, :level_color)"
            );

            foreach ($CategoryLevelList as $level) {
                $insertLevelQuery->bindParam(":category_id", $CategoryID);
                $insertLevelQuery->bindParam(":level_name", $level['LevelName']);
                $insertLevelQuery->bindParam(":level_color", $level['Color']);
                $insertLevelQuery->execute();
            }
            // Commit transaction
            $pdo->commit();

            return ['status' => 200, 'message' => 'Category updated successfully.'];
        } catch (PDOException $error) {
            // Rollback transaction on error
            $pdo->rollback();
            return ['message' => 'Error updating category: ' . $error->getMessage()];
        }
    }


    protected function update_area_setup($category_area_id, $polygon_id, $category_coords, $CategoryID, $CategoryLevel)
    {
        try {
            $checking_query = $this->PlsConnect()->prepare(
                "SELECT 1 FROM category_area WHERE category_area_id = :category_area_id LIMIT 1"
            );
            if ($checking_query->execute([':category_area_id' => $category_area_id])) {
                if (!$checking_query->fetch()) {
                    return [
                        'message' => 'Area setup not found.',
                    ];
                }
            }
            $query = $this->PlsConnect()->prepare("UPDATE category_area SET polygon_id = :polygon_id, category_coordinates = :category_coords, category_id = :category_name, category_level_id = :category_level WHERE category_area_id = :category_area_id");
            $query->bindParam(":polygon_id", $polygon_id);
            $query->bindParam(":category_coords", $category_coords);
            $query->bindParam(":category_name", $CategoryID);
            $query->bindParam(":category_level", $CategoryLevel);
            $query->bindParam(":category_area_id", $category_area_id);
            if ($query->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Area setup updated successfully.',
                ];
            } else {
                return [
                    'message' => 'Failed to update area setup.',
                ];
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function update_purok_sitio($PurokUniqueId, $PurokName)
    {
        try {
            $checking_query = $this->PlsConnect()->prepare(
                "SELECT 1 FROM purok_sitio_list WHERE purok_sitio_id = :purok_id LIMIT 1"
            );
            $checking_query->bindParam(":purok_id", $PurokUniqueId);
            if ($checking_query->execute()) {
                if (!$checking_query->fetch()) {
                    return [
                        'message' => 'Purok/Sitio not found.',
                    ];
                }
            }
            $query = $this->PlsConnect()->prepare("UPDATE purok_sitio_list SET purok_sitio_name = :purok_name WHERE purok_sitio_id = :purok_id");
            $query->bindParam(":purok_name", $PurokName);
            $query->bindParam(":purok_id", $PurokUniqueId);
            if ($query->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Purok/Sitio updated successfully.',
                ];
            } else {
                return [
                    'message' => 'Failed to update Purok/Sitio.',
                ];
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

    protected function update_personal_informdation(
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
        try {
            // 1) Check if person_unique_id exists
            $checking_query = $this->PlsConnect()->prepare(
                "SELECT 1 FROM individual_records_list WHERE person_unique_id = :person_unique_id LIMIT 1"
            );
            $checking_query->bindParam(":person_unique_id", $PersonUniqueID);
            $checking_query->execute();
            if (!$checking_query->fetch()) {
                return [
                    'status' => 404,
                    'message' => 'Personal Information of this data is not found',
                ];
            }

            // 2) Check email uniqueness (only if changed / if email exists for other person)
            if (!empty($EmailAddress)) {
                $checkingEmail = $this->PlsConnect()->prepare(
                    "SELECT 1 FROM individual_records_list WHERE email_address = :email_address AND person_unique_id != :person_unique_id LIMIT 1"
                );
                $checkingEmail->bindParam(':email_address', $EmailAddress);
                $checkingEmail->bindParam(':person_unique_id', $PersonUniqueID);
                $checkingEmail->execute();

                if ($checkingEmail->fetch()) {
                    return [
                        'status' => 409,
                        'message' => 'Email address is already use',
                    ];
                }
            }

            // 3) Update record
            $query = $this->PlsConnect()->prepare(
                "UPDATE individual_records_list SET
                    phil_sys_id = :phil_sys_id,
                    last_name = :last_name,
                    first_name = :first_name,
                    middle_name = :middle_name,
                    suffix = :suffix,
                    birdthdate = :birdthdate,
                    birth_place = :birth_place,
                    sex = :sex,
                    civil_status = :civil_status,
                    religion = :religion,
                    residential_address = :residential_address,
                    citizenship = :citizenship,
                    profession = :profession,
                    contact_no = :contact_no,
                    email_address = :email_address,
                    highest_attainment_education = :highest_attainment_education,
                    highest_attainment_education_specific = :highest_attainment_education_specific,
                    type_of_disability = :type_of_disability,
                    type_of_disability_others = :type_of_disability_others
                 WHERE person_unique_id = :person_unique_id"
            );

            $query->bindParam(':person_unique_id', $PersonUniqueID);
            $query->bindParam(':phil_sys_id', $PhilSysID);
            $query->bindParam(':last_name', $LastName);
            $query->bindParam(':first_name', $FirstName);
            $query->bindParam(':middle_name', $MiddleName);
            $query->bindParam(':suffix', $Suffix);
            $query->bindParam(':birdthdate', $Birdthdate);
            $query->bindParam(':birth_place', $BirthPlace);
            $query->bindParam(':sex', $Sex);
            $query->bindParam(':civil_status', $CivilStatus);
            $query->bindParam(':religion', $Religion);
            $query->bindParam(':residential_address', $ResidentialAddress);
            $query->bindParam(':citizenship', $Citizenship);
            $query->bindParam(':profession', $Profession);
            $query->bindParam(':contact_no', $ContactNo);
            $query->bindParam(':email_address', $EmailAddress);
            $query->bindParam(':highest_attainment_education', $HighestAttainmentEducation);
            $query->bindParam(':highest_attainment_education_specific', $HighestAttainmentEducationSpecific);
            $query->bindParam(':type_of_disability', $TypeOfDisability);
            $query->bindParam(':type_of_disability_others', $TypeOfDisabilityOthers);

            if ($query->execute()) {
                return [
                    'status' => 200,
                    'message' => 'Personal information updated successfully.',
                ];
            }

            return [
                'status' => 500,
                'message' => 'Failed to update personal information.',
            ];
        } catch (PDOException $error) {
            return [
                'status' => 500,
                'message' => $error->getMessage(),
            ];
        }
    }


    protected function update_household_member($HouseHoldID, $HouseHoldCoord, $HouseHoldNumber, $HouseHoldPurokSitio, $HouseHoldMember)
    {
        $pdo = $this->PlsConnect();
        try {
            // Decode once here
            $HouseHoldMember = json_decode($HouseHoldMember, true);
            if (!is_array($HouseHoldMember)) {
                return ['message' => 'Invalid HouseHoldMember JSON'];
            }
            // Check if category exists
            $checking_query = $pdo->prepare(
                "SELECT 1 FROM household_list WHERE houshold_id = :houshold_id LIMIT 1"
            );
            $checking_query->bindParam(":houshold_id", $HouseHoldID);
            $checking_query->execute();
            if (!$checking_query->fetch()) {
                return ['message' => 'Household not found.'];
            }

            // Check for duplicate category name
            $duplicate_check_query = $pdo->prepare(
                "SELECT 1 FROM household_list WHERE houshold_id = :houshold_id AND household_number != :household_number LIMIT 1"
            );
            $duplicate_check_query->bindParam(":houshold_id", $CategoryName);
            $duplicate_check_query->bindParam(":household_number", $HouseHoldNumber);
            $duplicate_check_query->execute();
            if ($duplicate_check_query->fetch()) {
                return ['message' => 'Household Number is already used.'];
            }

            // Start transaction
            $pdo->beginTransaction();
            // Update category name
            $updateQuery = $pdo->prepare(
                "UPDATE `household_list` SET `household_coord`=:household_coord,`household_number`=:household_number,`purok_sitio_id`=:purok_sitio_id WHERE `houshold_id`=:houshold_id"
            );
            $updateQuery->bindParam(":household_coord", $HouseHoldCoord);
            $updateQuery->bindParam(":household_number", $HouseHoldNumber);
            $updateQuery->bindParam(":purok_sitio_id", $HouseHoldPurokSitio);
            $updateQuery->bindParam(":houshold_id", $HouseHoldID);
            $updateQuery->execute();

            // Delete existing levels
            $deleteLevelsQuery = $pdo->prepare(
                "DELETE FROM household_member_list WHERE household_number = :household_number"
            );
            $deleteLevelsQuery->bindParam(":household_number", $HouseHoldID);
            $deleteLevelsQuery->execute();
            // // Insert new levels
            $insertLevelQuery = $pdo->prepare(
                "INSERT INTO household_member_list (`household_number`, `person_unique_id`, `family_order`) 
             VALUES (:household_number, :person_unique_id, :family_order)"
            );

            foreach ($HouseHoldMember as $HouseHoldMemberList) {
                $insertLevelQuery->bindParam(":household_number", $HouseHoldID);
                $insertLevelQuery->bindParam(":person_unique_id", $HouseHoldMemberList['person_unique_id']);
                $insertLevelQuery->bindParam(":family_order", $HouseHoldMemberList['family_order']);
                $insertLevelQuery->execute();
            }
            // Commit transaction
            $pdo->commit();

            return ['status' => 200, 'message' => 'Household updated successfully.'];
        } catch (PDOException $error) {
            // Rollback transaction on error
            $pdo->rollback();
            return ['message' => 'Error updating Household: ' . $error->getMessage()];
        }
    }
    /// Updating Process
}
