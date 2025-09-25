<!DOCTYPE html>
<html>
<head>
    <title>PYDI Notification</title>
    <style>
        .email-container{
            width: 100%;
            height: 100%;
            display: flex;
            align-items: center;
            padding: 50px;
            flex-direction: column;
        }

        .email-form{
            width: 600px;
            height: fit-content;
            background: #ffffff;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 10px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.05);
        }

        .email-form .email-for-title{
            font-size: 22px;
            margin-bottom: 30px;
            vertical-align: middle;
            padding-bottom: 15px;
            width: 100%;
        }

        .header{
            position: relative;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 10px 0;
        }

        .header .light-blue{
            position: absolute;
            right: 0;
        }

        .email-greetings{
            box-sizing:border-box;
            font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
            color:#3d4852;
            font-size:16px;
            font-weight:bold;
            margin-top:0;
            margin-bottom: 10px;
            text-align: center;
            width: 100%;
        }

        .email-greetings-2{
            box-sizing:border-box;
            font-family:-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif,'Apple Color Emoji','Segoe UI Emoji','Segoe UI Symbol';
            color:#3d4852;
            font-size:16px;
            font-weight:bold;
            margin-top:0;
            margin-bottom: 10px;
            text-align: left;
        }

        .email{
            background: #edf2f7;
            display: flex;
            justify-content: center;
            padding: 20px;
            overflow: hidden;
            width: fit-content;
        }

        .email .email-wrapper{
            display: block;
        }

        .email .email-header{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .email .email-footer{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
        }

        .email .email-body{
            background: white;
            padding: 20px;
            margin-bottom: 30px;
        }

        .email .action-wrapper{
            width: 100%;
            display: flex;
            justify-content: center;
            align-items: center;
            margin: 20px 0;
        }

        .email .message{
            color: #797979;
        }

        .email .footer-content{
            font-size: 13px;
            color: gray;
        }

        .email .action-btn{
            padding: 3px 15px 5px 15px;
            border: none;
            border-radius: 5px;
            background: #0061C4;
            color: white;
        }

        .email .action-btn:focus{
            outline: none;
            border: none;
        }
    </style>
</head>

<body>

    @yield ('content')

</body>

</html>
