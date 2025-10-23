<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Reset Password - LMS MOCC Mitra</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .header { background: #014cbb; color: white; padding: 20px; text-align: center; }
        .content { background: #f9f9f9; padding: 20px; }
        .button { background: #014cbb; color: white; padding: 12px 24px; text-decoration: none; border-radius: 5px; display: inline-block; }
        .footer { text-align: center; margin-top: 20px; font-size: 12px; color: #666; }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h2>BPS Kabupaten Tanah Laut</h2>
            <p>Learning Management System MOCC Mitra</p>
        </div>
        
        <div class="content">
            <h3>Reset Password Akun Anda</h3>
            
            <p>Halo,</p>
            
            <p>Kami menerima permintaan untuk mereset password akun LMS MOCC Mitra dengan username: <strong>{{ $username }}</strong></p>
            
            <p>Silakan klik tombol di bawah ini untuk mereset password Anda:</p>
            
            <p style="text-align: center;">
                <a href="{{ $resetUrl }}" class="button">Reset Password</a>
            </p>
            
            <p>Link ini akan kadaluarsa dalam 60 menit.</p>
            
            <p>Jika Anda tidak meminta reset password, abaikan email ini.</p>
            
            <p>Salam,<br>Tim LMS MOCC Mitra<br>BPS Kabupaten Tanah Laut</p>
        </div>
        
        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
        </div>
    </div>
</body>
</html>