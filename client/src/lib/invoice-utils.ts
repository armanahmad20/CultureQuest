export interface InvoiceItem {
  description: string;
  quantity: number;
  rate: number;
  total: number;
}

export interface InvoiceCalculation {
  subtotal: number;
  discountAmount: number;
  taxAmount: number;
  totalAmount: number;
}

export class InvoiceUtils {
  static readonly TAX_RATE = 0.17; // 17% tax rate for Pakistan
  static readonly POS_FEE = 1; // Rs. 1 per invoice POS service fee

  static calculateItemTotal(quantity: number, rate: number): number {
    return quantity * rate;
  }

  static calculateSubtotal(items: InvoiceItem[]): number {
    return items.reduce((sum, item) => sum + item.total, 0);
  }

  static calculateTax(subtotal: number, discountAmount: number = 0): number {
    const taxableAmount = subtotal - discountAmount;
    return taxableAmount * this.TAX_RATE;
  }

  static calculateTotal(subtotal: number, taxAmount: number, discountAmount: number = 0): number {
    return subtotal + taxAmount - discountAmount + this.POS_FEE;
  }

  static calculateInvoice(items: InvoiceItem[], discountAmount: number = 0): InvoiceCalculation {
    const subtotal = this.calculateSubtotal(items);
    const taxAmount = this.calculateTax(subtotal, discountAmount);
    const totalAmount = this.calculateTotal(subtotal, taxAmount, discountAmount);

    return {
      subtotal,
      discountAmount,
      taxAmount,
      totalAmount,
    };
  }

  static generateInvoiceNumber(): string {
    const now = new Date();
    const dateStr = now.toISOString().slice(0, 10).replace(/-/g, '');
    const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '');
    const randomStr = Math.random().toString(36).substring(2, 6).toUpperCase();
    return `INV-${dateStr}-${timeStr}-${randomStr}`;
  }

  static generateFbrInvoiceNumber(): string {
    const now = new Date();
    const dateStr = now.toISOString().slice(2, 10).replace(/-/g, '');
    const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '');
    const sequence = Math.floor(Math.random() * 9999).toString().padStart(4, '0');
    return `${dateStr}${timeStr}${sequence}`;
  }

  static formatCurrency(amount: number): string {
    return new Intl.NumberFormat('en-PK', {
      style: 'currency',
      currency: 'PKR',
      minimumFractionDigits: 2,
      maximumFractionDigits: 2,
    }).format(amount);
  }

  static validateNTN(ntn: string): boolean {
    // NTN should be 13 digits
    const ntnPattern = /^\d{13}$/;
    return ntnPattern.test(ntn);
  }

  static validateSTRN(strn: string): boolean {
    // STRN format validation (adjust based on actual FBR format)
    const strnPattern = /^[A-Z0-9]{6,12}$/;
    return strnPattern.test(strn);
  }

  static validateStoreId(storeId: string): boolean {
    // Store ID should be alphanumeric and 6-12 characters
    const storeIdPattern = /^[A-Z0-9]{6,12}$/;
    return storeIdPattern.test(storeId);
  }

  static generateQrCodeData(fbrInvoiceNumber: string, totalAmount: number): string {
    // Generate QR code data string for FBR verification
    const timestamp = new Date().toISOString();
    return `FBR:${fbrInvoiceNumber}:${totalAmount}:${timestamp}`;
  }

  static parseQrCodeData(qrData: string): { fbrInvoiceNumber: string; totalAmount: number; timestamp: string } | null {
    try {
      const parts = qrData.split(':');
      if (parts.length !== 4 || parts[0] !== 'FBR') {
        return null;
      }

      return {
        fbrInvoiceNumber: parts[1],
        totalAmount: parseFloat(parts[2]),
        timestamp: parts[3],
      };
    } catch (error) {
      return null;
    }
  }

  static getPaymentModeDisplayName(paymentMode: string): string {
    switch (paymentMode.toLowerCase()) {
      case 'cash':
        return 'Cash Payment';
      case 'card':
        return 'Card Payment';
      case 'cheque':
        return 'Cheque Payment';
      default:
        return 'Unknown Payment Method';
    }
  }

  static getFbrStatusDisplayName(status: string): string {
    switch (status.toLowerCase()) {
      case 'pending':
        return 'Pending FBR Submission';
      case 'confirmed':
        return 'Confirmed by FBR';
      case 'failed':
        return 'Failed to Submit';
      case 'sent':
        return 'Sent to FBR';
      default:
        return 'Unknown Status';
    }
  }

  static getFbrStatusColor(status: string): string {
    switch (status.toLowerCase()) {
      case 'confirmed':
        return 'text-green-600';
      case 'pending':
        return 'text-yellow-600';
      case 'failed':
        return 'text-red-600';
      case 'sent':
        return 'text-blue-600';
      default:
        return 'text-gray-600';
    }
  }

  static isValidInvoiceItem(item: InvoiceItem): boolean {
    return (
      item.description.trim().length > 0 &&
      item.quantity > 0 &&
      item.rate >= 0 &&
      item.total >= 0
    );
  }

  static validateInvoiceItems(items: InvoiceItem[]): string[] {
    const errors: string[] = [];

    if (items.length === 0) {
      errors.push('At least one item is required');
    }

    items.forEach((item, index) => {
      if (!this.isValidInvoiceItem(item)) {
        errors.push(`Item ${index + 1}: Invalid item data`);
      }
      if (item.description.trim().length === 0) {
        errors.push(`Item ${index + 1}: Description is required`);
      }
      if (item.quantity <= 0) {
        errors.push(`Item ${index + 1}: Quantity must be greater than 0`);
      }
      if (item.rate < 0) {
        errors.push(`Item ${index + 1}: Rate cannot be negative`);
      }
      if (Math.abs(item.total - (item.quantity * item.rate)) > 0.01) {
        errors.push(`Item ${index + 1}: Total doesn't match quantity × rate`);
      }
    });

    return errors;
  }
}
