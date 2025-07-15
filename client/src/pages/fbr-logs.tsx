import { useQuery } from "@tanstack/react-query";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Activity, RefreshCw, AlertCircle, CheckCircle } from "lucide-react";
import { type FbrLog } from "@shared/schema";

export default function FbrLogs() {
  const { data: logs, isLoading, refetch } = useQuery<FbrLog[]>({
    queryKey: ["/api/fbr-logs"],
  });

  const getStatusColor = (status: string) => {
    switch (status) {
      case "success":
        return "bg-green-100 text-green-800";
      case "error":
        return "bg-red-100 text-red-800";
      case "retry":
        return "bg-yellow-100 text-yellow-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  const getActionIcon = (action: string) => {
    switch (action) {
      case "send_invoice":
      case "auto_send_invoice":
        return <CheckCircle className="h-4 w-4" />;
      case "update_status":
        return <RefreshCw className="h-4 w-4" />;
      default:
        return <Activity className="h-4 w-4" />;
    }
  };

  if (isLoading) {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-2xl font-bold text-gray-900">FBR Logs</h1>
        </div>
        <div className="space-y-4">
          {[...Array(5)].map((_, i) => (
            <Card key={i}>
              <CardContent className="p-6">
                <div className="animate-pulse space-y-4">
                  <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                  <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                  <div className="h-4 bg-gray-200 rounded w-full"></div>
                </div>
              </CardContent>
            </Card>
          ))}
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-900">FBR Logs</h1>
        <Button
          onClick={() => refetch()}
          variant="outline"
          className="flex items-center gap-2"
        >
          <RefreshCw className="h-4 w-4" />
          Refresh
        </Button>
      </div>

      <div className="space-y-4">
        {logs?.map((log) => (
          <Card key={log.id} className="hover:shadow-md transition-shadow">
            <CardHeader className="pb-3">
              <div className="flex items-center justify-between">
                <CardTitle className="text-lg flex items-center gap-2">
                  {getActionIcon(log.action)}
                  {log.action.replace(/_/g, ' ').toUpperCase()}
                </CardTitle>
                <div className="flex items-center gap-2">
                  <Badge className={getStatusColor(log.status)}>
                    {log.status}
                  </Badge>
                  <span className="text-sm text-gray-500">
                    {new Date(log.createdAt).toLocaleString()}
                  </span>
                </div>
              </div>
            </CardHeader>
            <CardContent>
              <div className="space-y-4">
                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  <div>
                    <h4 className="font-medium text-gray-900 mb-2">Details</h4>
                    <div className="space-y-1">
                      {log.invoiceId && (
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-600">Invoice ID:</span>
                          <span className="font-medium">{log.invoiceId}</span>
                        </div>
                      )}
                      {log.storeConfigId && (
                        <div className="flex justify-between text-sm">
                          <span className="text-gray-600">Store Config ID:</span>
                          <span className="font-medium">{log.storeConfigId}</span>
                        </div>
                      )}
                      <div className="flex justify-between text-sm">
                        <span className="text-gray-600">Status:</span>
                        <span className="font-medium capitalize">{log.status}</span>
                      </div>
                    </div>
                  </div>

                  {log.errorMessage && (
                    <div>
                      <h4 className="font-medium text-red-700 mb-2 flex items-center gap-2">
                        <AlertCircle className="h-4 w-4" />
                        Error Message
                      </h4>
                      <p className="text-sm text-red-600 bg-red-50 p-2 rounded">
                        {log.errorMessage}
                      </p>
                    </div>
                  )}
                </div>

                <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
                  {log.requestData && (
                    <div>
                      <h4 className="font-medium text-gray-900 mb-2">Request Data</h4>
                      <pre className="text-xs bg-gray-50 p-2 rounded overflow-x-auto">
                        {JSON.stringify(log.requestData, null, 2)}
                      </pre>
                    </div>
                  )}

                  {log.responseData && (
                    <div>
                      <h4 className="font-medium text-gray-900 mb-2">Response Data</h4>
                      <pre className="text-xs bg-gray-50 p-2 rounded overflow-x-auto">
                        {JSON.stringify(log.responseData, null, 2)}
                      </pre>
                    </div>
                  )}
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {logs?.length === 0 && (
        <Card>
          <CardContent className="py-12 text-center">
            <Activity className="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">
              No FBR Logs Yet
            </h3>
            <p className="text-gray-600 mb-4">
              FBR API communication logs will appear here once you start creating invoices.
            </p>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
