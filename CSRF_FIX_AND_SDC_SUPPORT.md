# FBR POS Integration - CSRF Fix and SDC Support

## Package Information
- **Package Name:** `fbr_pos_integration_CSRF_FIX_v1.0.7.tar.gz`
- **Version:** 1.0.7
- **Date:** January 15, 2025

## Issues Fixed

### 1. CSRF Token "419 Page Expired!" Error
**Problem:** Users were getting "419 Page Expired!" error when trying to save store configuration.

**Root Cause:** Form submission was using `this.submit()` which bypassed CSRF token validation.

**Solution:** 
- Converted form submission to AJAX with proper CSRF token handling
- Added `save_store_config` AJAX endpoint in controller
- Implemented proper error handling for expired sessions
- Added user-friendly error messages

### 2. Enhanced FBR SDC Support
**New Features:**
- Added FBR SDC URL configuration field
- Added optional SDC username and password fields
- Improved support for Windows-only FBR SDC program
- Added informational help text for SDC configuration

## Technical Changes

### Controller Changes (`controllers/Fbr_pos_integration.php`)
- Added `save_store_config()` method for AJAX form submission
- Added `get_store_config()` method for retrieving individual configurations
- Enhanced validation and error handling
- Added support for new SDC fields

### Model Changes (`models/Fbr_pos_integration_model.php`)
- Added `get_store_config($id)` method
- Updated table structure to include SDC fields

### View Changes (`views/store_config.php`)
- Converted form submission to AJAX
- Added FBR SDC Connection section
- Added SDC URL, username, and password fields
- Enhanced error handling and user feedback
- Added informational alerts for SDC configuration

### Database Schema Updates
The database table `tbltblfbr_store_configs` now includes:
```sql
`sdc_url` varchar(255) DEFAULT 'http://localhost:8080',
`sdc_username` varchar(100) DEFAULT NULL,
`sdc_password` varchar(100) DEFAULT NULL,
```

## Installation Instructions

### For New Installations:
1. Extract `fbr_pos_integration_CSRF_FIX_v1.0.7.tar.gz`
2. Upload to `modules/` directory in Perfex CRM
3. Go to Setup > Modules
4. Install and activate the module

### For Existing Installations:
1. **IMPORTANT:** Backup your database before updating
2. Deactivate the current module in Setup > Modules
3. Remove the old module folder
4. Extract the new package to `modules/` directory
5. Activate the module again

## Configuration Guide

### Store Configuration Setup:
1. Navigate to FBR POS Integration > Store Configuration
2. Click "Add New Store Configuration"
3. Fill in basic store information (Name, ID, NTN, STRN, Address)
4. Configure FBR SDC Connection:
   - **SDC URL:** Enter the URL to your FBR SDC installation
     - For local machine: `http://localhost:8080`
     - For network machine: `http://192.168.1.100:8080`
   - **SDC Username/Password:** Optional authentication (if required)
5. Click "Save Configuration"

### FBR SDC Deployment Options:
1. **Local Installation:** SDC on same machine as Perfex CRM
2. **Network Installation:** SDC on Windows machine accessible via LAN
3. **VPN Connection:** SDC accessible through VPN connection

## Troubleshooting

### CSRF Token Issues:
- **Problem:** Still getting 419 errors
- **Solution:** Clear browser cache and reload the page
- **Alternative:** Check if session is expired and refresh the page

### SDC Connection Issues:
- **Problem:** Cannot connect to FBR SDC
- **Solution:** 
  - Verify SDC URL is correct and accessible
  - Check Windows firewall settings
  - Ensure FBR SDC service is running on target machine
  - Test network connectivity between servers

### Form Validation Errors:
- **Problem:** Form shows validation errors
- **Solution:** Check all required fields are filled:
  - Store Name
  - Store ID
  - NTN (13 digits)
  - STRN
  - Address
  - SDC URL

## Testing the Fix

### Test CSRF Token Fix:
1. Go to Store Configuration page
2. Add or edit a store configuration
3. Fill in all required fields
4. Click "Save Configuration"
5. Should see success message without 419 error

### Test SDC Fields:
1. Create new store configuration
2. Verify SDC URL defaults to `http://localhost:8080`
3. Edit configuration and verify SDC fields populate correctly
4. Save and verify fields are stored in database

## Support Information

### Package Files:
- `fbr_pos_integration_CSRF_FIX_v1.0.7.tar.gz` - Complete fixed module
- `CSRF_FIX_AND_SDC_SUPPORT.md` - This documentation

### Previous Versions:
- `fbr_pos_integration_FIXED_v1.0.5.tar.gz` - Previous database fix
- `fbr_pos_integration_SDC_v1.0.6.tar.gz` - Previous SDC support

### Database Requirements:
- MySQL 5.7+ or MariaDB 10.2+
- PHP 7.4+ recommended
- Perfex CRM 2.9+ compatibility

## What's New in v1.0.7

✅ **Fixed:** CSRF token "419 Page Expired!" error  
✅ **Added:** Enhanced FBR SDC URL configuration  
✅ **Added:** Optional SDC authentication fields  
✅ **Improved:** AJAX form submission with better error handling  
✅ **Enhanced:** User interface with informational alerts  
✅ **Updated:** Database schema for SDC support  

## Next Steps

After successful installation and configuration:
1. Test store configuration creation and editing
2. Verify FBR SDC connection settings
3. Test invoice creation and FBR communication
4. Monitor FBR logs for successful data transmission

For technical support or questions, refer to the module documentation or contact support.