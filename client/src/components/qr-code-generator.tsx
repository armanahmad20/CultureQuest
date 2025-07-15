import { useEffect, useRef } from "react";

interface QrCodeGeneratorProps {
  data: string;
  size?: number;
}

export default function QrCodeGenerator({ data, size = 200 }: QrCodeGeneratorProps) {
  const canvasRef = useRef<HTMLCanvasElement>(null);

  useEffect(() => {
    const canvas = canvasRef.current;
    if (!canvas) return;

    const ctx = canvas.getContext("2d");
    if (!ctx) return;

    // Simple QR code placeholder visualization
    // In a real implementation, you would use a QR code library
    ctx.fillStyle = "#000000";
    ctx.fillRect(0, 0, size, size);

    ctx.fillStyle = "#ffffff";
    ctx.fillRect(10, 10, size - 20, size - 20);

    ctx.fillStyle = "#000000";
    ctx.font = "12px monospace";
    ctx.textAlign = "center";
    ctx.fillText("QR CODE", size / 2, size / 2 - 10);
    ctx.fillText("PLACEHOLDER", size / 2, size / 2 + 10);

    // Add some QR-like patterns
    for (let i = 0; i < 10; i++) {
      for (let j = 0; j < 10; j++) {
        if (Math.random() > 0.5) {
          ctx.fillRect(20 + i * 16, 20 + j * 16, 8, 8);
        }
      }
    }

    // Add corner squares (typical QR code feature)
    ctx.fillRect(20, 20, 40, 40);
    ctx.fillRect(size - 60, 20, 40, 40);
    ctx.fillRect(20, size - 60, 40, 40);

    ctx.fillStyle = "#ffffff";
    ctx.fillRect(30, 30, 20, 20);
    ctx.fillRect(size - 50, 30, 20, 20);
    ctx.fillRect(30, size - 50, 20, 20);

    ctx.fillStyle = "#000000";
    ctx.fillRect(35, 35, 10, 10);
    ctx.fillRect(size - 45, 35, 10, 10);
    ctx.fillRect(35, size - 45, 10, 10);
  }, [data, size]);

  return (
    <div className="flex flex-col items-center space-y-2">
      <canvas
        ref={canvasRef}
        width={size}
        height={size}
        className="border border-gray-300 rounded"
      />
      <p className="text-xs text-gray-500 text-center max-w-[200px] break-all">
        {data}
      </p>
      <p className="text-xs text-gray-400 text-center">
        Scan with FBR Tax Asaan app for verification
      </p>
    </div>
  );
}
