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
            ORDER BY cat_name.category_id
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
    //// Updating Process
}
