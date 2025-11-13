<?php 
/**
 * This file contains the Backup_Database class wich performs
 * a partial or complete backup of any given MySQL database
 * @author Daniel López Azaña <daniloaz@gmail.com>
 * @version 1.0
 */

// We DO NOT include connection.php. This script makes its own connection.

/**
 * Get credentials from Railway Environment Variables
 */
define("DB_USER", getenv('MYSQL_USER'));
define("DB_PASSWORD", getenv('MYSQL_PASSWORD'));
define("DB_NAME", getenv('MYSQL_DATABASE'));
define("DB_HOST", getenv('MYSQL_HOST'));
define("DB_PORT", getenv('MYSQL_PORT')); // Get the Railway port

/**
 * Define Backup behavior
 */
define("BACKUP_DIR", '../backup'); // This is the folder *inside* the container
define("TABLES", '*');
define('IGNORE_TABLES',array()); // Tables to ignore
define("CHARSET", 'utf8');
define("GZIP_BACKUP_FILE", false); 
define("DISABLE_FOREIGN_KEY_CHECKS", true); 
define("BATCH_SIZE", 1000); 

/**
 * The Backup_Database class
 */
class Backup_Database {
    var $host;
    var $username;
    var $passwd;
    var $dbName;
    var $charset;
    var $port; // <-- ADDED PORT
    var $conn;
    var $backupDir;
    var $backupFile;
    var $gzipBackupFile;
    var $output;
    var $disableForeignKeyChecks;
    var $batchSize;

    public function __construct($host, $username, $passwd, $dbName, $port, $charset = 'utf8') {
        $this->host                    = $host;
        $this->username                = $username;
        $this->passwd                  = $passwd;
        $this->dbName                  = $dbName;
        $this->charset                 = $charset;
        $this->port                    = $port; // <-- ADDED PORT
        $this->conn                    = $this->initializeDatabase();
        $this->backupDir               = BACKUP_DIR ? BACKUP_DIR : '.';
        $this->backupFile              = 'BackupFile-'.date("mdY_His", time()).'.sql';
        $this->gzipBackupFile          = defined('GZIP_BACKUP_FILE') ? GZIP_BACKUP_FILE : true;
        $this->disableForeignKeyChecks = defined('DISABLE_FOREIGN_KEY_CHECKS') ? DISABLE_FOREIGN_KEY_CHECKS : true;
        $this->batchSize               = defined('BATCH_SIZE') ? BATCH_SIZE : 1000; 
        $this->output                  = '';
    }

    protected function initializeDatabase() {
        try {
            // --- UPDATED TO INCLUDE PORT ---
            $conn = mysqli_connect($this->host, $this->username, $this->passwd, $this->dbName, $this->port);
            if (mysqli_connect_errno()) {
                throw new Exception('ERROR connecting database: ' . mysqli_connect_error());
                die();
            }
            if (!mysqli_set_charset($conn, $this->charset)) {
                mysqli_query($conn, 'SET NAMES '.$this->charset);
            }
        } catch (Exception $e) {
            print_r($e->getMessage());
            die();
        }

        return $conn;
    }

