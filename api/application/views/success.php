<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Sucess</title>
    <style>
        @import url('https://fonts.googleapis.com/css?family=Reem+Kufi');
        body {
            background: #fff;
            color: #000;
            font-size: 17px;
            font-family: 'Reem Kufi', sans-serif;
            margin: 0;
        }
        .content-main {
            text-align: center;
            position: absolute;
            width: 100%;
            top: 50%;
            left: 50%;
            transform: translate(-50%,-50%);
            -webkit-transform: translateY(-50%) translateX(-50%);
            -ms-transform: translateY(-50%) translateX(-50%);
        }
        .sucessimg img {
            width: 50px;
        }
        .sucessimg {
            text-align: center;
        }
        .loadergif img {
            width: 300px;
        }
        .loadergif {
            text-align: center;
        }
        .content-main h3 {
            font-size: 50px;
            margin: 20px 0 0;
        }
        .content-main h5 {
            font-size: 30px;
            margin: 0;
        }
    </style>
</head>
<body>
    <div class="content-main">
        <div class="sucessimg">
            <img src="<?php echo base_url() ?>assets/ico/success.png">
        </div>
        <h3>Thank You</h3>
        <h5>Your payment has been successfully submitted.</h5>
        <h5>Please wait while we are redirecting...</h5>
        <div class="loadergif">
            <img src="<?php echo base_url() ?>assets/ico/loading.gif">
        </div>
    </div>
</body>
</html>