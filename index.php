<?php

error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once 'database.php';

class ApiController
{
    private $db;

    public function __construct()
    {
        $this->db = Database::getInstance()->getConnection();
    }

    public function handleRequest()
    {
        try {
            $action = isset($_GET['action']) ? $_GET['action'] : null;

            switch ($action) {
                case 'getEntities':
                    return $this->getEntities();
                case 'getVigieScans':
                    return $this->getVigieScans();
                case 'getRecentPrices':
                    $ean13 = isset($_GET['ean13']) ? $_GET['ean13'] : null;
                    if ($ean13) {
                        return $this->getRecentPrices($ean13);
                    } else {
                        return json_encode(['success' => false, 'error' => 'Invalid action']);
                    }
                    // no break
                default:
                    return json_encode(['success' => false, 'error' => 'Invalid action']);
            }
        } catch (PDOException $e) {
            // Handle database error
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getEntities()
    {
        try {
            $stmt = $this->db->query('SELECT * FROM entities');
            $entities = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the fetched entities
            return json_encode(['success' => true, 'data' => $entities]);
        } catch (PDOException $e) {
            // Handle database error
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function getVigieScans()
    {
        try {
            $stmt = $this->db->query('SELECT * FROM vigie_scans ORDER BY vigie_scans.Date DESC limit 5');
            $vigieScans = $stmt->fetchAll(PDO::FETCH_ASSOC);

            // Return the fetched vigie scans
            return json_encode(['success' => true, 'data' => $vigieScans]);
        } catch (PDOException $e) {
            // Handle database error
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    // Function to get the 5 most recent prices for a scanned product
    public function getRecentPrices($ean13)
    {
        try {
            global $mysqli;

            // SQL query to retrieve recent prices
            $stmt = $this->db->query('SELECT vigie_scans.Price, vigie_scans.Date, entities.company_sign AS Entity_sign, entities.company_commercial_name AS Entity_commercial_name
                                      FROM vigie_scans
                                      JOIN entities 
                                      ON vigie_scans.EntityCode = entities.code 
                                      AND vigie_scans.EntityUDID = entities.uuid
                                      WHERE vigie_scans.EAN = '.$ean13.'
                                      ORDER BY vigie_scans.Date DESC
                                      LIMIT 5');
            $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return json_encode(['success' => true, 'data' => $results]);
        } catch (PDOException $e) {
            // Handle database error
            return json_encode(['success' => false, 'error' => $e->getMessage()]);
        }
    }
}

// Example of usage
$api = new ApiController();
echo $api->handleRequest();

// $ean13 = '1234567890123';
// $recentPrices = getRecentPrices($ean13);
// echo json_encode($recentPrices);
