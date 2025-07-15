import { useState } from "react";
import { useForm, useFieldArray } from "react-hook-form";
import { zodResolver } from "@hookform/resolvers/zod";
import { useMutation, useQuery } from "@tanstack/react-query";
import { Button } from "@/components/ui/button";
import { Input } from "@/components/ui/input";
import { Label } from "@/components/ui/label";
import { Select, SelectContent, SelectItem, SelectTrigger, SelectValue } from "@/components/ui/select";
import { Card, CardContent, CardHeader, CardTitle } from "@/components/ui/card";
import { useToast } from "@/hooks/use-toast";
import { queryClient } from "@/lib/queryClient";
import { apiRequest } from "@/lib/queryClient";
import { insertInvoiceSchema, type InsertInvoice, type StoreConfig } from "@shared/schema";
import { z } from "zod";
import { Plus, Trash2, Calculator } from "lucide-react";

const invoiceFormSchema = insertInvoiceSchema.extend({
  items: z.array(
    z.object({
      description: z.string().min(1, "Description is required"),
      quantity: z.number().min(1, "Quantity must be at least 1"),
      rate: z.number().min(0, "Rate must be positive"),
      total: z.number().min(0, "Total must be positive"),
    })
  ).min(1, "At least one item is required"),
});

type InvoiceFormData = z.infer<typeof invoiceFormSchema>;

interface InvoiceGeneratorProps {
  onSuccess: () => void;
}

