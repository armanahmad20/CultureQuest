<?php
// Debug script to check table existence and fix naming issues
// Place this in your Perfex CRM root directory and run it via browser

defined('BASEPATH') or exit('No direct script access allowed');

// Load CodeIgniter
$CI = &get_instance();
$CI->load->dbforge();

echo "<h2>FBR POS Integration - Table Debug</h2>";

// Check what db_prefix() returns
echo "<p><strong>Database prefix:</strong> " . db_prefix() . "</p>";

// Expected table names
$expected_tables = [
    'fbr_store_configs',
    'fbr_invoice_logs',
    'fbr_pct_codes'
];

echo "<h3>Expected vs Actual Tables:</h3>";
foreach ($expected_tables as $table) {
    $prefixed_name = db_prefix() . $table;
    echo "<p>";
    echo "<strong>Expected:</strong> {$prefixed_name} - ";
    echo "<strong>Exists:</strong> " . ($CI->db->table_exists($prefixed_name) ? "YES" : "NO");
    echo "</p>";
}

// Check all tables that might contain 'fbr'
echo "<h3>All FBR-related tables in database:</h3>";
$tables = $CI->db->list_tables();
foreach ($tables as $table) {
    if (strpos($table, 'fbr') !== false) {
        echo "<p>Found: {$table}</p>";
    }
}

// Try to create the tables manually
echo "<h3>Manual Table Creation Test:</h3>";

// Drop any existing variations
$drop_variations = [
    'fbr_store_configs',
    'tblfbr_store_configs',
    'tbltblfbr_store_configs',
    'tbltbltblfbr_store_configs'
];

foreach ($drop_variations as $table) {
    if ($CI->db->table_exists($table)) {
        $CI->dbforge->drop_table($table);
        echo "<p>Dropped: {$table}</p>";
    }
}

// Create the store configs table with correct name
$CI->dbforge->add_field([
    'id' => [
        'type' => 'INT',
        'constraint' => 11,
        'unsigned' => TRUE,
        'auto_increment' => TRUE
    ],
    'store_name' => [
        'type' => 'VARCHAR',
        'constraint' => 255,
        'null' => FALSE
    ],
    'store_id' => [
        'type' => 'VARCHAR',
        'constraint' => 50,
        'null' => FALSE
    ],
    'ntn' => [
        'type' => 'VARCHAR',
        'constraint' => 15,
        'null' => FALSE
    ],
    'strn' => [
        'type' => 'VARCHAR',
        'constraint' => 15,
        'null' => FALSE
    ],
    'address' => [
        'type' => 'TEXT',
        'null' => FALSE
    ],
    'pos_type' => [
        'type' => 'VARCHAR',
        'constraint' => 100,
        'null' => FALSE
    ],
    'pos_version' => [
        'type' => 'VARCHAR',
        'constraint' => 20,
        'null' => FALSE
    ],
    'ip_address' => [
        'type' => 'VARCHAR',
        'constraint' => 45,
        'null' => FALSE
    ],
    'is_active' => [
        'type' => 'TINYINT',
        'constraint' => 1,
        'default' => 1
    ],
    'created_at' => [
        'type' => 'DATETIME',
        'null' => FALSE
    ],
    'updated_at' => [
        'type' => 'DATETIME',
        'null' => TRUE
    ]
]);

$CI->dbforge->add_key('id', TRUE);
$CI->dbforge->add_key('store_id');

$table_name = db_prefix() . 'fbr_store_configs';
if ($CI->dbforge->create_table($table_name, TRUE)) {
    echo "<p>✓ Successfully created: {$table_name}</p>";
} else {
    echo "<p>✗ Failed to create: {$table_name}</p>";
}

// Test table access
echo "<h3>Table Access Test:</h3>";
try {
    $count = $CI->db->count_all_results(db_prefix() . 'fbr_store_configs');
    echo "<p>✓ Successfully accessed table, record count: {$count}</p>";
} catch (Exception $e) {
    echo "<p>✗ Error accessing table: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p><strong>Instructions:</strong></p>";
echo "<ol>";
echo "<li>Run this script to see what tables exist</li>";
echo "<li>If tables are created successfully, try activating the module again</li>";
echo "<li>Delete this file after debugging</li>";
echo "</ol>";
?>