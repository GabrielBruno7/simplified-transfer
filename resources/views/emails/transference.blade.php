<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Transferência Recebida - PicPay Simplificado</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap');
    </style>
</head>
<body style="margin: 0; padding: 0; font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif; background-color: #f8fafc; color: #1a202c;">
    <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="100%" style="background-color: #f8fafc;">
        <tr>
            <td align="center" style="padding: 40px 20px;">
                <table role="presentation" cellspacing="0" cellpadding="0" border="0" width="600" style="max-width: 600px; background-color: #ffffff; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05); overflow: hidden;">
                    <!-- Header -->
                    <tr>
                        <td style="background: linear-gradient(135deg, #00d4aa 0%, #00b894 100%); padding: 40px 30px; text-align: center;">
                            <div style="width: 64px; height: 64px; background-color: rgba(255, 255, 255, 0.2); border-radius: 50%; display: inline-flex; align-items: center; justify-content: center; margin-bottom: 20px;">
                                <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M12 2L15.09 8.26L22 9L17 14.14L18.18 21L12 17.77L5.82 21L7 14.14L2 9L8.91 8.26L12 2Z" fill="white"/>
                                </svg>
                            </div>
                            <h1 style="color: #ffffff; font-size: 28px; font-weight: 700; margin: 0 0 8px 0; line-height: 1.3;">Transferência Recebida!</h1>
                            <p style="color: rgba(255, 255, 255, 0.9); font-size: 16px; margin: 0; font-weight: 400;">Uma nova transferência foi processada com sucesso</p>
                        </td>
                    </tr>

                    <!-- Content -->
                    <tr>
                        <td style="padding: 40px 30px;">
                            <!-- Success Badge -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <span style="background-color: #d1fae5; color: #047857; font-size: 14px; font-weight: 600; padding: 8px 16px; border-radius: 20px; display: inline-block;">
                                    ✅ Concluída
                                </span>
                            </div>

                            <!-- Transfer Details -->
                            <div style="background-color: #f8fafc; border-radius: 8px; padding: 24px; margin-bottom: 24px;">
                                <h2 style="color: #1a202c; font-size: 18px; font-weight: 600; margin: 0 0 20px 0;">Detalhes da Transferência</h2>
                                
                                <div style="margin-bottom: 16px;">
                                    <p style="color: #64748b; font-size: 14px; margin: 0 0 4px 0; font-weight: 500;">Remetente</p>
                                    <p style="color: #1a202c; font-size: 16px; font-weight: 600; margin: 0;">{{ $sender }}</p>
                                </div>

                                <div style="border-top: 1px solid #e2e8f0; padding-top: 16px;">
                                    <p style="color: #64748b; font-size: 14px; margin: 0 0 4px 0; font-weight: 500;">Valor Recebido</p>
                                    <p style="color: #00b894; font-size: 24px; font-weight: 700; margin: 0;">R$ {{ number_format($amount, 2, ',', '.') }}</p>
                                </div>
                            </div>

                            <!-- Message -->
                            <div style="text-align: center; margin-bottom: 32px;">
                                <p style="color: #64748b; font-size: 16px; line-height: 1.6; margin: 0;">
                                    O valor já está disponível em sua conta e pode ser utilizado imediatamente.
                                </p>
                            </div>

                            <!-- CTA Button -->
                            <div style="text-align: center;">
                                <a href="#" style="background: linear-gradient(135deg, #00d4aa 0%, #00b894 100%); color: #ffffff; text-decoration: none; font-size: 16px; font-weight: 600; padding: 14px 32px; border-radius: 8px; display: inline-block; transition: all 0.2s ease;">
                                    Ver Extrato
                                </a>
                            </div>
                        </td>
                    </tr>

                    <!-- Footer -->
                    <tr>
                        <td style="background-color: #f8fafc; padding: 30px; text-align: center; border-top: 1px solid #e2e8f0;">
                            <p style="color: #64748b; font-size: 14px; margin: 0 0 8px 0;">PicPay Simplificado</p>
                            <p style="color: #9ca3af; font-size: 12px; margin: 0; line-height: 1.4;">
                                Este é um email automático, não é necessário respondê-lo.<br>
                                Se você tem dúvidas, entre em contato conosco.
                            </p>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
