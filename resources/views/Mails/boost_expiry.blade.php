<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Boost Package Expired</title>
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
            border-bottom: 3px solid #dc3545;
            padding-bottom: 20px;
            margin-bottom: 30px;
        }
        .header h2 {
            color: #dc3545;
            margin: 0;
        }
        .alert-box {
            background-color: #fff3cd;
            border-left: 4px solid #ffc107;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .alert-box h3 {
            color: #856404;
            margin-top: 0;
        }
        .expired-info {
            background-color: #f8d7da;
            border-left: 4px solid #dc3545;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
        }
        .expired-info h3 {
            color: #dc3545;
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
            border-left: 4px solid #dc3545;
            padding: 15px;
            margin: 10px 0;
            border-radius: 5px;
        }
        .product-name {
            font-weight: bold;
            color: #dc3545;
            font-size: 16px;
        }
        .product-details {
            color: #666;
            font-size: 14px;
            margin-top: 5px;
        }
        .cta-button {
            display: inline-block;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 15px 30px;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            text-align: center;
            margin: 20px 0;
            display: block;
            text-align: center;
        }
        .cta-button:hover {
            opacity: 0.9;
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
        .badge-danger {
            background-color: #dc3545;
            color: white;
        }
        .badge-warning {
            background-color: #ffc107;
            color: #333;
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
            <h2>⏰ Your Boost Package Has Expired</h2>
        </div>

        <p>Hi <strong>{{ $vendor->first_name ?? 'Vendor' }}</strong>,</p>
        
        <div class="alert-box">
            <h3>⚠️ Important Notice</h3>
            <p>Your boost package has expired. Your products are no longer being promoted in the boosted sections on our platform.</p>
        </div>

        <div class="expired-info">
            <h3>📦 Expired Boost Package Details</h3>
            <div class="info-row">
                <span class="info-label">Package Name:</span>
                <span class="info-value">{{ $package->name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Amount Paid:</span>
                <span class="info-value">${{ number_format($vendorBoost->amount, 2) }} {{ strtoupper($vendorBoost->currency) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Expired Date:</span>
                <span class="info-value">
                    <span class="badge badge-danger">{{ $expiredDate }}</span>
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Boost Position:</span>
                <span class="info-value">{{ $vendorBoost->position->name ?? 'Not Specified' }}</span>
            </div>
        </div>

        <div class="products-list">
            <h3 style="color: #dc3545;">📦 Products No Longer Boosted ({{ $products->count() }})</h3>
            @foreach($products as $boostedProduct)
                <div class="product-item">
                    <div class="product-name">{{ $boostedProduct->product->name ?? 'N/A' }}</div>
                    <div class="product-details">
                        <strong>SKU:</strong> {{ $boostedProduct->product->sku ?? 'N/A' }} | 
                        <strong>Price:</strong> ${{ number_format($boostedProduct->product->price ?? 0, 2) }}
                    </div>
                    <div class="product-details" style="margin-top: 5px; color: #dc3545;">
                        <strong>Boost Ended:</strong> {{ \Carbon\Carbon::parse($boostedProduct->boost_end)->format('d M Y, h:i A') }}
                    </div>
                </div>
            @endforeach
        </div>

        <div style="background-color: #e7f3ff; padding: 20px; border-radius: 5px; margin: 20px 0; text-align: center;">
            <h3 style="color: #007bff; margin-top: 0;">🔄 Renew Your Boost Package</h3>
            <p>Don't let your products lose visibility! Purchase a new boost package to keep your products promoted on our platform.</p>
            <p style="margin: 15px 0;"><strong>Benefits of Renewing:</strong></p>
            <ul style="text-align: left; display: inline-block; margin: 10px 0;">
                <li>Increased product visibility</li>
                <li>Higher placement on home page</li>
                <li>More customer engagement</li>
                <li>Better sales opportunities</li>
            </ul>
            <p style="margin-top: 20px;">
                <strong>Log in to your vendor dashboard to purchase a new boost package now!</strong>
            </p>
        </div>

        <div style="background-color: #f8f9fa; padding: 15px; border-radius: 5px; margin: 20px 0;">
            <p style="margin: 0;"><strong>💡 What Happens Next?</strong></p>
            <ul style="margin: 10px 0; padding-left: 20px;">
                <li>Your products have been removed from boosted sections</li>
                <li>Products are still available in regular product listings</li>
                <li>You can purchase a new boost package anytime</li>
                <li>Your previous boost history is saved in your account</li>
            </ul>
        </div>

        <div class="footer">
            <p>Thank you for using our Boost Service!</p>
            <p>If you have any questions, please contact our support team.</p>
            <p>© {{ date('Y') }} Part Synch. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

