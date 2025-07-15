# FBR POS Integration - Database Troubleshooting Guide

## Problem: "Table 'tbltblfbr_store_configs' already exists"

This error indicates a table prefix duplication issue. Here are the solutions:

### Solution 1: Use the Fixed Module (Recommended)

1. **Download the Latest Version**: Use `fbr_pos_integration_v1.0.2.tar.gz` (includes all fixes)
2. **Clean Installation**: The new version automatically handles table cleanup
3. **Activate Module**: The fixed activation process will work properly

### Solution 2: Manual Database Cleanup

If you still encounter issues, run these SQL commands in your database:

```sql
-- Drop all possible variations of the tables
DROP TABLE IF EXISTS `tblfbr_store_configs`;
DROP TABLE IF EXISTS `tblfbr_invoice_logs`;
DROP TABLE IF EXISTS `tblfbr_pct_codes`;
DROP TABLE IF EXISTS `tbltblfbr_store_configs`;
DROP TABLE IF EXISTS `tbltblfbr_invoice_logs`;
DROP TABLE IF EXISTS `tbltblfbr_pct_codes`;
```

Then try activating the module again.

### Solution 3: Complete Manual Installation

If module activation continues to fail, use the included `manual_install.sql` file:

1. **Access your database** (via phpMyAdmin, command line, etc.)
2. **Run the SQL script**: Execute the `manual_install.sql` file
3. **Activate the module**: The database structure will already be in place

### Understanding the Problem

The issue occurs because:
- Perfex CRM uses `db_prefix()` which adds `tbl` to table names
- Some installations may have different prefix behaviors
- The error `tbltblfbr_store_configs` shows double prefixing

### What the Fix Does

The updated module (v1.0.2) includes:
- **Robust table cleanup**: Removes all possible table name variations
- **Improved error handling**: Better detection of existing tables
- **Manual installation option**: SQL script for direct database setup
- **Comprehensive logging**: Better error reporting during activation

### Prevention

To avoid this issue in the future:
1. Always use the latest module version
2. Ensure proper folder naming (`fbr_pos_integration`)
3. Run database cleanup before major updates
4. Test on a staging environment first

### Still Having Issues?

If problems persist after trying these solutions:

1. **Check PHP Error Logs**: Look for detailed error messages
2. **Verify Database Permissions**: Ensure the database user has CREATE/DROP privileges
3. **Test Database Connection**: Verify your database is accessible
4. **Check Module Folder**: Must be named exactly `fbr_pos_integration`

### Manual Table Structure

If you need to understand the expected table structure, refer to `manual_install.sql` which contains:
- Complete CREATE TABLE statements
- Proper indexes and constraints
- Default data insertion
- Column additions to existing tables

This ensures your database matches the expected structure exactly.