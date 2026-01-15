<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Asset Registry Report - {{ date('Y-m-d') }}</title>
    <style>
        @page { margin: 1cm; }
        body { 
            font-family: 'Helvetica', 'Arial', sans-serif; 
            font-size: 10pt; 
            color: #333; 
            line-height: 1.4;
        }
        /* Header Section */
        .header { border-bottom: 2px solid #0f172a; padding-bottom: 10px; margin-bottom: 20px; }
        .college-name { font-size: 18pt; font-weight: bold; color: #0f172a; text-transform: uppercase; }
        .report-title { font-size: 12pt; color: #64748b; margin-top: 5px; }
        
        /* Meta Info (Location & Date) */
        .meta-table { width: 100%; margin-bottom: 20px; }
        .meta-label { font-weight: bold; color: #475569; width: 15%; }
        .meta-value { width: 35%; }

        /* Table Styling */
        table { width: 100%; border-collapse: collapse; margin-bottom: 30px; }
        th { 
            background-color: #f8fafc; 
            color: #0f172a; 
            text-align: left; 
            padding: 10px; 
            border-bottom: 1px solid #cbd5e1; 
            text-transform: uppercase;
            font-size: 8pt;
        }
        td { padding: 10px; border-bottom: 1px solid #e2e8f0; font-size: 9pt; vertical-align: top; }
        
        /* Tags & Statuses */
        .tag { font-family: 'Courier', monospace; font-weight: bold; color: #065f46; }
        .status-badge { 
            font-size: 8pt; 
            padding: 2px 6px; 
            border-radius: 4px; 
            border: 0.5px solid #ccc;
        }

        /* Footer / Signatures */
        .footer { position: fixed; bottom: 0; width: 100%; font-size: 8pt; color: #94a3b8; text-align: center; }
        .signature-section { margin-top: 50px; width: 100%; }
        .sig-box { width: 33%; text-align: center; display: inline-block; }
        .sig-line { border-top: 1px solid #333; width: 80%; margin: 40px auto 5px auto; }
    </style>
</head>
<body>

    <div class="header">
        <div class="college-name">College of Medicine</div>
        <div class="report-title">Inventory Asset Registry Report</div>
    </div>

    <table class="meta-table">
        <tr>
            <td class="meta-label">Location:</td>
            <td class="meta-value">
                {{ Auth::user()->unit->unit_name ?? Auth::user()->department->dept_name ?? Auth::user()->office->office_name ?? Auth::user()->institute->institute_name ?? Auth::user()->faculty->faculty_name ?? 'Central Registry' }}
            </td>
            <td class="meta-label">Date Generated:</td>
            <td class="meta-value">{{ date('F d, Y') }}</td>
        </tr>
        <tr>
            <td class="meta-label">Officer:</td>
            <td class="meta-value">{{ Auth::user()->profile->full_name ?? Auth::user()->username }}</td>
            <td class="meta-label">Total Items:</td>
            <td class="meta-value">{{ number_format($assets->sum('quantity')) }}</td>
        </tr>
    </table>

    <table>
        <thead>
            <tr>
                <th width="5%">S/N</th>
                <th width="25%">Asset Name</th>
                <th width="20%">Inventory Tag</th>
                <th width="15%">Serial Number</th>
                <th width="10%">Qty</th>
                <th width="15%">Category</th>
                <th width="10%">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($assets as $index => $asset)
            <tr>
                <td>{{ $index + 1 }}</td>
                <td><strong>{{ $asset->item_name }}</strong></td>
                <td class="tag">{{ $asset->asset_tag ?? 'NOT TAGGED' }}</td>
                <td><code>{{ $asset->serial_number ?? 'N/A' }}</code></td>
                <td>{{ number_format($asset->quantity) }}</td>
                <td>{{ $asset->subcategory->subcategory_name ?? $asset->category->name }}</td>
                <td>{{ ucfirst($asset->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="signature-section">
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>Prepared By (Staff)</strong>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>Verified By (Auditor)</strong>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <strong>Departmental Stamp</strong>
        </div>
    </div>

    <div class="footer">
        Generated via College of Medicine Inventory System on {{ date('Y-m-d H:i:s') }}
    </div>

</body>
</html>