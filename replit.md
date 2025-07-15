# FBR POS Integration System

## Overview

This is a full-stack web application for managing Pakistan's Federal Board of Revenue (FBR) Point of Sale (POS) integration. The system allows businesses to configure store settings, generate invoices, send them to FBR servers, and track the communication logs. It's built with a modern tech stack featuring React frontend, Express backend, and PostgreSQL database.

## User Preferences

Preferred communication style: Simple, everyday language.

## System Architecture

### Frontend Architecture
- **Framework**: React 18 with TypeScript
- **Routing**: Wouter (lightweight client-side routing)
- **State Management**: TanStack Query (React Query) for server state
- **UI Components**: Radix UI primitives with custom styled components
- **Styling**: Tailwind CSS with CSS variables for theming
- **Form Handling**: React Hook Form with Zod validation
- **Build Tool**: Vite for fast development and optimized builds

### Backend Architecture
- **Runtime**: Node.js with Express.js framework
- **Language**: TypeScript with ES modules
- **Database**: PostgreSQL with Drizzle ORM
- **Database Provider**: Neon serverless PostgreSQL
- **API Design**: RESTful API with JSON responses
- **Development**: Hot reload with Vite middleware in development

### Project Structure
```
├── client/          # React frontend application
├── server/          # Express backend application
├── shared/          # Shared TypeScript types and schemas
├── migrations/      # Database migration files
└── dist/           # Built application files
```

## Key Components

### Database Schema (shared/schema.ts)
- **users**: User authentication and management
- **storeConfigs**: Store configuration including NTN, STRN, and FBR settings
- **invoices**: Invoice records with items, amounts, and FBR status
- **fbrLogs**: Communication logs with FBR servers

### Storage Layer (server/storage.ts)
- **IStorage Interface**: Defines contract for data operations
- **MemStorage Class**: In-memory storage implementation for development
- **Database Operations**: CRUD operations for all entities
- **Sample Data**: Pre-populated test data for development

### API Routes (server/routes.ts)
- **Store Config Management**: CRUD operations for store configurations
- **Invoice Management**: Create, read, update invoice records
- **FBR Log Tracking**: Monitor FBR communication attempts
- **Validation**: Zod schema validation for all input data

### Frontend Pages
- **Dashboard**: Overview of system status and statistics
- **Store Config**: Manage store settings and FBR integration parameters
- **Invoices**: Create and manage invoice records
- **FBR Logs**: Monitor FBR communication history

## Data Flow

1. **Store Configuration**: Admin configures store details including NTN, STRN, and FBR connection settings
2. **Invoice Creation**: Users create invoices with line items, customer details, and payment information
3. **FBR Communication**: System sends invoice data to FBR servers and tracks responses
4. **Status Monitoring**: Real-time tracking of FBR communication status and error handling
5. **Audit Trail**: Complete logging of all FBR interactions for compliance

## External Dependencies

### Frontend Dependencies
- **UI Framework**: Radix UI for accessible component primitives
- **State Management**: TanStack Query for server state synchronization
- **Form Handling**: React Hook Form with Zod resolver
- **Styling**: Tailwind CSS with PostCSS processing
- **Icons**: Lucide React icon library

### Backend Dependencies
- **Database**: Drizzle ORM with PostgreSQL dialect
- **Validation**: Zod for runtime type checking
- **Session Management**: Connect-pg-simple for PostgreSQL sessions
- **Date Handling**: date-fns for date manipulation
- **Build Tools**: esbuild for production bundling

### Development Dependencies
- **TypeScript**: Full TypeScript support across frontend and backend
- **Vite**: Fast development server and build tool
- **Replit Integration**: Custom plugins for Replit development environment

## Deployment Strategy

### Development Mode
- **Frontend**: Vite dev server with HMR
- **Backend**: tsx for TypeScript execution with auto-restart
- **Database**: Neon serverless PostgreSQL connection
- **Environment**: Replit-optimized with custom error handling

### Production Build
- **Frontend**: Vite build with optimization and bundling
- **Backend**: esbuild compilation to single JavaScript file
- **Static Assets**: Served from Express with client-side routing support
- **Database**: PostgreSQL with connection pooling

### Environment Configuration
- **DATABASE_URL**: PostgreSQL connection string (required)
- **NODE_ENV**: Environment mode (development/production)
- **Session Management**: PostgreSQL-based session storage

The system is designed to be easily deployable on Replit with automatic environment setup and can be adapted for other hosting platforms with minimal configuration changes.