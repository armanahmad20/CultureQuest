# How to Check PHP Error Log

## Method 1: XAMPP (Most Common for Local Development)

### Windows XAMPP:
```
C:\xampp\php\logs\php_error_log
```

### Alternative XAMPP locations:
```
C:\xampp\apache\logs\error.log
C:\xampp\logs\php_error_log
```

## Method 2: Check PHP Configuration

Create a simple PHP file to find your error log location:

1. Create a file called `phpinfo.php` in your web root:
```php
<?php
phpinfo();
?>
```

2. Visit `http://your-domain/phpinfo.php` in browser
3. Look for "error_log" setting - this shows the log file path
4. **Delete the file after checking** (security risk)

## Method 3: Command Line Check

If you have command line access:
```bash
php -i | grep error_log
```

## Method 4: Common Server Locations

### Linux/Unix servers:
```
/var/log/apache2/error.log
/var/log/nginx/error.log
/var/log/php_errors.log
/var/log/httpd/error_log
```

### cPanel hosting:
```
public_html/error_logs/
/home/username/public_html/error_logs/
```

### Windows IIS:
```
C:\inetpub\logs\LogFiles\
```

## Method 5: Create a Test Script

Create this PHP file to check and test error logging:

```php
<?php
// check_error_log.php
echo "Current error log location: " . ini_get('error_log') . "<br>";
echo "Error reporting level: " . error_reporting() . "<br>";
echo "Log errors enabled: " . (ini_get('log_errors') ? 'YES' : 'NO') . "<br>";

// Test error logging
error_log("TEST: FBR DEBUG message - " . date('Y-m-d H:i:s'));
echo "Test error message sent to log.";
?>
```

## Method 6: For FBR Module Specific

Since you're using XAMPP, most likely check these locations:

1. **PHP Error Log:**
   - `C:\xampp\php\logs\php_error_log`

2. **Apache Error Log:**
   - `C:\xampp\apache\logs\error.log`

3. **FBR Module will write to both:**
   - PHP error log (via `error_log()`)
   - Perfex CRM log (via `log_message()`)

## Quick Test Steps:

1. **Install the debug package** (`fbr_pos_integration_ENHANCED_DEBUG_v1.1.1.tar.gz`)

2. **Try adding a store configuration** - this will trigger debug messages

3. **Check these files in order:**
   - `C:\xampp\php\logs\php_error_log`
   - `C:\xampp\apache\logs\error.log`
   - Your Perfex CRM `application/logs/log-YYYY-MM-DD.php`

4. **Look for lines containing:** "FBR DEBUG:"

## What to Look For:

When you save a store configuration, you should see entries like:
```
FBR DEBUG: Attempting to save config data: {"store_name":"Test Store",...}
FBR DEBUG: Table tblfbr_store_configs exists: true/false
FBR DEBUG: Create result: SUCCESS/FAILED
FBR DEBUG: Insert successful, ID: 123
FBR DEBUG: Total configs after save: 1
```

## If You Can't Find the Log:

1. Use the "Test Database" button - it will show environment and debug settings
2. The debug messages will appear in **both** PHP error log and Perfex logs
3. If still having trouble, create the test script above to locate your error log

## Alternative: Live Debug Display

If you can't access logs easily, I can modify the module to display debug information directly in the browser for testing purposes.