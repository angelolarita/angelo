<?php
require_once __DIR__ . '/../core/Database.php';
require_once __DIR__ . '/../core/Udf.php';

$db = new Database(); // Instantiate the Database class
$pdo = $db->pdo; 

function getEmploymentData($pdo) {
    $stmt = $pdo->prepare("
       SELECT 
           graduates.course,
           graduates.graduation_year,
           employment_survey.employment_status,
           SUM(CASE WHEN employment_survey.employed = 'yes' THEN 1 ELSE 0 END) AS employed,
           COUNT(*) AS total
       FROM graduates
       LEFT JOIN employment_survey ON graduates.id = employment_survey.graduate_id
       GROUP BY 
           graduates.course, 
           graduates.graduation_year,
           employment_survey.employment_status
    ");
    
    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>