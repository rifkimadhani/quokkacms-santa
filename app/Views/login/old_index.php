<?php
    //handle flashdata
    //
    $htmlFlashdata='';
    if (empty(session()->getFlashdata('type'))==false){
        $type = session()->getFlashdata('type');
        $message = session()->getFlashdata('message');
        if ($type=='error'){
            $html = <<<HTML
    <div class="alert alert-danger login-alert" role="alert">
        <center>{$message}</center>
    </div>
HTML;
        } else {
            $html = <<<HTML
    <div class="alert alert-success" role="success">
        <center>{$message}</center>
    </div>
HTML;
        }
        $htmlFlashdata = $html;
    }
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>OTT SERVER</title>
    <link rel="icon" href="<?=base_url()?>/favicon.ico" type="image/gif">
    <style type="text/css">
        .login {
            margin: 220px auto;
            padding: 10px;
            border: 1px solid #ccc;
            background: lightblue;
        }
        .footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            /* Set the fixed height of the footer here */
            height: 50px;
            line-height: 60px; /* Vertically center the text there */
            /*background-color: black;*/
        }
        .field-icon {
            float: right;
            margin-left: -25px;
            margin-top: -25px;
            margin-right:20px;
            font-size:26px;
            position: relative;
            z-index: 2;
            cursor:pointer;
        }
    </style>
    <link rel="stylesheet" type="text/css" href="<?= base_url('plugin/css/bootstrap.css'); ?>">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <script src="<?= base_url('plugin/js/jquery_slim.js'); ?>" type="text/javascript"></script>
    <script src="<?= base_url('plugin/js/tether.min.js'); ?>" type="text/javascript"></script>
    <script src="<?= base_url('plugin/js/bootstrap.js'); ?>" type="text/javascript"></script>

</head>
<body style="background-color: #E8E6E6">

<header>
    <nav class="navbar navbar-toggleable-md navbar-inverse fixed-top">
        <button class="navbar-toggler navbar-toggler-right" type="button" data-toggle="collapse" data-target="#navbarsExampleDefault" aria-controls="navbarsExampleDefault" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
<!--        <div class="navbar-brand" href="#">-->
<!--            <b style="color: white">ENTERTAINZ<span style="color: green"> MANAGEMENT</b></span>-->
<!--        </div>-->

        <div class="collapse navbar-collapse" id="navbarsExampleDefault">
            <ul class="navbar-nav ml-auto">

                <center><img src="<?= base_url('res/me_logo_fa_3.png'); ?>" class="embed-responsive-item float-left" width="200px" ></center>
            </ul>

        </div>
    </nav>
</header>

<div class="login col-lg-4 col-sm-12 col-md-12" style="background-color: whitesmoke">
    <?=$htmlFlashdata?>
    <div style="border: solid; border-width: 1px; border-color: black;height:250px;">
        <div style="margin-top: 20px">
            <form class="form-horizontal" method="POST" action="<?= base_url('login/login'); ?>">
                <div class="form-group">
                    <label for="emailAdress" class="col-2 control-label"><b>Email/Username</b></label>
                    <div class="col-12">
                        <input type="text" class="form-control" id="username" name="username" placeholder="Email/Username" required>
                    </div>
                </div>
                <div class="form-group">
                    <label for="exampleInputPassword1" class="col-2 control-label"><b>Password</b></label>
                    <div class="col-12">
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <span toggle="#password" class="fa fa-fw fa-eye field-icon toggle-password"></span>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-12 pull-left">
                        <button type="submit" class="btn btn-primary" name="login" style="border-color: #F89938; border-radius:10px; background-color: #F89938;cursor:pointer;">Login</button>
<!--                        <a class="forget-password" style="float:right;cursor:pointer;color:blue;">Forget Password?</a>-->
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<footer class="footer">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-4 col-sm-12 col-xs-12">
            </div>
            <div class="col-lg-4 hidden-xs hidden-sm">
                <p style="text-align: center; color: grey">Copyright &copy Madeira Research Pte Ltd</p>
            </div>
            <div class="col-lg-4 hidden-xs hidden-sm">
            </div>
        </div>
    </div>
</footer>
<div class="modal fade newModal" tabindex="-1" role="dialog" aria-labelledby="newModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg flipInX animated" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">RESET PASSWORD</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <form class="form-horizontal" method="POST" action="<?= base_url('auth/resetpassword'); ?>">
                    <div class="form-group">
                        <label for="emailAdress" class="col-12 control-label"><b>Your Email Account</b></label>
                        <div class="col-12">
                            <input type="email" class="form-control" id="user_name" name="user_name" placeholder="Please Input Your Email" required>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block" name="login" style="border-color: #F89938; border-radius:10px; background-color: #F89938;cursor:pointer;">Reset Password</button>
                </form>
            </div>
        </div>
    </div>
</div>
<script>
    jQuery('document').ready(function()
    {
        jQuery('.forget-password').click( function ()
        {
            jQuery('.newModal').modal();
        });
        jQuery(".toggle-password").click(function()
        {
            jQuery(this).toggleClass("fa-eye fa-eye-slash");
            var input = jQuery(jQuery(this).attr("toggle"));
            if (input.attr("type") == "password")
            {
                input.attr("type", "text");
            }
            else
            {
                input.attr("type", "password");
            }
        });
    });
</script>
</body>
</html>

