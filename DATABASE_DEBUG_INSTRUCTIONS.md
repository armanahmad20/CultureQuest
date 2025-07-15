# Database Debug Instructions

## Package Information
- **Package Name:** `fbr_pos_integration_DEBUG_v1.1.0.tar.gz`
- **Version:** 1.1.0 (Debug Version)
- **Date:** January 15, 2025

## Issue Description
Store configuration appears to save successfully but doesn't show in the database. This debug version will help identify the root cause.

## What's Added in This Debug Version

### 1. Enhanced Logging
- **Controller Debug**: Logs all data being saved
- **Model Debug**: Logs SQL operations and results
- **Database Verification**: Checks if table exists and verifies saved data

### 2. Database Test Button
- Added "Test Database" button on Store Configuration page
- Shows table existence, record count, and current configurations
- Helps verify database connection and table structure

### 3. Better Error Handling
- Enhanced error messages with database-specific information
- Exception handling with detailed error logging
- Verification of data after insert operations

## Installation Instructions

1. **Install the debug package:**
   ```bash
   # Extract the package
   tar -xzf fbr_pos_integration_DEBUG_v1.1.0.tar.gz
   
   # Upload to your Perfex CRM modules directory
   # Activate the module
   ```

2. **Enable debug logging in Perfex CRM:**
   - Go to your Perfex CRM root directory
   - Edit `application/config/config.php`
   - Set: `$config['log_threshold'] = 4;` (to enable debug logging)

## Testing Steps

### Step 1: Test Database Connection
1. Go to FBR POS Integration > Store Configuration
2. Click the "Test Database" button
3. Note the results:
   - Does the table exist?
   - What is the current record count?
   - Are any existing configurations shown?

### Step 2: Try Adding Configuration
1. Click "Add New Store Configuration"
2. Fill in all required fields:
   - Store Name
   - Store ID
   - NTN (13 digits)
   - STRN
   - Address
3. Click "Save Configuration"
4. Note the success/error message

### Step 3: Check Debug Logs
1. Go to your Perfex CRM root directory
2. Check the log file: `application/logs/log-YYYY-MM-DD.php`
3. Look for entries starting with "FBR:" to see debug information

### Step 4: Test Database Again
1. Click "Test Database" button again
2. Compare the record count before and after
3. Check if your configuration appears in the list

## Common Issues and Solutions

### Issue 1: Table Doesn't Exist
**Symptoms:** Test Database shows "Table exists: NO"
**Solution:** 
- Deactivate and reactivate the module
- Check if database user has CREATE TABLE permissions

### Issue 2: Configuration Saves but Count Doesn't Increase
**Symptoms:** Success message shown but record count stays same
**Solution:**
- Check debug logs for database errors
- Verify database user has INSERT permissions
- Check if NTN field is causing unique constraint issues

### Issue 3: Database Connection Issues
**Symptoms:** Test Database button fails completely
**Solution:**
- Check Perfex CRM database configuration
- Verify database server is running
- Check database user permissions

## Debug Information to Collect

When reporting issues, please provide:

1. **Database Test Results:**
   - Table exists: YES/NO
   - Record count: X
   - Configurations found: X

2. **Debug Log Entries:**
   - Look for "FBR:" entries in application/logs/
   - Include any error messages
   - Include the data being saved

3. **Database Information:**
   - Database type (MySQL/MariaDB)
   - Database version
   - Table structure (if accessible)

## Manual Database Check

If you have direct database access, you can run these SQL queries:

```sql
-- Check if table exists
SHOW TABLES LIKE 'tblfbr_store_configs';

-- Check table structure
DESCRIBE tblfbr_store_configs;

-- Count records
SELECT COUNT(*) FROM tblfbr_store_configs;

-- View all configurations
SELECT * FROM tblfbr_store_configs;
```

## Next Steps

After testing with this debug version:
1. Use the "Test Database" button to verify database connection
2. Try adding a configuration and note any error messages
3. Check the debug logs for detailed information
4. Report the findings along with the debug information

This debug version will help us identify whether the issue is:
- Database connection problems
- Table creation issues
- Data validation problems
- Permission issues
- SQL query problems

The enhanced logging will show exactly what's happening during the save process.