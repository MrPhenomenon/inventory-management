<?php $this->beginPage() ?>
<!doctype html>
<html lang="en">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title><?=$this->params['subject']?></title>
    <style media="all" type="text/css">
        body {
            -webkit-font-smoothing: antialiased;
            -ms-text-size-adjust: 100%;
            -webkit-text-size-adjust: 100%;
            font-family: Helvetica, sans-serif;
            font-size: 16px;
            line-height: 1.6;
            margin: 0;
            padding: 0;
        }

        .wrapper {
            box-sizing: border-box;
            padding: 24px 24px 24px 0px;
            max-width: 600px;
        }

        p {
            font-family: Helvetica, sans-serif;
            font-size: 16px;
            margin: 0;
            margin-bottom: 16px;
        }

        a {
            color: #000854;
            text-decoration: underline;
        }

        .disclaimer {
            font-size: 12px;
            color: #555555;
        }

        @media only screen and (max-width: 640px) {}

        @media all {}
    </style>
    <?php $this->head() ?>
</head>

<body>
    <?php $this->beginBody() ?>
    <div class="wrapper">
        <div class="content">
            <p>Attention: <?= $this->params['name'] ?>,</p>
            <?= $content ?>
        </div>

        <div class="footer">
            <p>If you have questions or require assistance, you may contact us at any time.</p>
            <p>EIN Applications Team <br>support@einapplications.org <br><a href="https://einapplications.org/chat-support/">Support Chat</a></p>
            <!-- <hr style="border-top: 1px solid #cbcbcb; margin: 30px 0px 30px 0px;"> -->
            <!-- <p class="disclaimer">Confidentiality and Intended Recipient Notice: <br>This email and any attachments are intended solely for the individual or entity to whom they are addressed. They may contain confidential or privileged information. If you are not the intended recipient, please delete this message immediately. Any unauthorized review, use, disclosure, distribution, or copying of this communication is strictly prohibited.</p> -->
            <!-- <p class="disclaimer">This communication has been prepared by EINApplications.org, an independent filing assistance service that facilitates and delivers EIN confirmation documents issued by the Internal Revenue Service. EINApplications.org is not a government agency and is not affiliated, endorsed, or associated with any such agency.</p> -->
        </div>
    </div>
    <?php $this->endBody() ?>
</body>

</html>
<?php $this->endPage() ?>