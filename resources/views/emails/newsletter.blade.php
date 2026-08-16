@php $company = \App\Models\CompanySetting::current(); @endphp
<!DOCTYPE html>
<html>
<head><meta charset="utf-8"></head>
<body style="font-family: Arial, sans-serif; color: #1f2937; max-width: 600px; margin: 0 auto; padding: 24px;">
    <h2 style="margin-bottom: 4px;">{{ $company->name }}</h2>
    <p style="color: #6b7280; margin-top: 0;">{{ $newsletterSubject }}</p>

    <div style="white-space: pre-line; margin: 16px 0;">{{ $body }}</div>

    <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 24px 0;">

    <p style="margin-top: 24px;">— {{ $company->name }}</p>
</body>
</html>
