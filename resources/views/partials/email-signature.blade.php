<table cellpadding="0" cellspacing="0" style="font-family: Arial, sans-serif; font-size:10px; color:#333;">
    <tr>
        <!-- Left: Company Logo -->
        <td style="padding: 10px 20px; text-align: center;">
            <img src="{{ $companyLogo }}" alt="Company Logo" width="80" style="margin-bottom: 8px;">
            <div style="font-size: 10px; font-weight: bold; color: #555;">
                {{ $company ?? 'Volks Energie Pvt. Ltd.' }}
            </div>
        </td>

        <!-- Middle Line -->
        <td style="border-left: 2px solid #ccc; width: 1px;"></td>

        <!-- Right: Details -->
        <td style="padding: 10px 20px;">
            <div style="font-size: 12px; font-weight: bold; color: #1a73e8;">
                {{ $name }}
            </div>
            <div style="text-transform: uppercase; font-size: 10px; color: #888;">
                {{ str_replace('-', ' ', $designation) }}
            </div>
            <br>

            @if ($phone)
                <div style="font-size:10px;">
                    📞 {{ $phone }}
                </div>
            @endif

            <div style="font-size:10px;">
                ✉️ <a href="mailto:{{ $email }}">{{ $email }}</a>
            </div>

            <div style="font-size:10px;">
                🔗 <a href="{{ $website }}">{{ $website }}</a>
            </div>
        </td>
    </tr>
</table>
