import { Switch, Route } from "wouter";
import { queryClient } from "./lib/queryClient";
import { QueryClientProvider } from "@tanstack/react-query";
import { Toaster } from "@/components/ui/toaster";
import { TooltipProvider } from "@/components/ui/tooltip";
import NotFound from "@/pages/not-found";
import Dashboard from "@/pages/dashboard";
import StoreConfig from "@/pages/store-config";
import Invoices from "@/pages/invoices";
import FbrLogs from "@/pages/fbr-logs";
import Navigation from "@/components/navigation";

function Router() {
  return (
    <div className="min-h-screen bg-gray-50">
      <Navigation />
      <main className="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <Switch>
          <Route path="/" component={Dashboard} />
          <Route path="/store-config" component={StoreConfig} />
          <Route path="/invoices" component={Invoices} />
          <Route path="/fbr-logs" component={FbrLogs} />
          <Route component={NotFound} />
        </Switch>
      </main>
    </div>
  );
}

function App() {
  return (
    <QueryClientProvider client={queryClient}>
      <TooltipProvider>
        <Toaster />
        <Router />
      </TooltipProvider>
    </QueryClientProvider>
  );
}

export default App;
