<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>New class Created</title> 
</head>
<body>
 

    <div style="width: 100%; background-color: #ffffff; padding: 40px 0;">
        <div style="max-width: 700px; margin: 0 auto; background-color: #1E88E5; border-radius: 8px; color: white; font-family: Arial, sans-serif;">
            
            {{-- Header Row --}}
            <div style="padding: 20px 30px; font-size: 12px;">
                <div style="display: table; width: 100%;">
                    <div style="display: table-cell; text-align: left; letter-spacing: 1px;">NEW CLASS MAIL</div> 
                    <div style="display: table-cell; text-align: right; letter-spacing: 1px;">
                        @php
                            $date = date('l, d M');
                            $formattedDate = strtoupper($date);
                        @endphp
                        {{ $formattedDate }}
                    </div>
                </div>
            </div>
    
            {{-- Main message --}}
            <div style="text-align: center; padding: 0 30px 25px 30px;">
                <h2 style="margin: 10px 0 5px 0; color: white;">Hello, {{ $user->name }}</h2>
                <p style="margin: 0; font-size: 16px; color: white;"> 
                    A new <strong>{{ $className }}</strong> has been created taught by
                    <strong>{{ $teacherName }}</strong> on <strong>{{ \Carbon\Carbon::parse($classDate)->format('F j, Y') }}</strong>.
                    Stay tuned for more info.
                </p> 
                <p>Thank you,<br>Livewire Student-Management</p>
           </div> 
        </div>
    </div>
</body>
</html>