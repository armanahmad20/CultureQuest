import { useQuery, useMutation } from "@tanstack/react-query";
import { useForm } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Switch } from "@/components/ui/switch";
import { useToast } from "@/hooks/use-toast";
import { queryClient } from "@/lib/queryClient";
import { apiRequest } from "@/lib/queryClient";
import { insertStoreConfigSchema, type StoreConfig, type InsertStoreConfig } from "@shared/schema";
import { Settings, Save, Plus } from "lucide-react";
import { useState } from "react";

export default function StoreConfig() {
  const { toast } = useToast();
  const [isCreating, setIsCreating] = useState(false);

  const { data: configs, isLoading } = useQuery<StoreConfig[]>({
    queryKey: ["/api/store-configs"],
  });

  const { data: activeConfig } = useQuery<StoreConfig>({
    queryKey: ["/api/store-configs/active"],
  });

  const form = useForm<InsertStoreConfig>({
    resolver: zodResolver(insertStoreConfigSchema),
    defaultValues: {
      storeName: "",
      storeId: "",
      ntn: "",
      strn: "",
      address: "",
      posType: "Windows POS",
      posVersion: "1.0.0",
      ipAddress: "",
      isActive: true,
    },
  });

  const createMutation = useMutation({
    mutationFn: async (data: InsertStoreConfig) => {
      const response = await apiRequest("POST", "/api/store-configs", data);
      return response.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/store-configs"] });
      queryClient.invalidateQueries({ queryKey: ["/api/store-configs/active"] });
      form.reset();
      setIsCreating(false);
      toast({
        title: "Success",
        description: "Store configuration created successfully",
      });
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to create store configuration",
        variant: "destructive",
      });
    },
  });

  const updateMutation = useMutation({
    mutationFn: async ({ id, data }: { id: number; data: Partial<InsertStoreConfig> }) => {
      const response = await apiRequest("PUT", `/api/store-configs/${id}`, data);
      return response.json();
    },
    onSuccess: () => {
      queryClient.invalidateQueries({ queryKey: ["/api/store-configs"] });
      queryClient.invalidateQueries({ queryKey: ["/api/store-configs/active"] });
      toast({
        title: "Success",
        description: "Store configuration updated successfully",
      });
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to update store configuration",
        variant: "destructive",
      });
    },
  });

  const onSubmit = (data: InsertStoreConfig) => {
    createMutation.mutate(data);
  };

  const toggleActive = (config: StoreConfig) => {
    updateMutation.mutate({
      id: config.id,
      data: { isActive: !config.isActive },
    });
  };

  if (isLoading) {
    return (
      <div className="space-y-6">
        <div className="flex justify-between items-center">
          <h1 className="text-2xl font-bold text-gray-900">Store Configuration</h1>
        </div>
        <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
          <Card>
            <CardContent className="p-6">
              <div className="animate-pulse space-y-4">
                <div className="h-4 bg-gray-200 rounded w-3/4"></div>
                <div className="h-10 bg-gray-200 rounded"></div>
                <div className="h-4 bg-gray-200 rounded w-1/2"></div>
                <div className="h-10 bg-gray-200 rounded"></div>
              </div>
            </CardContent>
          </Card>
        </div>
      </div>
    );
  }

  return (
    <div className="space-y-6">
      <div className="flex justify-between items-center">
        <h1 className="text-2xl font-bold text-gray-900">Store Configuration</h1>
        <Button
          onClick={() => setIsCreating(true)}
          className="flex items-center gap-2"
        >
          <Plus className="h-4 w-4" />
          Add Store Config
        </Button>
      </div>

      <div className="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {/* Create New Store Config */}
        {isCreating && (
          <Card>
            <CardHeader>
              <CardTitle className="flex items-center gap-2">
                <Settings className="h-5 w-5" />
                New Store Configuration
              </CardTitle>
            </CardHeader>
            <CardContent>
              <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-4">
                <div>
                  <Label htmlFor="storeName">Store Name</Label>
                  <Input
                    id="storeName"
                    {...form.register("storeName")}
                    placeholder="Enter store name"
                  />
                  {form.formState.errors.storeName && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.storeName.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="storeId">Store ID</Label>
                  <Input
                    id="storeId"
                    {...form.register("storeId")}
                    placeholder="Enter unique store ID"
                  />
                  {form.formState.errors.storeId && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.storeId.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="ntn">NTN (National Tax Number)</Label>
                  <Input
                    id="ntn"
                    {...form.register("ntn")}
                    placeholder="Enter NTN"
                  />
                  {form.formState.errors.ntn && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.ntn.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="strn">STRN (Sales Tax Registration Number)</Label>
                  <Input
                    id="strn"
                    {...form.register("strn")}
                    placeholder="Enter STRN"
                  />
                  {form.formState.errors.strn && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.strn.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="address">Address</Label>
                  <Input
                    id="address"
                    {...form.register("address")}
                    placeholder="Enter complete address"
                  />
                  {form.formState.errors.address && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.address.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="posType">POS Type</Label>
                  <Input
                    id="posType"
                    {...form.register("posType")}
                    placeholder="Enter POS type"
                  />
                  {form.formState.errors.posType && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.posType.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="posVersion">POS Version</Label>
                  <Input
                    id="posVersion"
                    {...form.register("posVersion")}
                    placeholder="Enter POS version"
                  />
                  {form.formState.errors.posVersion && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.posVersion.message}
                    </p>
                  )}
                </div>

                <div>
                  <Label htmlFor="ipAddress">IP Address</Label>
                  <Input
                    id="ipAddress"
                    {...form.register("ipAddress")}
                    placeholder="Enter IP address"
                  />
                  {form.formState.errors.ipAddress && (
                    <p className="text-sm text-red-600 mt-1">
                      {form.formState.errors.ipAddress.message}
                    </p>
                  )}
                </div>

                <div className="flex items-center space-x-2">
                  <Switch
                    id="isActive"
                    checked={form.watch("isActive")}
                    onCheckedChange={(checked) => form.setValue("isActive", checked)}
                  />
                  <Label htmlFor="isActive">Active</Label>
                </div>

                <div className="flex gap-2">
                  <Button
                    type="submit"
                    disabled={createMutation.isPending}
                    className="flex items-center gap-2"
                  >
                    <Save className="h-4 w-4" />
                    {createMutation.isPending ? "Creating..." : "Create"}
                  </Button>
                  <Button
                    type="button"
                    variant="outline"
                    onClick={() => setIsCreating(false)}
                  >
                    Cancel
                  </Button>
                </div>
              </form>
            </CardContent>
          </Card>
        )}

        {/* Existing Store Configs */}
        {configs?.map((config) => (
          <Card key={config.id} className={config.isActive ? "border-green-500" : ""}>
            <CardHeader>
              <CardTitle className="flex items-center justify-between">
                <span className="flex items-center gap-2">
                  <Settings className="h-5 w-5" />
                  {config.storeName}
                </span>
                <div className="flex items-center gap-2">
                  <Switch
                    checked={config.isActive}
                    onCheckedChange={() => toggleActive(config)}
                    disabled={updateMutation.isPending}
                  />
                  <span className="text-sm text-gray-500">
                    {config.isActive ? "Active" : "Inactive"}
                  </span>
                </div>
              </CardTitle>
            </CardHeader>
            <CardContent>
              <div className="space-y-3">
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label className="text-sm font-medium">Store ID</Label>
                    <p className="text-sm text-gray-600">{config.storeId}</p>
                  </div>
                  <div>
                    <Label className="text-sm font-medium">NTN</Label>
                    <p className="text-sm text-gray-600">{config.ntn}</p>
                  </div>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label className="text-sm font-medium">STRN</Label>
                    <p className="text-sm text-gray-600">{config.strn}</p>
                  </div>
                  <div>
                    <Label className="text-sm font-medium">POS Type</Label>
                    <p className="text-sm text-gray-600">{config.posType}</p>
                  </div>
                </div>
                <div>
                  <Label className="text-sm font-medium">Address</Label>
                  <p className="text-sm text-gray-600">{config.address}</p>
                </div>
                <div className="grid grid-cols-2 gap-4">
                  <div>
                    <Label className="text-sm font-medium">POS Version</Label>
                    <p className="text-sm text-gray-600">{config.posVersion}</p>
                  </div>
                  <div>
                    <Label className="text-sm font-medium">IP Address</Label>
                    <p className="text-sm text-gray-600">{config.ipAddress}</p>
                  </div>
                </div>
                <div className="text-xs text-gray-500">
                  Created: {new Date(config.createdAt).toLocaleString()}
                </div>
              </div>
            </CardContent>
          </Card>
        ))}
      </div>

      {configs?.length === 0 && !isCreating && (
        <Card>
          <CardContent className="py-12 text-center">
            <Settings className="h-12 w-12 text-gray-400 mx-auto mb-4" />
            <h3 className="text-lg font-medium text-gray-900 mb-2">
              No Store Configurations
            </h3>
            <p className="text-gray-600 mb-4">
              Create your first store configuration to start using FBR POS integration.
            </p>
            <Button
              onClick={() => setIsCreating(true)}
              className="flex items-center gap-2"
            >
              <Plus className="h-4 w-4" />
              Add Store Config
            </Button>
          </CardContent>
        </Card>
      )}
    </div>
  );
}
