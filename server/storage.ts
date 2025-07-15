import { users, storeConfigs, invoices, fbrLogs, type User, type InsertUser, type StoreConfig, type InsertStoreConfig, type Invoice, type InsertInvoice, type FbrLog, type InsertFbrLog } from "@shared/schema";

export interface IStorage {
  // User methods
  getUser(id: number): Promise<User | undefined>;
  getUserByUsername(username: string): Promise<User | undefined>;
  createUser(user: InsertUser): Promise<User>;
  
  // Store Config methods
  getStoreConfig(id: number): Promise<StoreConfig | undefined>;
  getActiveStoreConfig(): Promise<StoreConfig | undefined>;
  getAllStoreConfigs(): Promise<StoreConfig[]>;
  createStoreConfig(config: InsertStoreConfig): Promise<StoreConfig>;
  updateStoreConfig(id: number, config: Partial<InsertStoreConfig>): Promise<StoreConfig | undefined>;
  deleteStoreConfig(id: number): Promise<boolean>;
  
  // Invoice methods
  getInvoice(id: number): Promise<Invoice | undefined>;
  getInvoiceByNumber(invoiceNumber: string): Promise<Invoice | undefined>;
  getAllInvoices(): Promise<Invoice[]>;
  getInvoicesByStoreConfig(storeConfigId: number): Promise<Invoice[]>;
  createInvoice(invoice: InsertInvoice): Promise<Invoice>;
  updateInvoice(id: number, invoice: Partial<InsertInvoice>): Promise<Invoice | undefined>;
  
  // FBR Log methods
  getFbrLog(id: number): Promise<FbrLog | undefined>;
  getFbrLogsByInvoice(invoiceId: number): Promise<FbrLog[]>;
  getAllFbrLogs(): Promise<FbrLog[]>;
  createFbrLog(log: InsertFbrLog): Promise<FbrLog>;
}

export class MemStorage implements IStorage {
  private users: Map<number, User> = new Map();
  private storeConfigs: Map<number, StoreConfig> = new Map();
  private invoices: Map<number, Invoice> = new Map();
  private fbrLogs: Map<number, FbrLog> = new Map();
  
  private currentUserId = 1;
  private currentStoreConfigId = 1;
  private currentInvoiceId = 1;
  private currentFbrLogId = 1;

  constructor() {
    // Initialize with sample data
    this.initializeData();
  }

  private initializeData() {
    // Create default store config
    const defaultConfig: StoreConfig = {
      id: this.currentStoreConfigId++,
      storeName: "Demo Store",
      storeId: "DEMO001",
      ntn: "1234567890123",
      strn: "STRN123456",
      address: "123 Main Street, Karachi, Pakistan",
      posType: "Windows POS",
      posVersion: "1.0.0",
      ipAddress: "192.168.1.100",
      isActive: true,
      createdAt: new Date(),
      updatedAt: new Date(),
    };
    this.storeConfigs.set(defaultConfig.id, defaultConfig);
  }

  // User methods
  async getUser(id: number): Promise<User | undefined> {
    return this.users.get(id);
  }

  async getUserByUsername(username: string): Promise<User | undefined> {
    return Array.from(this.users.values()).find(user => user.username === username);
  }

  async createUser(insertUser: InsertUser): Promise<User> {
    const id = this.currentUserId++;
    const user: User = { ...insertUser, id };
    this.users.set(id, user);
    return user;
  }

  // Store Config methods
  async getStoreConfig(id: number): Promise<StoreConfig | undefined> {
    return this.storeConfigs.get(id);
  }

  async getActiveStoreConfig(): Promise<StoreConfig | undefined> {
    return Array.from(this.storeConfigs.values()).find(config => config.isActive);
  }

  async getAllStoreConfigs(): Promise<StoreConfig[]> {
    return Array.from(this.storeConfigs.values());
  }

  async createStoreConfig(insertConfig: InsertStoreConfig): Promise<StoreConfig> {
    const id = this.currentStoreConfigId++;
    const config: StoreConfig = {
      ...insertConfig,
      id,
      createdAt: new Date(),
      updatedAt: new Date(),
    };
    this.storeConfigs.set(id, config);
    return config;
  }

  async updateStoreConfig(id: number, updateData: Partial<InsertStoreConfig>): Promise<StoreConfig | undefined> {
    const existing = this.storeConfigs.get(id);
    if (!existing) return undefined;
    
    const updated: StoreConfig = {
      ...existing,
      ...updateData,
      updatedAt: new Date(),
    };
    this.storeConfigs.set(id, updated);
    return updated;
  }

  async deleteStoreConfig(id: number): Promise<boolean> {
    return this.storeConfigs.delete(id);
  }

  // Invoice methods
  async getInvoice(id: number): Promise<Invoice | undefined> {
    return this.invoices.get(id);
  }

  async getInvoiceByNumber(invoiceNumber: string): Promise<Invoice | undefined> {
    return Array.from(this.invoices.values()).find(invoice => invoice.invoiceNumber === invoiceNumber);
  }

  async getAllInvoices(): Promise<Invoice[]> {
    return Array.from(this.invoices.values());
  }

  async getInvoicesByStoreConfig(storeConfigId: number): Promise<Invoice[]> {
    return Array.from(this.invoices.values()).filter(invoice => invoice.storeConfigId === storeConfigId);
  }

  async createInvoice(insertInvoice: InsertInvoice): Promise<Invoice> {
    const id = this.currentInvoiceId++;
    const now = new Date();
    const fbrInvoiceNumber = this.generateFbrInvoiceNumber();
    
    const invoice: Invoice = {
      ...insertInvoice,
      id,
      fbrInvoiceNumber,
      qrCode: this.generateQrCode(fbrInvoiceNumber),
      fbrStatus: "pending",
      createdAt: now,
      updatedAt: now,
    };
    this.invoices.set(id, invoice);
    return invoice;
  }

  async updateInvoice(id: number, updateData: Partial<InsertInvoice>): Promise<Invoice | undefined> {
    const existing = this.invoices.get(id);
    if (!existing) return undefined;
    
    const updated: Invoice = {
      ...existing,
      ...updateData,
      updatedAt: new Date(),
    };
    this.invoices.set(id, updated);
    return updated;
  }

  // FBR Log methods
  async getFbrLog(id: number): Promise<FbrLog | undefined> {
    return this.fbrLogs.get(id);
  }

  async getFbrLogsByInvoice(invoiceId: number): Promise<FbrLog[]> {
    return Array.from(this.fbrLogs.values()).filter(log => log.invoiceId === invoiceId);
  }

  async getAllFbrLogs(): Promise<FbrLog[]> {
    return Array.from(this.fbrLogs.values());
  }

  async createFbrLog(insertLog: InsertFbrLog): Promise<FbrLog> {
    const id = this.currentFbrLogId++;
    const log: FbrLog = {
      ...insertLog,
      id,
      createdAt: new Date(),
    };
    this.fbrLogs.set(id, log);
    return log;
  }

  private generateFbrInvoiceNumber(): string {
    const now = new Date();
    const dateStr = now.toISOString().slice(2, 10).replace(/-/g, '');
    const timeStr = now.toTimeString().slice(0, 8).replace(/:/g, '');
    const sequence = String(this.currentInvoiceId).padStart(4, '0');
    return `${dateStr}${timeStr}${sequence}`;
  }

  private generateQrCode(fbrInvoiceNumber: string): string {
    // Generate QR code data for FBR verification
    return `FBR:${fbrInvoiceNumber}:${Date.now()}`;
  }
}

export const storage = new MemStorage();
