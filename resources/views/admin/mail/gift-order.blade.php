<!DOCTYPE html>
<html>
<head>
    <title>Gift Order of -  {{ $emailData['retailers'] }}</title>
</head>
<body>
    
    <table style="padding:0; margin:0; width:600px; margin:0 auto; border:1px solid #000;">
        <tr>
            <td>
                <p style="font-size:16px; fonr-weight:bold;">Dear Jyoti,</p>
                <p style="font-size:14px; color:#000;">Please find attached the details of the retailer who has claimed a gift through the Cozi Club app. Kindly place an order for the same.</p>
                <p style="font-size:14px; color:#000;">Retailer is:</p>
                <p style="font-size:14px; color:#000;">1. {{ $emailData['retailers'] }}</p>
                <p>Thanks & Regards,</p>
                <p>Supriya Kumar Bhar<br>Contact : 9147040104<br>Lux Industries Limited</p>
            </td>
        </tr>
    </table>


    
</body>
</html>