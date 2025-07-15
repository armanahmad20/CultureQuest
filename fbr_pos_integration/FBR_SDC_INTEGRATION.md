# FBR SDC Integration Guide

## Overview

The FBR Sales Data Controller (SDC) is a Windows-only application that must be installed on the business premises. This module communicates with the SDC through its local HTTP API.

## Architecture

```
Linux Web Server (Perfex CRM) → Internet → Business Premises → Windows Machine → FBR SDC
```

## FBR SDC Requirements

### System Requirements
- **Windows 10/11** (64-bit)
- **4GB RAM** minimum
- **Internet connection** for FBR communication
- **Static IP** or domain name for remote access

### Installation Steps
1. Download FBR SDC from official FBR website
2. Install on Windows machine at business premises
3. Configure firewall to allow HTTP traffic on port 8080
4. Obtain FBR registration and certificates

## Network Configuration

### Option 1: Local Network Setup
If Perfex CRM is hosted on the same local network:
- SDC URL: `http://192.168.1.100:8080` (replace with actual IP)
- No port forwarding required
- Faster, more secure communication

### Option 2: Internet-Based Setup
If Perfex CRM is hosted externally:
- Configure port forwarding on router: External Port → 8080
- Use dynamic DNS or static IP
- SDC URL: `http://yourdomain.com:8080` or `http://123.456.789.012:8080`
- **Security Note**: Use VPN or IP whitelist for security

### Option 3: VPN Setup (Recommended)
- Set up VPN between web server and business premises
- Most secure option for remote communication
- SDC URL: `http://10.0.0.100:8080` (VPN IP)

## Module Configuration

### Store Configuration
1. Go to **Setup → FBR POS Integration → Store Configuration**
2. Configure these settings:
   - **Store Name**: Your business name
   - **Store ID**: Unique identifier
   - **NTN**: National Tax Number
   - **STRN**: Sales Tax Registration Number
   - **SDC URL**: URL to reach your FBR SDC
   - **SDC Username**: (if SDC requires authentication)
   - **SDC Password**: (if SDC requires authentication)

### SDC URL Examples
- Local: `http://localhost:8080`
- Local Network: `http://192.168.1.100:8080`
- Remote: `http://yourbusiness.ddns.net:8080`
- VPN: `http://10.0.0.100:8080`

## FBR SDC API Endpoints

The module uses these SDC endpoints:

### Invoice Submission
- **Endpoint**: `POST /api/submit-invoice`
- **Purpose**: Submit invoice data to FBR
- **Response**: FBR invoice number and status

### Status Check
- **Endpoint**: `GET /api/status`
- **Purpose**: Check if SDC is running
- **Response**: Online status and version

### Invoice Verification
- **Endpoint**: `POST /api/verify-invoice`
- **Purpose**: Verify invoice with FBR
- **Response**: Verification status

### Invoice Status
- **Endpoint**: `POST /api/invoice-status`
- **Purpose**: Get current invoice status
- **Response**: Processing status updates

## Data Flow

1. **Invoice Created** in Perfex CRM
2. **Module Triggered** by invoice save
3. **Data Prepared** with PCT codes and tax info
4. **HTTP Request** sent to FBR SDC
5. **SDC Processes** data and contacts FBR
6. **Response Received** with FBR invoice number
7. **Status Updated** in Perfex CRM
8. **QR Code Generated** for invoice

## Troubleshooting

### Common Issues

#### "Cannot connect to FBR SDC"
- Check if SDC is running on Windows machine
- Verify network connectivity
- Test SDC URL in browser
- Check firewall settings

#### "HTTP Error 404"
- SDC API endpoints may be different
- Update SDC to latest version
- Check SDC documentation for correct endpoints

#### "Timeout Error"
- Increase timeout settings
- Check internet connection at business premises
- Verify SDC performance

#### "Authentication Required"
- Configure SDC username/password in store settings
- Check SDC authentication settings

### Network Testing

Test connectivity from your web server:
```bash
curl -X GET http://your-sdc-url:8080/api/status
```

Expected response:
```json
{
  "status": "online",
  "version": "1.2.3",
  "connected_to_fbr": true
}
```

## Security Considerations

1. **Firewall Rules**: Only allow necessary ports
2. **IP Whitelist**: Restrict access to known IPs
3. **VPN Access**: Use VPN for remote connections
4. **SSL/TLS**: Use HTTPS if supported by SDC
5. **Authentication**: Enable SDC authentication if available

## Deployment Options

### Option A: Same Location
- Host Perfex CRM on local Windows server
- Install FBR SDC on same machine
- Use `http://localhost:8080`
- Simplest setup, no network configuration needed

### Option B: Cloud + Local SDC
- Host Perfex CRM on cloud (recommended)
- Install FBR SDC on local Windows machine
- Configure network access
- More flexible but requires network setup

### Option C: Hybrid Setup
- Use cloud hosting for Perfex CRM
- Use VPN for secure SDC communication
- Best security and flexibility

## Maintenance

### Regular Tasks
- Keep FBR SDC updated
- Monitor SDC logs
- Check network connectivity
- Backup FBR certificates
- Test communication regularly

### Monthly Checks
- Verify FBR compliance
- Check invoice status reports
- Update network security
- Review error logs

## Support

For FBR SDC issues:
- Contact FBR support
- Check FBR official documentation
- Verify SDC installation

For module issues:
- Check module logs in Perfex CRM
- Test network connectivity
- Verify configuration settings