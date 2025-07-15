import type { Express } from "express";
import { createServer, type Server } from "http";
import { storage } from "./storage";
import { insertStoreConfigSchema, insertInvoiceSchema, insertFbrLogSchema } from "@shared/schema";
import { z } from "zod";

export async function registerRoutes(app: Express): Promise<Server> {
  // Store Config routes
  app.get("/api/store-configs", async (req, res) => {
    try {
      const configs = await storage.getAllStoreConfigs();
      res.json(configs);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch store configurations" });
    }
  });

  app.get("/api/store-configs/active", async (req, res) => {
    try {
      const config = await storage.getActiveStoreConfig();
      if (!config) {
        return res.status(404).json({ error: "No active store configuration found" });
      }
      res.json(config);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch active store configuration" });
    }
  });

  app.post("/api/store-configs", async (req, res) => {
    try {
      const validatedData = insertStoreConfigSchema.parse(req.body);
      const config = await storage.createStoreConfig(validatedData);
      res.json(config);
    } catch (error) {
      if (error instanceof z.ZodError) {
        res.status(400).json({ error: "Invalid data", details: error.errors });
      } else {
        res.status(500).json({ error: "Failed to create store configuration" });
      }
    }
  });

  app.put("/api/store-configs/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const validatedData = insertStoreConfigSchema.partial().parse(req.body);
      const config = await storage.updateStoreConfig(id, validatedData);
      if (!config) {
        return res.status(404).json({ error: "Store configuration not found" });
      }
      res.json(config);
    } catch (error) {
      if (error instanceof z.ZodError) {
        res.status(400).json({ error: "Invalid data", details: error.errors });
      } else {
        res.status(500).json({ error: "Failed to update store configuration" });
      }
    }
  });

  app.delete("/api/store-configs/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const success = await storage.deleteStoreConfig(id);
      if (!success) {
        return res.status(404).json({ error: "Store configuration not found" });
      }
      res.json({ success: true });
    } catch (error) {
      res.status(500).json({ error: "Failed to delete store configuration" });
    }
  });

  // Invoice routes
  app.get("/api/invoices", async (req, res) => {
    try {
      const invoices = await storage.getAllInvoices();
      res.json(invoices);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch invoices" });
    }
  });

  app.get("/api/invoices/:id", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const invoice = await storage.getInvoice(id);
      if (!invoice) {
        return res.status(404).json({ error: "Invoice not found" });
      }
      res.json(invoice);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch invoice" });
    }
  });

  app.post("/api/invoices", async (req, res) => {
    try {
      const validatedData = insertInvoiceSchema.parse(req.body);
      const invoice = await storage.createInvoice(validatedData);
      
      // Simulate FBR API call
      await simulateFbrApiCall(invoice.id);
      
      res.json(invoice);
    } catch (error) {
      if (error instanceof z.ZodError) {
        res.status(400).json({ error: "Invalid data", details: error.errors });
      } else {
        res.status(500).json({ error: "Failed to create invoice" });
      }
    }
  });

  app.put("/api/invoices/:id/fbr-status", async (req, res) => {
    try {
      const id = parseInt(req.params.id);
      const { fbrStatus } = req.body;
      
      const invoice = await storage.updateInvoice(id, { fbrStatus });
      if (!invoice) {
        return res.status(404).json({ error: "Invoice not found" });
      }
      
      // Log the status change
      await storage.createFbrLog({
        invoiceId: id,
        storeConfigId: invoice.storeConfigId,
        action: "update_status",
        requestData: { fbrStatus },
        responseData: { success: true },
        status: "success",
      });
      
      res.json(invoice);
    } catch (error) {
      res.status(500).json({ error: "Failed to update FBR status" });
    }
  });

  // FBR Log routes
  app.get("/api/fbr-logs", async (req, res) => {
    try {
      const logs = await storage.getAllFbrLogs();
      res.json(logs);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch FBR logs" });
    }
  });

  app.get("/api/fbr-logs/invoice/:invoiceId", async (req, res) => {
    try {
      const invoiceId = parseInt(req.params.invoiceId);
      const logs = await storage.getFbrLogsByInvoice(invoiceId);
      res.json(logs);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch FBR logs for invoice" });
    }
  });

  // FBR API simulation
  app.post("/api/fbr/send-invoice", async (req, res) => {
    try {
      const { invoiceId } = req.body;
      const invoice = await storage.getInvoice(invoiceId);
      
      if (!invoice) {
        return res.status(404).json({ error: "Invoice not found" });
      }

      // Simulate FBR API call
      const success = Math.random() > 0.1; // 90% success rate
      const status = success ? "confirmed" : "failed";
      
      await storage.updateInvoice(invoiceId, { fbrStatus: status });
      
      await storage.createFbrLog({
        invoiceId,
        storeConfigId: invoice.storeConfigId,
        action: "send_invoice",
        requestData: { invoiceId },
        responseData: { success, status },
        status: success ? "success" : "error",
        errorMessage: success ? undefined : "FBR server temporarily unavailable",
      });

      res.json({ success, status });
    } catch (error) {
      res.status(500).json({ error: "Failed to send invoice to FBR" });
    }
  });

  app.get("/api/dashboard/stats", async (req, res) => {
    try {
      const invoices = await storage.getAllInvoices();
      const logs = await storage.getAllFbrLogs();
      
      const stats = {
        totalInvoices: invoices.length,
        confirmedInvoices: invoices.filter(i => i.fbrStatus === "confirmed").length,
        pendingInvoices: invoices.filter(i => i.fbrStatus === "pending").length,
        failedInvoices: invoices.filter(i => i.fbrStatus === "failed").length,
        totalRevenue: invoices.reduce((sum, i) => sum + parseFloat(i.totalAmount), 0),
        recentLogs: logs.slice(-10).reverse(),
      };
      
      res.json(stats);
    } catch (error) {
      res.status(500).json({ error: "Failed to fetch dashboard stats" });
    }
  });

  const httpServer = createServer(app);
  return httpServer;
}

async function simulateFbrApiCall(invoiceId: number) {
  // Simulate delay and processing
  setTimeout(async () => {
    const success = Math.random() > 0.15; // 85% success rate
    const status = success ? "confirmed" : "failed";
    
    await storage.updateInvoice(invoiceId, { fbrStatus: status });
    
    const invoice = await storage.getInvoice(invoiceId);
    if (invoice) {
      await storage.createFbrLog({
        invoiceId,
        storeConfigId: invoice.storeConfigId,
        action: "auto_send_invoice",
        requestData: { invoiceId },
        responseData: { success, status },
        status: success ? "success" : "error",
        errorMessage: success ? undefined : "FBR server communication failed",
      });
    }
  }, 2000);
}
