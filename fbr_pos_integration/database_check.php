<?php
// Database Check Script - Place in your Perfex CRM root directory
// This will show us what database is actually being used

defined('BASEPATH') OR exit('No direct script access allowed');

require_once(FCPATH . 'application/config/database.php');

echo "<h2>Database Information</h2>";

// Show current database info
echo "<p><strong>Database Name:</strong> " . $db['default']['database'] . "</p>";
echo "<p><strong>Database Prefix:</strong> " . $db['default']['dbprefix'] . "</p>";
echo "<p><strong>Hostname:</strong> " . $db['default']['hostname'] . "</p>";

// Check if we can connect
$CI = &get_instance();
$current_db = $CI->db->database;
echo "<p><strong>Current Connected Database:</strong> " . $current_db . "</p>";

// List all tables in the database
echo "<h3>All Tables in Database:</h3>";
$tables = $CI->db->list_tables();
foreach ($tables as $table) {
    if (strpos($table, 'fbr') !== false) {
        echo "<p style='color: green;'><strong>FBR Table Found:</strong> " . $table . "</p>";
    } else {
        echo "<p>" . $table . "</p>";
    }
}

// Check specific FBR tables
echo "<h3>FBR Table Status:</h3>";
$fbr_tables = [
    'tblfbr_store_configs',
    'tbltblfbr_store_configs',
    'tblfbr_invoice_logs',
    'tbltblfbr_invoice_logs',
    'tblfbr_pct_codes',
    'tbltblfbr_pct_codes'
];

foreach ($fbr_tables as $table) {
    $exists = $CI->db->table_exists($table);
    echo "<p><strong>" . $table . ":</strong> " . ($exists ? "EXISTS" : "MISSING") . "</p>";
}

// Test db_prefix() function
echo "<h3>Prefix Test:</h3>";
echo "<p><strong>db_prefix():</strong> " . db_prefix() . "</p>";
echo "<p><strong>db_prefix() + 'fbr_store_configs':</strong> " . db_prefix() . 'fbr_store_configs' . "</p>";

// Try to count records in existing tables
echo "<h3>Record Count Test:</h3>";
foreach ($fbr_tables as $table) {
    if ($CI->db->table_exists($table)) {
        try {
            $count = $CI->db->count_all_results($table);
            echo "<p><strong>" . $table . ":</strong> " . $count . " records</p>";
        } catch (Exception $e) {
            echo "<p><strong>" . $table . ":</strong> ERROR - " . $e->getMessage() . "</p>";
        }
    }
}
?>