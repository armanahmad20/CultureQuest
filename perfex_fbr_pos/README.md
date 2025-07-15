# FBR POS Integration Module for Perfex CRM

## Overview

This module integrates Perfex CRM with Pakistan's Federal Board of Revenue (FBR) Point of Sale (POS) system for real-time sales reporting and tax compliance. It includes comprehensive PCT (Pakistan Customs Tariff) codes support for accurate product classification and tax calculations.

## Features

### Core Functionality
- **Real-time FBR Integration**: Automatic invoice submission to FBR servers
- **PCT Codes Management**: Complete Pakistan Customs Tariff codes support
- **Tax Compliance**: Automatic 17% tax calculations and reporting
- **QR Code Generation**: Digital invoice verification codes
- **Comprehensive Logging**: Full audit trail of FBR communications

### Dashboard Features
- Real-time statistics and revenue tracking
- FBR server status monitoring
- Recent activity logs
- Active store configuration display

### Store Configuration
- Multiple store configurations support
- NTN (National Tax Number) validation
- STRN (Sales Tax Registration Number) management
- POS system registration details

### PCT Codes Management
- Pre-loaded common PCT codes
- Custom PCT code creation and editing
- Tax rate configuration per code
- Active/inactive status management

### FBR Communication
- Automatic invoice submission
- Status tracking (pending, confirmed, failed)
- Retry mechanisms for failed submissions
- Detailed error logging and reporting

## Installation

1. **Download**: Extract the `perfex_fbr_pos` folder to your Perfex CRM modules directory
2. **Activate**: Go to Setup > Modules and activate "FBR POS Integration"
3. **Configure**: Set up your store configuration in the FBR POS Integration menu
4. **Test**: Send a test invoice to verify FBR connectivity

## Configuration

### Store Setup
1. Navigate to **FBR POS Integration > Store Configuration**
2. Click "Add New Store Configuration"
3. Fill in the required fields:
   - Store Name
   - Store ID (provided by FBR)
   - NTN (13-digit National Tax Number)
   - STRN (Sales Tax Registration Number)
   - Store Address
   - POS Type and Version
   - IP Address
4. Set the configuration as "Active"

### PCT Codes Setup
1. Navigate to **FBR POS Integration > PCT Codes**
2. Review the pre-loaded PCT codes
3. Add custom PCT codes if needed:
   - 8-digit PCT code
   - Description
   - Tax rate (%)
   - Active status

### System Settings
1. Navigate to **FBR POS Integration > Settings**
2. Configure:
   - Enable/disable FBR integration
   - Auto-send invoices to FBR
   - Default tax rate
   - FBR server URL

## Usage

### Automatic Integration
- When FBR integration is enabled, invoices are automatically sent to FBR upon creation
- Invoice status is updated in real-time (pending → confirmed/failed)
- QR codes are generated for confirmed invoices

### Manual Operations
- **Send Invoice**: Manually send specific invoices to FBR
- **Retry Failed**: Retry failed invoice submissions
- **Check Status**: Verify FBR server connectivity
- **View Logs**: Review detailed communication logs

### Product Configuration
1. Edit any product/service in Perfex CRM
2. Assign appropriate PCT code from the dropdown
3. Tax rates are automatically applied based on PCT code

## API Integration

### FBR API Endpoints
- **Submit Invoice**: `/submit-invoice`
- **Verify Invoice**: `/verify-invoice`
- **Check Status**: `/status`
- **Invoice Status**: `/invoice-status`

### Request Format
```json
{
  "invoice_id": 123,
  "store_id": "STORE001",
  "invoice_number": "INV-2024-001",
  "customer_name": "Customer Name",
  "items": [
    {
      "description": "Product Name",
      "quantity": 2,
      "rate": 100.00,
      "total": 200.00,
      "pct_code": "01111000",
      "tax_rate": 17.00
    }
  ],
  "subtotal": 200.00,
  "tax_amount": 34.00,
  "total_amount": 234.00,
  "payment_mode": "cash"
}
```

## Database Schema

### Tables Created
- `fbr_store_configs`: Store configuration details
- `fbr_invoice_logs`: FBR communication logs
- `fbr_pct_codes`: PCT codes and tax rates

### Modified Tables
- `items`: Added `pct_code` field
- `invoices`: Added `fbr_invoice_number`, `fbr_status`, `fbr_qr_code` fields

## Troubleshooting

### Common Issues

1. **FBR Server Connection Failed**
   - Check internet connectivity
   - Verify FBR server URL in settings
   - Ensure store configuration is active

2. **Invoice Submission Failed**
   - Verify NTN and STRN are correct
   - Check if all required fields are filled
   - Review FBR logs for detailed error messages

3. **PCT Code Not Found**
   - Ensure PCT codes are properly configured
   - Check if products have assigned PCT codes
   - Verify PCT codes are active

4. **Tax Calculation Issues**
   - Verify tax rates in PCT codes
   - Check if default tax rate is configured
   - Ensure tax calculations are enabled

### Log Files
- FBR communication logs are stored in the database
- Access logs via **FBR POS Integration > FBR Logs**
- Error messages provide detailed troubleshooting information

## Support

### Technical Requirements
- Perfex CRM version 2.3.0 or higher
- PHP 7.4 or higher
- MySQL 5.7 or higher
- cURL extension enabled
- Internet connectivity for FBR API calls

### Compliance
- Follows FBR POS API v1.1 specifications
- Compliant with Pakistan tax regulations
- Supports all required FBR reporting formats

### Updates
- Regular updates for FBR API changes
- New PCT codes additions
- Bug fixes and performance improvements

## License

This module is developed for Perfex CRM and follows the same licensing terms. Use in compliance with Pakistani tax regulations and FBR requirements.

## Version History

- **v1.0.0**: Initial release with core FBR integration
- **v1.0.1**: Added PCT codes support
- **v1.0.2**: Enhanced error handling and logging
- **v1.0.3**: UI improvements and dashboard enhancements

## Contact

For support and inquiries related to FBR POS integration, contact your system administrator or FBR support team.