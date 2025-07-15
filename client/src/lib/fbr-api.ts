import { apiRequest } from "./queryClient";

export interface FbrInvoiceData {
  invoiceId: number;
  storeId: string;
  invoiceNumber: string;
  fbrInvoiceNumber: string;
  customerName?: string;
  items: InvoiceItem[];
  totalAmount: number;
  taxAmount: number;
  paymentMode: string;
  timestamp: string;
}

export interface InvoiceItem {
  description: string;
  quantity: number;
  rate: number;
  total: number;
}

export interface FbrApiResponse {
  success: boolean;
  fbrInvoiceNumber?: string;
  status: string;
  message?: string;
  errors?: string[];
}

export class FbrApiClient {
  private baseUrl: string;

  constructor(baseUrl: string = "/api/fbr") {
    this.baseUrl = baseUrl;
  }

  async sendInvoice(invoiceData: FbrInvoiceData): Promise<FbrApiResponse> {
    try {
      const response = await apiRequest("POST", `${this.baseUrl}/send-invoice`, {
        invoiceId: invoiceData.invoiceId,
        storeId: invoiceData.storeId,
        invoiceNumber: invoiceData.invoiceNumber,
        items: invoiceData.items,
        totalAmount: invoiceData.totalAmount,
        taxAmount: invoiceData.taxAmount,
        paymentMode: invoiceData.paymentMode,
        timestamp: invoiceData.timestamp,
      });

      return await response.json();
    } catch (error) {
      console.error("FBR API Error:", error);
      return {
        success: false,
        status: "error",
        message: "Failed to communicate with FBR servers",
        errors: [error instanceof Error ? error.message : "Unknown error"],
      };
    }
  }

  async verifyInvoice(fbrInvoiceNumber: string): Promise<FbrApiResponse> {
    try {
      const response = await apiRequest("POST", `${this.baseUrl}/verify-invoice`, {
        fbrInvoiceNumber,
      });

      return await response.json();
    } catch (error) {
      console.error("FBR Verification Error:", error);
      return {
        success: false,
        status: "error",
        message: "Failed to verify invoice with FBR",
        errors: [error instanceof Error ? error.message : "Unknown error"],
      };
    }
  }

  async getInvoiceStatus(fbrInvoiceNumber: string): Promise<FbrApiResponse> {
    try {
      const response = await apiRequest("GET", `${this.baseUrl}/invoice-status/${fbrInvoiceNumber}`);

      return await response.json();
    } catch (error) {
      console.error("FBR Status Check Error:", error);
      return {
        success: false,
        status: "error",
        message: "Failed to check invoice status with FBR",
        errors: [error instanceof Error ? error.message : "Unknown error"],
      };
    }
  }

  async retryFailedInvoice(invoiceId: number): Promise<FbrApiResponse> {
    try {
      const response = await apiRequest("POST", `${this.baseUrl}/retry-invoice`, {
        invoiceId,
      });

      return await response.json();
    } catch (error) {
      console.error("FBR Retry Error:", error);
      return {
        success: false,
        status: "error",
        message: "Failed to retry invoice submission to FBR",
        errors: [error instanceof Error ? error.message : "Unknown error"],
      };
    }
  }

  async checkServerStatus(): Promise<{ online: boolean; message: string }> {
    try {
      const response = await apiRequest("GET", `${this.baseUrl}/status`);
      const data = await response.json();
      return {
        online: data.online,
        message: data.message || "Server status checked successfully",
      };
    } catch (error) {
      return {
        online: false,
        message: "Failed to connect to FBR servers",
      };
    }
  }
}

export const fbrApi = new FbrApiClient();
