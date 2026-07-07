<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boost Purchase Confirmation</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f4f4f4;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            border-bottom: 3px solid #007bff;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #007bff;
            margin: 0;
        }
        .boost-info {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .boost-info h3 {
            color: #007bff;
            margin-top: 0;
        }
        .info-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #e0e0e0;
        }
        .info-row:last-child {
            border-bottom: none;
        }
        .info-label {
            font-weight: bold;
            color: #555;
        }
        .info-value {
            color: #333;
        }
        .products-list {
            margin: 20px 0;
        }
        .product-item {
            background-color: #fff;
            border-left: 4px solid #28a745;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .product-name {
            font-weight: bold;
            color: #28a745;
            font-size: 16px;
        }
        .product-details {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .duration-box {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            border-radius: 10px;
            text-align: center;
            margin: 20px 0;
        }
        .duration-box h3 {
            margin: 0 0 10px 0;
            font-size: 24px;
        }
        .duration-box p {
            margin: 5px 0;
            font-size: 16px;
        }
        .remaining-time {
            font-size: 32px;
            font-weight: bold;
            margin: 10px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
            color: #888;
            font-size: 12px;
        }
        .badge {
            display: inline-block;
            padding: 5px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: bold;
        }
        .badge-success {
            background-color: #28a745;
            color: white;
        }
        .badge-info {
            background-color: #17a2b8;
            color: white;
        }
        @media screen and (max-width: 600px) {
            .email-wrapper {
                padding: 15px;
            }
            .info-row {
                flex-direction: column;
            }
            .info-label {
                margin-bottom: 5px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <div class="header">
            <h2>🚀 Boost Purchase Confirmation</h2>
        </div>

        @if($recipientType === 'vendor')
            <p>Hi <strong>{{ $vendor->first_name ?? 'Vendor' }}</strong>,</p>
            <p>Your boost purchase has been confirmed successfully! Your products are now being promoted on our platform.</p>
        @else
            <p>Hi Admin,</p>
            <p>A new boost purchase has been made by <strong>{{ $vendor->first_name ?? 'Vendor' }}</strong>.</p>
        @endif

        <div class="boost-info">
            <h3>📦 Boost Package Details</h3>
            <div class="info-row">
                <span class="info-label">Package Name:</span>
                <span class="info-value">{{ $package->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span class="info-value">${{ number_format($vendorBoost->amount, 2) }} {{ strtoupper($vendorBoost->currency) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Method:</span>
                <span class="info-value">
                    <span class="badge badge-info">{{ strtoupper($vendorBoost->payment_method) }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Payment Status:</span>
                <span class="info-value">
                    <span class="badge badge-success">{{ strtoupper($vendorBoost->payment_status) }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Transaction ID:</span>
                <span class="info-value">{{ $vendorBoost->transaction_id ?? 'N/A' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Boost Position:</span>
                <span class="info-value">{{ $vendorBoost->position->name ?? 'Not Specified' }}</span>
            </div>
        </div>

        <div class="duration-box">
            <h3>⏰ Boost Duration</h3>
            <p><strong>Start Date:</strong> {{ \Carbon\Carbon::parse($vendorBoost->start_date)->format('d M Y, h:i A') }}</p>
            <p><strong>End Date:</strong> {{ \Carbon\Carbon::parse($vendorBoost->end_date)->format('d M Y, h:i A') }}</p>
            <div class="remaining-time">
                @if($remainingDays > 0)
                    {{ $remainingDays }} Days Remaining
                @elseif($remainingHours > 0)
                    {{ $remainingHours }} Hours Remaining
                @else
                    Expired
                @endif
            </div>
            <p style="margin-top: 10px;">Total Duration: {{ $package->duration_days }} Days</p>
        </div>

        <div class="products-list">
            <h3 style="color: #28a745;">📦 Boosted Products ({{ $products->count() }})</h3>
            @foreach($products as $boostedProduct)
                <div class="product-item">
                    <div class="product-name">{{ $boostedProduct->product->name ?? 'N/A' }}</div>
                    <div class="product-details">
                        <strong>SKU:</strong> {{ $boostedProduct->product->sku ?? 'N/A' }} | 
                        <strong>Price:</strong> ${{ number_format($boostedProduct->product->price ?? 0, 2) }} |
                        <strong>Stock:</strong> {{ $boostedProduct->product->stock_quantity ?? 0 }}
                    </div>
                    <div class="product-details" style="margin-top: 5px;">
                        <strong>Boost Start:</strong> {{ \Carbon\Carbon::parse($boostedProduct->boost_start)->format('d M Y, h:i A') }} |
                        <strong>Boost End:</strong> {{ \Carbon\Carbon::parse($boostedProduct->boost_end)->format('d M Y, h:i A') }}
                    </div>
                </div>
            @endforeach
        </div>

        @if($recipientType === 'vendor')
            <div style="background-color: #e7f3ff; padding: 15px; border-radius: 5px; margin: 20px 0;">
                <p style="margin: 0;"><strong>💡 Important:</strong></p>
                <ul style="margin: 10px 0; padding-left: 20px;">
                    <li>Your products will appear in the <strong>{{ $vendorBoost->position->name ?? 'selected' }}</strong> section on the home page</li>
                    <li>Boost will automatically expire on {{ \Carbon\Carbon::parse($vendorBoost->end_date)->format('d M Y, h:i A') }}</li>
                    <li>You can purchase another boost package anytime before expiry</li>
                </ul>
            </div>
        @endif

        <div class="footer">
            <p>Thank you for using our Boost Service!</p>
            <p>© {{ date('Y') }} Part Synch. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

