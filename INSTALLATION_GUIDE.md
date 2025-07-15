# FBR POS Integration Module - Installation Guide

## Download and Installation

1. **Download the Module**
   - Download the `perfex_fbr_pos_v1.0.0.tar.gz` file
   - Extract it to get the `perfex_fbr_pos` folder

2. **Install in Perfex CRM**
   - Copy the `perfex_fbr_pos` folder to your Perfex CRM `/modules/` directory
   - The final path should be: `/path/to/perfex/modules/perfex_fbr_pos/`

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

## Support

For technical support, refer to the included README.md file or contact your system administrator.