export default function InvoiceGenerator({ onSuccess }: InvoiceGeneratorProps) {
  const { toast } = useToast();
  const [subtotal, setSubtotal] = useState(0);
  const [taxAmount, setTaxAmount] = useState(0);
  const [totalAmount, setTotalAmount] = useState(0);

  const { data: activeStore } = useQuery<StoreConfig>({
    queryKey: ["/api/store-configs/active"],
  });

  const form = useForm<InvoiceFormData>({
    resolver: zodResolver(invoiceFormSchema),
    defaultValues: {
      storeConfigId: activeStore?.id || 1,
      invoiceNumber: `INV-${Date.now()}`,
      customerName: "",
      customerPhone: "",
      items: [
        {
          description: "",
          quantity: 1,
          rate: 0,
          total: 0,
        },
      ],
      subtotal: "0.00",
      taxAmount: "0.00",
      discountAmount: "0.00",
      totalAmount: "0.00",
      paymentMode: "cash",
    },
  });

  const { fields, append, remove } = useFieldArray({
    control: form.control,
    name: "items",
  });

  const createMutation = useMutation({
    mutationFn: async (data: InsertInvoice) => {
      const response = await apiRequest("POST", "/api/invoices", data);
      return response.json();
    },
    onSuccess: (data) => {
      queryClient.invalidateQueries({ queryKey: ["/api/invoices"] });
      queryClient.invalidateQueries({ queryKey: ["/api/dashboard/stats"] });
      toast({
        title: "Success",
        description: `Invoice ${data.invoiceNumber} created successfully`,
      });
      onSuccess();
    },
    onError: (error) => {
      toast({
        title: "Error",
        description: "Failed to create invoice",
        variant: "destructive",
      });
    },
  });

  const calculateTotals = () => {
    const items = form.getValues("items");
    const newSubtotal = items.reduce((sum, item) => sum + item.total, 0);
    const discountAmount = parseFloat(form.getValues("discountAmount")) || 0;
    const newTaxAmount = (newSubtotal - discountAmount) * 0.17; // 17% tax rate
    const newTotalAmount = newSubtotal - discountAmount + newTaxAmount;

    setSubtotal(newSubtotal);
    setTaxAmount(newTaxAmount);
    setTotalAmount(newTotalAmount);

    form.setValue("subtotal", newSubtotal.toFixed(2));
    form.setValue("taxAmount", newTaxAmount.toFixed(2));
    form.setValue("totalAmount", newTotalAmount.toFixed(2));
  };

  const updateItemTotal = (index: number) => {
    const items = form.getValues("items");
    const quantity = items[index].quantity;
    const rate = items[index].rate;
    const total = quantity * rate;
    
    form.setValue(`items.${index}.total`, total);
    calculateTotals();
  };

  const addItem = () => {
    append({
      description: "",
      quantity: 1,
      rate: 0,
      total: 0,
    });
  };

  const removeItem = (index: number) => {
    remove(index);
    calculateTotals();
  };

  const onSubmit = (data: InvoiceFormData) => {
    const invoiceData: InsertInvoice = {
      ...data,
      storeConfigId: activeStore?.id || 1,
      items: data.items,
    };
    createMutation.mutate(invoiceData);
  };

  if (!activeStore) {
    return (
      <Card>
        <CardContent className="py-8 text-center">
          <p className="text-gray-500">
            Please configure an active store before creating invoices.
          </p>
        </CardContent>
      </Card>
    );
  }

  return (
    <form onSubmit={form.handleSubmit(onSubmit)} className="space-y-6">
      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <Label htmlFor="invoiceNumber">Invoice Number</Label>
          <Input
            id="invoiceNumber"
            {...form.register("invoiceNumber")}
            placeholder="Auto-generated"
            readOnly
          />
        </div>
        <div>
          <Label htmlFor="paymentMode">Payment Mode</Label>
          <Select
            value={form.watch("paymentMode")}
            onValueChange={(value) => form.setValue("paymentMode", value as "cash" | "card" | "cheque")}
          >
            <SelectTrigger>
              <SelectValue placeholder="Select payment mode" />
            </SelectTrigger>
            <SelectContent>
              <SelectItem value="cash">Cash</SelectItem>
              <SelectItem value="card">Card</SelectItem>
              <SelectItem value="cheque">Cheque</SelectItem>
            </SelectContent>
          </Select>
        </div>
      </div>

      <div className="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div>
          <Label htmlFor="customerName">Customer Name (Optional)</Label>
          <Input
            id="customerName"
            {...form.register("customerName")}
            placeholder="Enter customer name"
          />
        </div>
        <div>
          <Label htmlFor="customerPhone">Customer Phone (Optional)</Label>
          <Input
            id="customerPhone"
            {...form.register("customerPhone")}
            placeholder="Enter customer phone"
          />
        </div>
      </div>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center justify-between">
            <span>Invoice Items</span>
            <Button
              type="button"
              variant="outline"
              size="sm"
              onClick={addItem}
              className="flex items-center gap-2"
            >
              <Plus className="h-4 w-4" />
              Add Item
            </Button>
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            {fields.map((field, index) => (
              <div key={field.id} className="grid grid-cols-1 md:grid-cols-5 gap-4 p-4 border rounded-lg">
                <div className="md:col-span-2">
                  <Label htmlFor={`items.${index}.description`}>Description</Label>
                  <Input
                    id={`items.${index}.description`}
                    {...form.register(`items.${index}.description`)}
                    placeholder="Item description"
                  />
                </div>
                <div>
                  <Label htmlFor={`items.${index}.quantity`}>Quantity</Label>
                  <Input
                    id={`items.${index}.quantity`}
                    type="number"
                    {...form.register(`items.${index}.quantity`, { valueAsNumber: true })}
                    min="1"
                    onChange={() => updateItemTotal(index)}
                  />
                </div>
                <div>
                  <Label htmlFor={`items.${index}.rate`}>Rate</Label>
                  <Input
                    id={`items.${index}.rate`}
                    type="number"
                    step="0.01"
                    {...form.register(`items.${index}.rate`, { valueAsNumber: true })}
                    min="0"
                    onChange={() => updateItemTotal(index)}
                  />
                </div>
                <div className="flex items-end gap-2">
                  <div className="flex-1">
                    <Label htmlFor={`items.${index}.total`}>Total</Label>
                    <Input
                      id={`items.${index}.total`}
                      type="number"
                      step="0.01"
                      {...form.register(`items.${index}.total`, { valueAsNumber: true })}
                      readOnly
                    />
                  </div>
                  {fields.length > 1 && (
                    <Button
                      type="button"
                      variant="outline"
                      size="sm"
                      onClick={() => removeItem(index)}
                    >
                      <Trash2 className="h-4 w-4" />
                    </Button>
                  )}
                </div>
              </div>
            ))}
          </div>
        </CardContent>
      </Card>

      <Card>
        <CardHeader>
          <CardTitle className="flex items-center gap-2">
            <Calculator className="h-5 w-5" />
            Invoice Summary
          </CardTitle>
        </CardHeader>
        <CardContent>
          <div className="space-y-4">
            <div className="grid grid-cols-1 md:grid-cols-3 gap-4">
              <div>
                <Label htmlFor="subtotal">Subtotal</Label>
                <Input
                  id="subtotal"
                  type="number"
                  step="0.01"
                  value={subtotal.toFixed(2)}
                  readOnly
                />
              </div>
              <div>
                <Label htmlFor="discountAmount">Discount Amount</Label>
                <Input
                  id="discountAmount"
                  type="number"
                  step="0.01"
                  {...form.register("discountAmount", { valueAsNumber: true })}
                  min="0"
                  onChange={calculateTotals}
                />
              </div>
              <div>
                <Label htmlFor="taxAmount">Tax Amount (17%)</Label>
                <Input
                  id="taxAmount"
                  type="number"
                  step="0.01"
                  value={taxAmount.toFixed(2)}
                  readOnly
                />
              </div>
            </div>
            <div className="border-t pt-4">
              <div className="flex justify-between items-center">
                <Label className="text-lg font-semibold">Total Amount</Label>
                <div className="text-2xl font-bold text-green-600">
                  PKR {totalAmount.toFixed(2)}
                </div>
              </div>
            </div>
          </div>
        </CardContent>
      </Card>

      <div className="flex gap-4">
        <Button
          type="submit"
          disabled={createMutation.isPending}
          className="flex-1"
        >
          {createMutation.isPending ? "Creating Invoice..." : "Create Invoice"}
        </Button>
        <Button
          type="button"
          variant="outline"
          onClick={() => onSuccess()}
        >
          Cancel
        </Button>
      </div>
    </form>
  );
}
