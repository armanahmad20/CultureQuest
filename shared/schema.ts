import { pgTable, text, serial, integer, boolean, timestamp, decimal, jsonb } from "drizzle-orm/pg-core";
import { createInsertSchema } from "drizzle-zod";
import { z } from "zod";

export const users = pgTable("users", {
  id: serial("id").primaryKey(),
  username: text("username").notNull().unique(),
  password: text("password").notNull(),
});

export const storeConfigs = pgTable("store_configs", {
  id: serial("id").primaryKey(),
  storeName: text("store_name").notNull(),
  storeId: text("store_id").notNull().unique(),
  ntn: text("ntn").notNull(),
  strn: text("strn").notNull(),
  address: text("address").notNull(),
  posType: text("pos_type").notNull(),
  posVersion: text("pos_version").notNull(),
  ipAddress: text("ip_address").notNull(),
  isActive: boolean("is_active").default(true),
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

export const invoices = pgTable("invoices", {
  id: serial("id").primaryKey(),
  storeConfigId: integer("store_config_id").references(() => storeConfigs.id),
  invoiceNumber: text("invoice_number").notNull().unique(),
  fbrInvoiceNumber: text("fbr_invoice_number").unique(),
  customerName: text("customer_name"),
  customerPhone: text("customer_phone"),
  items: jsonb("items").notNull(), // Array of invoice items
  subtotal: decimal("subtotal", { precision: 10, scale: 2 }).notNull(),
  taxAmount: decimal("tax_amount", { precision: 10, scale: 2 }).notNull(),
  discountAmount: decimal("discount_amount", { precision: 10, scale: 2 }).default("0.00"),
  totalAmount: decimal("total_amount", { precision: 10, scale: 2 }).notNull(),
  paymentMode: text("payment_mode").notNull(), // cash, card, cheque
  qrCode: text("qr_code"),
  fbrStatus: text("fbr_status").default("pending"), // pending, sent, confirmed, failed
  createdAt: timestamp("created_at").defaultNow(),
  updatedAt: timestamp("updated_at").defaultNow(),
});

export const fbrLogs = pgTable("fbr_logs", {
  id: serial("id").primaryKey(),
  invoiceId: integer("invoice_id").references(() => invoices.id),
  storeConfigId: integer("store_config_id").references(() => storeConfigs.id),
  action: text("action").notNull(), // send_invoice, verify_invoice, get_status
  requestData: jsonb("request_data"),
  responseData: jsonb("response_data"),
  status: text("status").notNull(), // success, error, retry
  errorMessage: text("error_message"),
  createdAt: timestamp("created_at").defaultNow(),
});

export const insertStoreConfigSchema = createInsertSchema(storeConfigs).omit({
  id: true,
  createdAt: true,
  updatedAt: true,
});

export const insertInvoiceSchema = createInsertSchema(invoices).omit({
  id: true,
  fbrInvoiceNumber: true,
  qrCode: true,
  fbrStatus: true,
  createdAt: true,
  updatedAt: true,
});

export const insertFbrLogSchema = createInsertSchema(fbrLogs).omit({
  id: true,
  createdAt: true,
});

export type InsertStoreConfig = z.infer<typeof insertStoreConfigSchema>;
export type StoreConfig = typeof storeConfigs.$inferSelect;
export type InsertInvoice = z.infer<typeof insertInvoiceSchema>;
export type Invoice = typeof invoices.$inferSelect;
export type InsertFbrLog = z.infer<typeof insertFbrLogSchema>;
export type FbrLog = typeof fbrLogs.$inferSelect;
export type InsertUser = z.infer<typeof insertUserSchema>;
export type User = typeof users.$inferSelect;

export const insertUserSchema = createInsertSchema(users).pick({
  username: true,
  password: true,
});
