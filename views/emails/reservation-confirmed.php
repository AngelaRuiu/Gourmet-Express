<div style="font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; border: 1px solid #e2e8f0; border-radius: 8px; overflow: hidden;">
    <div style="background-color: #1e293b; padding: 20px; text-align: center;">
        <h1 style="color: #ffffff; margin: 0; font-size: 24px;">Gourmet Express</h1>
    </div>
    
    <div style="padding: 30px; line-height: 1.6; color: #334155;">
        <h2 style="color: #1e293b;">Hello <?= htmlspecialchars($name) ?>,</h2>
        <p>We are delighted to confirm your reservation. We’ve saved a table for you!</p>
        
        <div style="background-color: #f8fafc; padding: 20px; border-radius: 6px; margin: 20px 0;">
            <h3 style="margin-top: 0; font-size: 16px; text-transform: uppercase; color: #64748b;">Reservation Details</h3>
            <table style="width: 100%; border-collapse: collapse;">
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Date:</td>
                    <td style="padding: 5px 0; text-align: right;"><?= $details['date'] ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Time:</td>
                    <td style="padding: 5px 0; text-align: right;"><?= $details['time'] ?></td>
                </tr>
                <tr>
                    <td style="padding: 5px 0; font-weight: bold;">Guests:</td>
                    <td style="padding: 5px 0; text-align: right;"><?= $details['guests'] ?> People</td>
                </tr>
            </table>
        </div>

        <div style="text-align: center; margin-top: 30px;">
            <a href="<?= $app_url ?>/reservations/manage" 
               style="background-color: #0f172a; color: #ffffff; padding: 12px 24px; text-decoration: none; border-radius: 5px; font-weight: bold;">
               Manage Reservation
            </a>
        </div>
    </div>

    <div style="background-color: #f1f5f9; padding: 15px; text-align: center; font-size: 12px; color: #94a3b8;">
        © <?= date('Y') ?> Gourmet Express.
    </div>
</div>