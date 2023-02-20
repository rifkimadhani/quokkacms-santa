<?php 
//  $currentctrl = $this->router->fetch_class();
?>
<div class="row">
    <div class="col-md-6">
        <div class="box box-default">
            <div class="box-header with-border">
                <i class="fa fa-warning"></i>
                <h3 class="box-title">LOGIN</h3>
            </div>
            <div class="box-body">
                <form id="changePasswordForm" class="form" method="POST" action="<?= base_url('adminprofile/change_password'); ?>">
                    <div class="form-group">
                        <label class="col-form-label"><b>New Password</b></label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Type Your New Password" required>
                        <span toggle="#password" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                    </div>
                    <div class="form-group">
                        <label class="col-form-label"><b>Retype Password</b></label>
                            <input type="password" class="form-control" id="password_retype" name="password_retype" placeholder="Retype Your New Password" required>
                            <span toggle="#password_retype" class="fa fa-fw fa-eye-slash field-icon toggle-password"></span>
                    </div>
                    <div class="form-group">
                        <div class="col-12">
                            <button type="submit" class="btn btn-primary btn-block" name="login">
                                Change Password
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
<!--    <div class="col-md-6">-->
<!--        <div class="box box-default">-->
<!--            <div class="box-header with-border">-->
<!--                <i class="fa fa-warning"></i>-->
<!--                <h3 class="box-title">PROFILE</h3>-->
<!--            </div>-->
<!--            <div class="box-body">-->
<!--                <form id="changeProfileForm" class="form" method="POST" action="--><?//= base_url('adminprofile/update'); ?><!--">-->
<!--                    <div class="form-group">-->
<!--                            <label class="col-form-label"><b>Full Name</b></label>-->
<!--                            <input type="text" class="form-control" id="name" name="name" placeholder="Username" value="--><?//=$data['username']?><!--" required>-->
<!--                    </div>-->
<!--                    <div class="form-group">-->
<!--                        <div class="col-12">-->
<!--                            <button type="submit" class="btn btn-primary btn-block" name="login">-->
<!--                                Update Profile-->
<!--                            </button>-->
<!--                        </div>-->
<!--                    </div>-->
<!--                </form>-->
<!--            </div>-->
<!--        </div>-->
<!--    </div>-->
</div>

<script>
    jQuery( "#changePasswordForm" ).validate(
    {
        rules: { password: "required",password_retype: {equalTo: "#password"} }
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
</script>




