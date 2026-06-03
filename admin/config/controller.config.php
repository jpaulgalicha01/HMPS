<?php


class controller extends db
{

    /// Inserting Process

    protected function add_family($FamilyName, $FamilyUniqueID)
    {
        try {
            // Update Family Name   
            if ($FamilyUniqueID != "") {
                $check_if_exists_query = $this->PlsConnect()->prepare("SELECT 1 FROM family_name_list WHERE family_unique_id = :family_unique_id LIMIT 1");
                $check_if_exists_query->bindParam(":family_unique_id", $FamilyUniqueID);
                if ($check_if_exists_query->execute()) {
                    if (!$check_if_exists_query->fetch()) {
                        return [
                            'message' => 'Family List not found.',
                        ];
                    }
                    $checking_query = $this->PlsConnect()->prepare(
                        "SELECT 1 FROM family_name_list WHERE family_name = :family_name AND family_unique_id != :family_unique_id LIMIT 1"
                    );
                    $checking_query->bindParam(":family_name", $FamilyName);
                    $checking_query->bindParam(":family_unique_id", $FamilyUniqueID);
                    $checking_query->execute();
                    if ($checking_query->fetch()) {
                        return [
                            'message' => 'Family Name is already used.',
                        ];
                    }
                    $query = $this->PlsConnect()->prepare("UPDATE family_name_list SET family_name = :family_name WHERE family_unique_id = :family_unique_id");
                    $query->bindParam(":family_name", $FamilyName);
                    $query->bindParam(":family_unique_id", $FamilyUniqueID);
                    if ($query->execute()) {
                        return [
                            'status' => 200,
                            'message' => 'Family List Updated Successfully.',
                        ];
                    } else {
                        return [
                            'message' => 'Failed to update family list.',
                        ];
                    }
                }
            }

            // Add Family Name
            else {

                $checking_query = $this->PlsConnect()->prepare(
                    "SELECT 1 FROM family_name_list WHERE family_name = :family_name LIMIT 1"
                );
                $checking_query->bindParam(":family_name", $FamilyName);
                $checking_query->execute();
                if ($checking_query->fetch()) {
                    return [
                        'message' => 'Family Name is already used.',
                    ];
                }
                $query = $this->PlsConnect()->prepare("INSERT INTO family_name_list (family_unique_id,family_name) VALUES (UUID(),:family_name)");
                $query->bindParam(":family_name", $FamilyName);
                if ($query->execute()) {
                    return [
                        'status' => 200,
                        'message' => 'Family List Added Successfully.',
                    ];
                } else {
                    return [
                        'message' => 'Failed to add family list.',
                    ];
                }
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

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

    /// Inserting Process

    /// Fetching Process

    protected function get_family_list()
    {
        try {
            $query = $this->PlsConnect()->prepare("SELECT * FROM family_name_list");
            $query->execute();
            $rows = $query->fetchAll();
            $modified = array_map(function ($row) {
                $row['family_unique_id'] = $row['family_unique_id'];
                $row['family_name'] = $row['family_name'];
                $row['family_id'] = "FAM-" . str_pad($row['family_id'], 6, '0', STR_PAD_LEFT);

                return $row;
            }, $rows);
            return $modified;
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

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
            echo json_encode($response);
            return false;
        }
        return $rows;
    }

    /// Fetching Process

    /// Deleting Process
    protected function delete_family_list($FamilyId)
    {
        try {
            $query = $this->PlsConnect()->prepare("DELETE FROM family_name_list WHERE family_unique_id = :family_unique_id");
            $query->bindParam(":family_unique_id", $FamilyId);
            if ($query->execute()) {
                return 1;
            } else {
                return "Failed to delete family list.";
            }
        } catch (PDOException $error) {
            return $error->getMessage();
        }
    }

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


    /// Updating Process
}
