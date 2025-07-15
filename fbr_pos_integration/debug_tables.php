<?php
// Debug script to check what tables exist and what the system is looking for
// Upload this to your Perfex CRM root and run it in browser

echo "<h2>FBR Database Debug Information</h2>";

// Database connection
$config = include(APPPATH . 'config/database.php');
$db = $config['default'];

echo "<p><strong>Database:</strong> " . $db['database'] . "</p>";
echo "<p><strong>Prefix:</strong> " . $db['dbprefix'] . "</p>";

// Connect to database
$mysqli = new mysqli($db['hostname'], $db['username'], $db['password'], $db['database']);

if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

echo "<h3>All Tables in Database:</h3>";
$result = $mysqli->query("SHOW TABLES");
while ($row = $result->fetch_array()) {
    $table = $row[0];
    if (strpos($table, 'fbr') !== false) {
        echo "<p style='color: green;'><strong>FBR Table:</strong> " . $table . "</p>";
    } else {
        echo "<p>" . $table . "</p>";
    }
}

echo "<h3>Create Missing Tables SQL:</h3>";
echo "<textarea style='width: 100%; height: 300px; font-family: monospace;'>";
echo "-- Run this SQL in your database\n";
echo "CREATE TABLE IF NOT EXISTS tblfbr_pct_codes (\n";
echo "  id int(11) unsigned NOT NULL AUTO_INCREMENT,\n";
echo "  pct_code varchar(20) NOT NULL,\n";
echo "  description text NOT NULL,\n";
echo "  tax_rate decimal(5,2) DEFAULT 17.00,\n";
echo "  is_active tinyint(1) DEFAULT 1,\n";
echo "  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
echo "  PRIMARY KEY (id)\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

echo "CREATE TABLE IF NOT EXISTS tbltblfbr_pct_codes (\n";
echo "  id int(11) unsigned NOT NULL AUTO_INCREMENT,\n";
echo "  pct_code varchar(20) NOT NULL,\n";
echo "  description text NOT NULL,\n";
echo "  tax_rate decimal(5,2) DEFAULT 17.00,\n";
echo "  is_active tinyint(1) DEFAULT 1,\n";
echo "  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
echo "  PRIMARY KEY (id)\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

echo "CREATE TABLE IF NOT EXISTS tblfbr_store_configs (\n";
echo "  id int(11) unsigned NOT NULL AUTO_INCREMENT,\n";
echo "  store_name varchar(255) NOT NULL,\n";
echo "  store_id varchar(50) NOT NULL,\n";
echo "  ntn varchar(15) NOT NULL,\n";
echo "  strn varchar(15) NOT NULL,\n";
echo "  address text NOT NULL,\n";
echo "  pos_type varchar(100) NOT NULL,\n";
echo "  pos_version varchar(20) NOT NULL,\n";
echo "  ip_address varchar(45) NOT NULL,\n";
echo "  is_active tinyint(1) DEFAULT 1,\n";
echo "  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
echo "  updated_at datetime DEFAULT NULL,\n";
echo "  PRIMARY KEY (id)\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

echo "CREATE TABLE IF NOT EXISTS tbltblfbr_store_configs (\n";
echo "  id int(11) unsigned NOT NULL AUTO_INCREMENT,\n";
echo "  store_name varchar(255) NOT NULL,\n";
echo "  store_id varchar(50) NOT NULL,\n";
echo "  ntn varchar(15) NOT NULL,\n";
echo "  strn varchar(15) NOT NULL,\n";
echo "  address text NOT NULL,\n";
echo "  pos_type varchar(100) NOT NULL,\n";
echo "  pos_version varchar(20) NOT NULL,\n";
echo "  ip_address varchar(45) NOT NULL,\n";
echo "  is_active tinyint(1) DEFAULT 1,\n";
echo "  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
echo "  updated_at datetime DEFAULT NULL,\n";
echo "  PRIMARY KEY (id)\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

echo "CREATE TABLE IF NOT EXISTS tblfbr_invoice_logs (\n";
echo "  id int(11) unsigned NOT NULL AUTO_INCREMENT,\n";
echo "  invoice_id int(11) NOT NULL,\n";
echo "  store_config_id int(11) NOT NULL,\n";
echo "  fbr_invoice_number varchar(100) DEFAULT NULL,\n";
echo "  action varchar(50) NOT NULL,\n";
echo "  request_data text DEFAULT NULL,\n";
echo "  response_data text DEFAULT NULL,\n";
echo "  status varchar(20) NOT NULL,\n";
echo "  error_message text DEFAULT NULL,\n";
echo "  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
echo "  PRIMARY KEY (id)\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

echo "CREATE TABLE IF NOT EXISTS tbltblfbr_invoice_logs (\n";
echo "  id int(11) unsigned NOT NULL AUTO_INCREMENT,\n";
echo "  invoice_id int(11) NOT NULL,\n";
echo "  store_config_id int(11) NOT NULL,\n";
echo "  fbr_invoice_number varchar(100) DEFAULT NULL,\n";
echo "  action varchar(50) NOT NULL,\n";
echo "  request_data text DEFAULT NULL,\n";
echo "  response_data text DEFAULT NULL,\n";
echo "  status varchar(20) NOT NULL,\n";
echo "  error_message text DEFAULT NULL,\n";
echo "  created_at datetime NOT NULL DEFAULT CURRENT_TIMESTAMP,\n";
echo "  PRIMARY KEY (id)\n";
echo ") ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;\n\n";

echo "-- Insert sample data\n";
echo "INSERT IGNORE INTO tblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES\n";
echo "('01111000', 'Food and Beverages', 17.00, 1, NOW()),\n";
echo "('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),\n";
echo "('03111000', 'Electronics and Appliances', 17.00, 1, NOW());\n\n";

echo "INSERT IGNORE INTO tbltblfbr_pct_codes (pct_code, description, tax_rate, is_active, created_at) VALUES\n";
echo "('01111000', 'Food and Beverages', 17.00, 1, NOW()),\n";
echo "('02111000', 'Clothing and Textiles', 17.00, 1, NOW()),\n";
echo "('03111000', 'Electronics and Appliances', 17.00, 1, NOW());\n\n";

echo "INSERT IGNORE INTO tblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES\n";
echo "('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());\n\n";

echo "INSERT IGNORE INTO tbltblfbr_store_configs (store_name, store_id, ntn, strn, address, pos_type, pos_version, ip_address, is_active, created_at) VALUES\n";
echo "('Default Store', 'STORE001', '0000000000000', 'STRN000000', 'Default Address, Pakistan', 'Perfex CRM', '1.0.0', '127.0.0.1', 1, NOW());\n";

echo "</textarea>";

$mysqli->close();
?>