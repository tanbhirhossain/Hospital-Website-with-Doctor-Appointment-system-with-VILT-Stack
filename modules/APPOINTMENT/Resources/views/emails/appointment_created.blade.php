<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        /* Modern, clean font stack */
        body { font-family: 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; -webkit-font-smoothing: antialiased; }
    </style>
</head>
<body style="margin: 0; padding: 0; background-color: #f8fafc; color: #1e293b;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="padding: 40px 0;">
        <tr>
            <td align="center">
                <!-- Main Card -->
                <table role="presentation" width="550" cellspacing="0" cellpadding="0" border="0" style="background-color: #ffffff; border-radius: 24px; overflow: hidden; box-shadow: 0 20px 40px rgba(0,0,0,0.06);">
                    <!-- Brand Hero Section -->
                    <tr>
                        <td align="center" style="padding: 48px 40px; background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);">
                            <div style="font-size: 28px; font-weight: 300; color: #ffffff; letter-spacing: 2px; text-transform: uppercase;">AMZ Hospital</div>
                            <div style="height: 1px; width: 40px; background: #38bdf8; margin: 20px auto 0;"></div>
                        </td>
                    </tr>
                    
                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px;">
                            <h1 style="font-size: 24px; font-weight: 400; color: #0f172a; margin: 0 0 20px 0;">Appointment Request</h1>
                            <p style="font-size: 16px; line-height: 1.7; color: #64748b; margin-bottom: 30px;">
                                Dear <strong>{{ $appointment->patient_name ?? 'Valued Patient' }}</strong>, your request has been received. We are preparing for your visit to ensure you receive the premium care you deserve.
                            </p>

                            <!-- Glassmorphism-inspired Doctor Detail -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="background-color: #f1f5f9; border-radius: 16px; padding: 20px;">
                                <tr>
                                    @if($doctor)
                                        <td width="70"><img src="{{ $doctor->profile_image_url }}" alt="Doctor" style="width: 64px; height: 64px; border-radius: 50%; display: block; object-fit: cover;"></td>
                                        <td style="padding-left: 20px;">
                                            <div style="font-weight: 600; color: #0f172a; font-size: 16px;">Dr. {{ $doctor->name }}</div>
                                            <div style="font-size: 14px; color: #64748b; margin-top: 2px;">{{ $doctor->specialty }}</div>
                                        </td>
                                    @endif
                                </tr>
                            </table>

                            <!-- Details Table -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 30px;">
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Serial Number</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600;">#{{ $appointment->serial_number }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; color: #64748b;">Date</td>
                                    <td style="padding: 12px 0; border-bottom: 1px solid #e2e8f0; text-align: right; font-weight: 600;">{{ \Carbon\Carbon::parse($appointment->appointment_date)->format('d M, Y') }}</td>
                                </tr>
                                <tr>
                                    <td style="padding: 12px 0; color: #64748b;">Time</td>
                                    <td style="padding: 12px 0; text-align: right; font-weight: 600;">{{ $appointment->start_time }}</td>
                                </tr>
                            </table>

                            <!-- CTA Button -->
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" border="0" style="margin-top: 40px;">
                                <tr>
                                    <td align="center">
                                        <a href="#" style="background-color: #0f172a; color: #ffffff; padding: 14px 30px; text-decoration: none; border-radius: 50px; font-weight: 500; font-size: 14px; letter-spacing: 1px;">VIEW APPOINTMENT</a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>

                    <!-- Elegant Footer -->
                    <tr>
                        <td align="center" style="padding: 30px; background-color: #f8fafc; font-size: 11px; color: #94a3b8; letter-spacing: 1px; text-transform: uppercase;">
                            AMZ Hospital Ltd &bull; Trusted Excellence
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>