    public function backupTables($tables = '*') {
        try {
            if($tables == '*') {
                $tables = array();
                $result = mysqli_query($this->conn, 'SHOW TABLES');
                while($row = mysqli_fetch_row($result)) {
                    $tables[] = $row[0];
                }
            } else {
                $tables = is_array($tables) ? $tables : explode(',', str_replace(' ', '', $tables));
            }

            $sql = 'CREATE DATABASE IF NOT EXISTS `'.$this->dbName.'`'.";\n\n";
            $sql .= 'USE `'.$this->dbName."`;\n\n";

            if ($this->disableForeignKeyChecks === true) {
                $sql .= "SET foreign_key_checks = 0;\n\n";
            }

            foreach($tables as $table) {
                if( in_array($table, IGNORE_TABLES) )
                    continue;
                $this->obfPrint("Backing up `".$table."` table...".str_repeat('.', 50-strlen($table)), 0, 0);

                $sql .= 'DROP TABLE IF EXISTS `'.$table.'`;';
                $row = mysqli_fetch_row(mysqli_query($this->conn, 'SHOW CREATE TABLE `'.$table.'`'));
                $sql .= "\n\n".$row[1].";\n\n";

                $row = mysqli_fetch_row(mysqli_query($this->conn, 'SELECT COUNT(*) FROM `'.$table.'`'));
                $numRows = $row[0];

                $numBatches = intval($numRows / $this->batchSize) + 1; 

                for ($b = 1; $b <= $numBatches; $b++) {
                    
                    $query = 'SELECT * FROM `' . $table . '` LIMIT ' . ($b * $this->batchSize - $this->batchSize) . ',' . $this->batchSize;
                    $result = mysqli_query($this->conn, $query);
                    $realBatchSize = mysqli_num_rows ($result); 
                    $numFields = mysqli_num_fields($result);

                    if ($realBatchSize !== 0) {
                        $sql .= 'INSERT INTO `'.$table.'` VALUES ';

                        for ($i = 0; $i < $numFields; $i++) {
                            $rowCount = 1;
                            while($row = mysqli_fetch_row($result)) {
                                $sql.='(';
                                for($j=0; $j<$numFields; $j++) {
                                    if (isset($row[$j])) {
                                        $row[$j] = addslashes($row[$j]);
                                        $row[$j] = str_replace("\n","\\n",$row[$j]);
                                        $row[$j] = str_replace("\r","\\r",$row[$j]);
                                      
                                        if ($row[$j] == 'true' or $row[$j] == 'false' or preg_match('/^-?[0-9]+$/', $row[$j]) or $row[$j] == 'NULL' or $row[$j] == 'null') {
                                            $sql .= $row[$j];
                                        } else {
                                            $sql .= '"'.$row[$j].'"' ;
                                        }
                                    } else {
                                        $sql.= 'NULL';
                                    }
    
                                    if ($j < ($numFields-1)) {
                                        $sql .= ',';
                                    }
                                }
    
                                if ($rowCount == $realBatchSize) {
                                    $rowCount = 0;
                                    $sql.= ");\n"; //close the insert statement
                                } else {
                                    $sql.= "),\n"; //close the row
                                }
    
                                $rowCount++;
                            }
                        }
    
                        $this->saveFile($sql);
                        $sql = '';
                    }
                }
 
                $sql.="\n\n";
                $this->obfPrint('OK');
            }

            if ($this->disableForeignKeyChecks === true) {
                $sql .= "SET foreign_key_checks = 1;\n";
            }

            $this->saveFile($sql);

            if ($this->gzipBackupFile) {
                $this->gzipBackupFile();
            } else {
                $this->obfPrint('Backup file succesfully saved to ' . $this->backupDir.'/'.$this->backupFile, 1, 1);
            }
            
            // --- **FIX**: INSERT INTO 'backup' TABLE AT THE END ---
            $bak = $this->backupFile;
            $sql_backup = "INSERT INTO backup (`path`) VALUES (?)";
            $stmt = mysqli_prepare($this->conn, $sql_backup);
            mysqli_stmt_bind_param($stmt, 's', $bak);
            mysqli_stmt_execute($stmt);
            // --- END FIX ---

        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }

        return true;
    }

    protected function saveFile(&$sql) {
        if (!$sql) return false;
        try {
            if (!file_exists($this->backupDir)) {
                mkdir($this->backupDir, 0777, true);
            }
            file_put_contents($this->backupDir.'/'.$this->backupFile, $sql, FILE_APPEND | LOCK_EX);
        } catch (Exception $e) {
            print_r($e->getMessage());
            return false;
        }
        return true;
    }

    protected function gzipBackupFile($level = 9) {
        // ... (this function is fine) ...
    }

    public function obfPrint ($msg = '', $lineBreaksBefore = 0, $lineBreaksAfter = 1) {
        // ... (this function is fine) ...
    }

    public function getOutput() {
        return $this->output;
    }
   
    public function getBackupFile() {
        // ... (this function is fine) ...
    }

    public function getBackupDir() {
        return $this->backupDir;
    }

    public function getChangedTables($since = '1 day') {
        // ... (this function is fine) ...
    }
}


/**
 * Instantiate Backup_Database and perform backup
 */
error_reporting(E_ALL);

if (php_sapi_name() != "cli") {
    echo '<div style="font-family: monospace;">';
}

$backupDatabase = new Backup_Database(DB_HOST, DB_USER, DB_PASSWORD, DB_NAME, DB_PORT, CHARSET);
$result = $backupDatabase->backupTables(TABLES) ? 'OK' : 'KO';
$backupDatabase->obfPrint('Backup result: ' . $result, 1);

$output = $backupDatabase->getOutput();

if (php_sapi_name() != "cli") {
    echo '</div>';
}
?>