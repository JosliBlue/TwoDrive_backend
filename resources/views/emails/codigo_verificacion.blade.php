<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <style>
        body {
            font-family: 'Segoe UI', 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }

        .header {
            text-align: center;
            padding: 20px 0;
            border-bottom: 1px solid #eaeaea;
        }

        .logo {
            max-width: 150px;
        }

        .content {
            padding: 20px 0;
        }

        .code-container {
            background: #f5f7fa;
            padding: 15px;
            border-radius: 8px;
            text-align: center;
            margin: 25px 0;
            font-size: 24px;
            font-weight: bold;
            color: #2c3e50;
            letter-spacing: 2px;
        }

        .footer {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eaeaea;
            font-size: 12px;
            color: #7f8c8d;
            text-align: center;
        }

        .button {
            display: inline-block;
            padding: 12px 24px;
            background-color: #3498db;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-weight: bold;
            margin-top: 15px;
        }
    </style>
</head>

<body>
    <div class="header">
        <img src="https://example.com/logo.png" alt="TwoDrive Logo" class="logo">
        <h2>Verificación de tu cuenta</h2>
    </div>
    <div class="content">
        <p>Hola,</p>
        <p>Hemos recibido una solicitud para verificar tu cuenta en TwoDrive. Utiliza el siguiente código de
            verificación:</p>
        <div class="code-container">
            {{ $code }}
        </div>
        <p>Este código expirará en <strong>15 minutos</strong>. Si no has solicitado este código, puedes ignorar este
            mensaje.</p>
        <p>Gracias,<br>El equipo de TwoDrive</p>
    </div>
    <div class="footer">
        <p>© {{ $year }} TwoDrive. Todos los derechos reservados.</p>
        <p>Si tienes alguna pregunta, contáctanos en soporte@twodrive.com</p>
    </div>
</body>

</html>
