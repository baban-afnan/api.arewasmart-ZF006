<x-mail::message>
# Two Factor Authentication

Your login code is: **{{ $code }}**

This code will expire in 10 minutes.

If you did not request this code, please ignore this email.

**Important:**
- This code will expire in **10 minutes**
- Do not share this code with anyone
- If you did not request this code, please ignore this email and secure your account

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
