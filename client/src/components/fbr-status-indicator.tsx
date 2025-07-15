import { useState, useEffect } from "react";
import { Badge } from "@/components/ui/badge";
import { Button } from "@/components/ui/button";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Popover, PopoverContent, PopoverTrigger } from "@/components/ui/popover";
import { CheckCircle, XCircle, AlertCircle, RefreshCw, Info } from "lucide-react";

export default function FbrStatusIndicator() {
  const [status, setStatus] = useState<"online" | "offline" | "error">("online");
  const [lastCheck, setLastCheck] = useState<Date>(new Date());

  useEffect(() => {
    // Simulate FBR server status check
    const checkStatus = () => {
      const random = Math.random();
      if (random > 0.85) {
        setStatus("offline");
      } else if (random > 0.75) {
        setStatus("error");
      } else {
        setStatus("online");
      }
      setLastCheck(new Date());
    };

    checkStatus();
    const interval = setInterval(checkStatus, 30000); // Check every 30 seconds

    return () => clearInterval(interval);
  }, []);

  const getStatusColor = () => {
    switch (status) {
      case "online":
        return "bg-green-100 text-green-800";
      case "offline":
        return "bg-red-100 text-red-800";
      case "error":
        return "bg-yellow-100 text-yellow-800";
      default:
        return "bg-gray-100 text-gray-800";
    }
  };

  const getStatusIcon = () => {
    switch (status) {
      case "online":
        return <CheckCircle className="h-4 w-4" />;
      case "offline":
        return <XCircle className="h-4 w-4" />;
      case "error":
        return <AlertCircle className="h-4 w-4" />;
      default:
        return <Info className="h-4 w-4" />;
    }
  };

  const getStatusText = () => {
    switch (status) {
      case "online":
        return "FBR Online";
      case "offline":
        return "FBR Offline";
      case "error":
        return "FBR Error";
      default:
        return "FBR Status";
    }
  };

  return (
    <Popover>
      <PopoverTrigger asChild>
        <Button variant="outline" size="sm" className="flex items-center gap-2">
          {getStatusIcon()}
          <Badge className={getStatusColor()}>
            {getStatusText()}
          </Badge>
        </Button>
      </PopoverTrigger>
      <PopoverContent className="w-80">
        <Card>
          <CardHeader className="pb-3">
            <CardTitle className="text-lg flex items-center gap-2">
              {getStatusIcon()}
              FBR Server Status
            </CardTitle>
          </CardHeader>
          <CardContent>
            <div className="space-y-3">
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-600">Current Status:</span>
                <Badge className={getStatusColor()}>
                  {getStatusText()}
                </Badge>
              </div>
              <div className="flex justify-between items-center">
                <span className="text-sm text-gray-600">Last Checked:</span>
                <span className="text-sm font-medium">
                  {lastCheck.toLocaleTimeString()}
                </span>
              </div>
              <div className="pt-2 border-t">
                <div className="text-sm text-gray-600">
                  {status === "online" && (
                    <p className="text-green-700">
                      ✓ FBR servers are responding normally. Invoices will be transmitted in real-time.
                    </p>
                  )}
                  {status === "offline" && (
                    <p className="text-red-700">
                      ✗ FBR servers are currently unavailable. Invoices will be queued and sent automatically when service resumes.
                    </p>
                  )}
                  {status === "error" && (
                    <p className="text-yellow-700">
                      ⚠ FBR servers are experiencing issues. Some invoices may be delayed.
                    </p>
                  )}
                </div>
              </div>
              <Button
                variant="outline"
                size="sm"
                onClick={() => setLastCheck(new Date())}
                className="w-full flex items-center gap-2"
              >
                <RefreshCw className="h-4 w-4" />
                Check Status
              </Button>
            </div>
          </CardContent>
        </Card>
      </PopoverContent>
    </Popover>
  );
}
