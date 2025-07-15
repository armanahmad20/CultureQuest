# FBR POS Integration Module - Installation Guide

## Download and Installation

1. **Download the Module**
   - Download the `fbr_pos_integration_v1.0.1.tar.gz` file (latest version with fixes)
   - Extract it to get the `fbr_pos_integration` folder

2. **Install in Perfex CRM**
   - Extract the downloaded file to get the `fbr_pos_integration` folder
   - Copy the `fbr_pos_integration` folder to your Perfex CRM `/modules/` directory
   - The final path should be: `/path/to/perfex/modules/fbr_pos_integration/`
   - **Important**: The folder name must be exactly `fbr_pos_integration` for proper recognition

3. **Activate the Module**
   - Go to Setup → Modules in your Perfex CRM admin panel
   - Find "FBR POS Integration" in the list
   - Click "Activate" to enable the module

4. **Configure Store Settings**
   - Go to FBR POS Integration → Store Configuration
   - Add your store details:
     - Store Name
     - Store ID (from FBR)
     - NTN (13-digit National Tax Number)
     - STRN (Sales Tax Registration Number)
     - Store Address
   - Set configuration as "Active"

5. **Configure PCT Codes**
   - Go to FBR POS Integration → PCT Codes
   - Review pre-loaded PCT codes
   - Add custom PCT codes if needed
   - Assign PCT codes to your products/services

6. **Test the Integration**
   - Go to FBR POS Integration → Settings
   - Test FBR server connection
   - Create a test invoice to verify functionality

## Features Included

✅ **Complete FBR Integration**
- Real-time invoice submission to FBR servers
- Automatic tax calculations (17% Pakistan tax rate)
- QR code generation for invoice verification
- Comprehensive audit logging

✅ **PCT Codes Support**
- Pre-loaded common PCT codes
- Custom PCT code management
- Tax rate configuration per code
- Easy assignment to products

✅ **Dashboard & Monitoring**
- Real-time statistics
- Revenue tracking
- FBR server status monitoring
- Recent activity logs

✅ **Compliance Features**
- NTN and STRN validation
- FBR invoice number generation
- Complete audit trail
- Error handling and retry mechanisms

## Files Included

- `fbr_pos_integration.php` - Main module file
- `controllers/Fbr_pos_integration.php` - Controller
- `models/Fbr_pos_integration_model.php` - Model
- `libraries/Fbr_api.php` - FBR API integration
- `views/` - All view files (dashboard, settings, etc.)
- `assets/` - CSS and JavaScript files
- `README.md` - Detailed documentation

## Requirements

- Perfex CRM version 2.3.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- cURL extension enabled
- Active internet connection for FBR API

## Troubleshooting

### Database Table Issues
If you encounter "Table already exists" errors during activation:
1. The module automatically handles table recreation during activation
2. If issues persist, manually drop the FBR tables from your database:
   - `DROP TABLE IF EXISTS tbl_fbr_store_configs;`
   - `DROP TABLE IF EXISTS tbl_fbr_invoice_logs;`
   - `DROP TABLE IF EXISTS tbl_fbr_pct_codes;`
3. Then try activating the module again

### Folder Name Issues
- The module folder **must** be named exactly `fbr_pos_integration`
- If you named it differently, rename the folder before activation

### Common Issues
- **Permission Error**: Ensure your web server has write permissions to the modules directory
- **Database Connection**: Verify your database credentials are correct
- **PHP Version**: Ensure PHP 7.4 or higher is installed
- **Missing Extensions**: Verify cURL extension is enabled in PHP

## Support

For technical support, refer to the included README.md file or contact your system administrator.