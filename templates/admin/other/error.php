<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $msg['title'] ??'错误'; ?></title>
    <style>
        *{
            margin: 0;
            padding: 0;
            color: #31313f;
        }
        .error{
            width: 730px;
            margin: 10% auto;
            font-size: 14px;
            background-color:#f3f7f9 ;
            border: 1px solid #cacad9;
            height: auto;
        }
        .title{
            background-color: #f44336;
            color: #fff;
            height: 46px;
            line-height: 46px;
            padding-left: 20px;
        }
        .content{
            padding: 35px 22px 50px 22px;
        }
        .content .result{
            color: #f34335;
            font-weight: 600;
        }
        .content .reson{
            margin: 15px 0 20px 0;
        }
        .content .reson .resonLeft{
            font-weight: 500;
            color: #000;
        }
        .content .reson .resonRight{
            max-width: 600px;
        }
        .content .reson span{
            display: inline-block;
            vertical-align: top;
            text-align: justify;
        }
        .methodTitle{
            color: #000;
            font-weight: 600;
            margin-bottom: 10px;
        }
        .methodItems p{
            line-height: 22px;
            margin-bottom: 10px;
            padding-left: 54px;
            text-align: justify;
        }
    </style>
</head>
<body>
    <div class="error">
        <p class="title"><?php echo $msg['title'] ??'错误'; ?></p>
        <div class="content">
            <p class="result"><?php echo $msg['big_title'] ??''; ?></p>
            <div class="method">
                <p class="methodTitle"><?php echo $msg['methodTitle'] ??'可能原因：'; ?></p>
                <div class="methodItems">
                    <?php echo $msg['content']; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>